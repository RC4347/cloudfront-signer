<?php

namespace RC4347\CloudFrontSigner\credentials;

class ClientConfig
{
    public string $version;
    public string $profile;
    public string $region;

    public function __construct(string $version, string $profile, string $region)
    {
        $this->version = $version;
        $this->profile = $profile;
        $this->region = $region;
    }
}