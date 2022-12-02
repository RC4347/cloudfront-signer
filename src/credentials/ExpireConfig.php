<?php

namespace RC4347\CloudFrontSigner\credentials;

class ExpireConfig
{
    public string $resourceKey;
    public int $expires;

    /**
     * @param string $resourceKey
     * @param int $expires
     */
    public function __construct(string $resourceKey, int $expires)
    {
        $this->resourceKey = $resourceKey;
        $this->expires = $expires;
    }

}