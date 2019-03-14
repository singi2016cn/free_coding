<?php

abstract class AbstractRequest 
{
    abstract protected function getTxCode();
    abstract protected function build();
    
    protected $doEncrypt = true;
    private $host, $port, $orgCode, $keys, $traceNo;
    private $errno, $errstr;
    
    public function __construct($host, $port, $orgCode, $keys)
    {
        if ($this->doEncrypt && !$keys) {
            throw new Exception('Key must be set.');
        }
        
        if (empty($orgCode) || empty($host)) {
            throw new Exception('orgCode/host must be set.');
        }
        
        if (is_string($keys) && strlen($keys) == 32) {
            $this->keys = $keys;
        } elseif ($keys) {
            $arrKeys = json_decode($keys, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new Exception('Key must be set.');
            }
            $this->keys = $arrKeys;
        }
        
        $this->orgCode = $orgCode;
        $this->host = $host;
        $this->port = $port;
        $this->setTraceNo();

        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    private function setTraceNo()
    {
        $this->traceNo  = date('ymdHis') . $this->getTxCode() . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        return $this;
    }
    
    public function getTraceNo()
    {
        return $this->traceNo;
    }
    
    public function send()
    {
        if (!$this->validate()) {
            $logText = "检查组包数据校验失败，请检查错误原因：[{$this->getError()}]{$this->getErrStr()}";
            Debugger::println($logText, Debugger::LEVEL_ERROR, "XMLBuild");
            Logger::error($logText, 'XMLBuild');
            $this->setError(1104, $this->getErrStr());
            return false;
        }
        $xml = $this->buildXml($originXml);
        $xml = str_pad(strlen($xml), 6, '0' , STR_PAD_LEFT) . $xml;
        
        Debugger::println($xml, Debugger::LEVEL_INFO, 'BeforeSent');

        $fp = @fsockopen($this->host, $this->port, $errno, $errstr, 5);
        if (!$fp) {
            $logText = sprintf("无法连接到服务器，请检查通道！错误原因: %s, 错误码：%d", mb_convert_encoding($errstr, 'utf-8', 'gbk'), $errno);
            Debugger::println($logText, Debugger::LEVEL_ERROR, 'Socket');
            Logger::error($logText, $this->getTxCode());
            $this->setError(1100, $logText);
            return false;
        }
        
        $putLen = fputs($fp, $xml);
        $targetLen = strlen($xml);
        if ($putLen != $targetLen) {
            $logText = "发送XML失败，发送长度：$putLen，目标长度：$targetLen！关闭连接！";
            Debugger::println($logText, Debugger::LEVEL_ERROR, 'Socket');
            Logger::error($logText);
            fclose($fp);
            $this->setError(1101, $logText);
            return false;
        }
        $responseLen = intval(fread($fp, 6));
        Debugger::println($responseLen, Debugger::LEVEL_DEBUG, 'ResponseLength');
        $response = null;
        if ($responseLen > 0) {
            $response = fread($fp, $responseLen);
        }
        fclose($fp);

        if (!$response) {
            $logText = "无法正常读取到应答消息，请确认通道是否正常！";
            Debugger::println($logText, Debugger::LEVEL_ERROR, 'Socket');
            Logger::error($logText, $this->getTxCode());
            $this->setError(1102, $logText);
            return false;
        }
        $response = mb_convert_encoding($response, 'utf-8', 'gbk');
        Debugger::println($response, Debugger::LEVEL_INFO, 'Response');
        Logger::info($response, $this->getTxCode());

        try {
            $oResponse = Response::factory($response, $this->keys);
            $logText = "[TxCode]{$this->getTxCode()}\n" .
                "[Request]{$xml}\n" .
                "[RequestOriginXML]{$originXml}\n" .
                "[ResponseLength]{$responseLen}\n" .
                "[ResponseEncrypted]{$oResponse->getEncryptedXml()}\n" .
                "[ResponseDecrypted]{$oResponse->getDecryptedXml()}";
            Debugger::println($logText, Debugger::LEVEL_DEBUG, 'Debug');
            Logger::info($logText, $this->getTxCode());
            return $oResponse;
        } catch (\Exception $e) {
            Debugger::println($e->getMessage(), Debugger::LEVEL_ERROR, 'XMLParsing');
            Logger::error($e->getMessage(), $this->getTxCode());
            $this->setError(1103, $e->getMessage());
            return false;
        }
    }

    protected function setError($errno, $errstr = '')
    {
        $this->errno  = $errno;
        $this->errstr = $errstr;
        return $this;
    }

    public function getError()
    {
        return $this->errno;
    }

    public function getErrStr()
    {
        return $this->errstr;
    }
    
    private function buildXml(&$originXml)
    {
        list($date, $time) = explode('-', date('Ymd-His'));
        $originXml = $body = sprintf('<TxCode>%s</TxCode><TxDate>%s</TxDate><TxTime>%s</TxTime><TraceNo>%s</TraceNo>%s', $this->getTxCode(), $date, $time, $this->getTraceNo(), $this->build());
        Debugger::println($body, Debugger::LEVEL_DEBUG, 'BeforeEncrypted');

        // $this->doEncrypt = false;
        if (!$this->doEncrypt) {
            $xml = '<?xml version="1.0" encoding="GBK"?><Root><Head><TxCode>%s</TxCode><OrgNo>%s</OrgNo><TraceNo>%s</TraceNo><SignData>%s</SignData></Head><Req>%s</Req></Root>';
            return sprintf($xml, $this->getTxCode(), $this->orgCode, $this->getTraceNo(), '', $body);
        }
        
        $secNo = rand(0, 9);
        $key = $this->keys[$secNo];
        $oCrypto = new Crypto($key);
        $body = $oCrypto->encrypt($body);

        $xml = '<?xml version="1.0" encoding="GBK"?><Root><Head><TxCode>%s</TxCode><OrgNo>%s</OrgNo><TraceNo>%s</TraceNo><SecNo>%02d</SecNo><SignData>%s</SignData></Head><Req>%s</Req></Root>';
        return sprintf($xml, $this->getTxCode(), $this->orgCode, $this->getTraceNo(), $secNo + 1, '', $body);
    }

    protected function validate()
    {
        return true;
    }

    protected function isValidLicense($license)
    {
        $licenseValidator = new LicenseValidator();
        return $licenseValidator->validate($license, $error);
    }

    protected function isValidMobile($mobile)
    {
        $mobileValidator = new MobileValidator();
        return $mobileValidator->validate($mobile, $error);
    }
}
