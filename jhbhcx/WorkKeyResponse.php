<?php

class WorkKeyResponse extends AbstractResponse
{
    public function getDckJson()
    {
        return (string) $this->sXml->Dck;
    }

    public function getDcks()
    {
        $arrDcks = json_decode(strval($this->sXml->Dck), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return [];
        }
        return $arrDcks;
    }

    protected function processEncryptedXml()
    {
        $decryptedXml = '';
        if (preg_match('#(?<=<Dck>).*(?=</Dck>)#msx', $this->encryptedXml, $arrMatches)) {
            $dck = $arrMatches[0];
            if ($dck) {
                $arrWorkKeys = [];
                $len = strlen($dck) / 10;
                $arrDcks = str_split($dck, strlen($dck) / 10); 
                $oCrypto = new Crypto($this->key);
                foreach ($arrDcks as $dck) {
                    $dck = $oCrypto->decrypt($dck, false);
                    $keys = unpack('H32key', $dck);
                    $arrWorkKeys[] = strtoupper($keys['key']);
                }
                $decryptedXml = '<RetCode>00000</RetCode><RetMsg>Ok</RetMsg><Dck>' . json_encode($arrWorkKeys) . '</Dck>';
            }
        }
        return $decryptedXml;
    }
}