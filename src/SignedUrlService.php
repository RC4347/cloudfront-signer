<?php

namespace RC4347\CloudFrontSigner;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use RC4347\CloudFrontSigner\credentials\AccessConfig;
use RC4347\CloudFrontSigner\credentials\ClientConfig;
use RC4347\CloudFrontSigner\credentials\ExpireConfig;

class SignedUrlService
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

    /**
     * @return string
     */
    public function run(): string
    {
        $cloudFrontClient = new CloudFrontClient([
            'profile' => $this->clientConfig->profile ?? 'default',
            'version' => $this->clientConfig->version ?? 'latest',
            'region' => $this->clientConfig->region
        ]);

        return $this->getSignedUrl($cloudFrontClient);
    }

    /**
     * @param CloudFrontClient $cloudFrontClient
     * @return string
     */
    protected function getSignedUrl(CloudFrontClient $cloudFrontClient): string
    {
        try {
            return $cloudFrontClient->getSignedUrl([
                'url' => $this->config->resourceKey,
                'expires' => $this->config->expires,
                'private_key' => $this->accessConfig->privateKey,
                'key_pair_id' => $this->accessConfig->keyPairId
            ]);

        } catch (AwsException $e) {
            return 'Error: ' . $e->getAwsErrorMessage();
        }
    }

}