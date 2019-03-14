<?php

class WorkKeyRequest extends AbstractRequest
{
    protected $doEncrypt = false;
    
    public function getTxCode()
    {
        return 'Z001';
    }
    
    public function build()
    {
        return '';
    }
}