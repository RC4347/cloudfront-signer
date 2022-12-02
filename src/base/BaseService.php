<?php

namespace RC4347\CloudFrontSigner\base;

use Aws\CloudFront\CloudFrontClient;
use RC4347\CloudFrontSigner\credentials\AccessConfig;
use RC4347\CloudFrontSigner\credentials\ClientConfig;
use RC4347\CloudFrontSigner\credentials\ExpireConfig;

abstract class BaseService
{
    public ExpireConfig $config;
    public ClientConfig $clientConfig;
    public AccessConfig $accessConfig;

    /**
     * @param ExpireConfig $config
     * @param ClientConfig $clientConfig
     * @param AccessConfig $accessConfig
     */
    public function __construct(ExpireConfig $config, ClientConfig $clientConfig, AccessConfig $accessConfig)
    {
        $this->config = $config;
        $this->clientConfig = $clientConfig;
        $this->accessConfig = $accessConfig;
    }

    public function getClient()
    {
        return new CloudFrontClient([
            'profile' => $this->clientConfig->profile ?? 'default',
            'version' => $this->clientConfig->version ?? 'latest',
            'region' => $this->clientConfig->region
        ]);
    }

    public abstract function run();
}