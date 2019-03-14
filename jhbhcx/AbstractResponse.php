<?php

abstract class AbstractResponse 
{
	const ACCTYPE_CREDIT = 0;
	const ACCTYPE_DEBIT  = 1;

    private static $_arrAccTypes = [
    	self::ACCTYPE_CREDIT => '贷记卡',
    	self::ACCTYPE_DEBIT  => '借记卡',
    ];

    public function getAccTypes()
    {
        return self::$_arrAccTypes;
    }

    public function isDebitCard($accType)
    {
        return $accType == self::ACCTYPE_DEBIT;
    }

    public function descAccType($accType)
    {
        return isset(self::$_arrAccTypes[$accType]) ? self::$_arrAccTypes[$accType] : null;
    }

    protected $encryptedXml, $decryptedXml;
    protected $sXml;
	protected $key;
    private $spXml;
	
	public function __construct($key, $sXml)
    {
		$this->key = $key;
        $this->setEncryptedXml($sXml)->parseXml();
    }

    public function getResponseXml()
    {
        if ($this->spXml) {
            return $this->spXml->asXML();
        }
        return null;
    }
    
    public function getEncryptedXml()
    {
        return $this->encryptedXml;
    }
    
    /**
     * 设置密文
     * 兼容SimpleXMLElement对象及加密的字符串作为参数
     * 如果为SimpleXMLElement对象时，将可以通过Response对象获得整个XML文档以及查询TxCode/OrgNo信息
     *
     * @param SimpleXMLElement|string $sXml
     * @return this
     */
    private function setEncryptedXml($sXml)
    {
        if ($sXml instanceof SimpleXMLElement) {
            $this->spXml = $sXml;
            $respBody = $sXml->Resp->asXML();

            if (preg_match('#(?<=<Resp>).*(?=</Resp>)#msx', $respBody, $arrMatches)) {
                $respBody = $arrMatches[0];
            } else {
                throw new Exception("Invalid Response Node");
            }
        } elseif (is_string($sXml)) {
            $respBody = $sXml;
        } else {
            throw Exception('Unsupported argument type');
        }

        $this->encryptedXml = $respBody;
        return $this;
    }
    
    public function getDecryptedXml()
    {
        return $this->decryptedXml;
    }
    
    private function setDecryptedXml($decryptedXml)
    {
        $this->decryptedXml = $decryptedXml;
        return $this;
    }
    
    public function getXmlObject()
    {
        return $this->sXml;
    }
    
    public function hasErrors()
    {
        return strval($this->sXml->RetCode) === '00000' ? false : true;
    }
    
    public function getCode()
    {
        return strval($this->sXml->RetCode);
    }
    
    public function getMsg()
    {
        return strval($this->sXml->RetMsg);
    }

    public function getTxCode()
    {
        if ($this->spXml) {
            return strval($this->spXml->Head->TxCode);
        }
        return null;
    }

    public function getOrgNo()
    {
        if ($this->spXml) {
            return strval($this->spXml->Head->OrgNo);
        }
        return null;
    }
    
    public function getTxDate()
    {
        return strval($this->sXml->TxDate);
    }
    
    public function getTxTime()
    {
        return strval($this->sXml->TxTime);
    }
    
    public function getTraceNo()
    {
        return strval($this->sXml->TraceNo);
    }

    protected function processEncryptedXml()
    {
        $decryptedXml = '';
        if ($this->encryptedXml) {
            if ($this->key && !preg_match('#<RetCode>[^<]*</RetCode>#', $this->encryptedXml)) {
                $oCrypto = new Crypto($this->key);
                $decryptedXml =  mb_convert_encoding($oCrypto->decrypt($this->encryptedXml), 'utf-8', 'gbk');
            } else {
                $decryptedXml = $this->encryptedXml;
            }
        }
        if ($decryptedXml == '') {
            $decryptedXml = '<RetCode>999999</RetCode><RetMsg>Error!</RetMsg>';
        }
        return $decryptedXml;
    }

    private function parseXml()
    {
        $decryptedXml = $this->processEncryptedXml();
        $this->setDecryptedXml($decryptedXml);

        $decryptedXml = '<Resp>' . $decryptedXml . '</Resp>';
        $this->sXml = simplexml_load_string($decryptedXml, 'SimpleXMLElement', LIBXML_NOCDATA);
    }
}