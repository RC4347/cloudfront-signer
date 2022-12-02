<?php

namespace RC4347\CloudFrontSigner\credentials;

class AccessConfig
{
    public $privateKey;
    public $keyPairId;

    /**
     * @param $privateKey
     * @param $keyPairId
     */
    public function __construct($privateKey, $keyPairId)
    {
        $this->privateKey = $privateKey;
        $this->keyPairId = $keyPairId;
    }

}