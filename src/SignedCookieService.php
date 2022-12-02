<?php

namespace RC4347\CloudFrontSigner;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use RC4347\CloudFrontSigner\credentials\AccessConfig;
use RC4347\CloudFrontSigner\credentials\ClientConfig;
use RC4347\CloudFrontSigner\credentials\ExpireConfig;

class SignedCookieService
{

    public ?string $policy;

    public ClientConfig $clientConfig;
    public AccessConfig $accessConfig;
    public ExpireConfig $config;

    /**
     * @param string|null $policy
     * @param ClientConfig $clientConfig
     * @param AccessConfig $accessConfig
     * @param ExpireConfig $config
     */
    public function __construct(ClientConfig $clientConfig, AccessConfig $accessConfig, ExpireConfig $config, ?string $policy = null)
    {
        $this->policy = $policy;
        $this->clientConfig = $clientConfig;
        $this->accessConfig = $accessConfig;
        $this->config = $config;
    }

    /**
     * @return array|string
     */
    public function run()
    {
        $cloudFrontClient = new CloudFrontClient([
            'profile' => $this->clientConfig->profile ?? 'default',
            'version' => $this->clientConfig->version ?? 'latest',
            'region' => $this->clientConfig->region
        ]);

        return $this->getSignedCookie($cloudFrontClient);
    }

    /**
     * @param CloudFrontClient $cloudFrontClient
     * @return array|string
     */
    protected function getSignedCookie(CloudFrontClient $cloudFrontClient)
    {
        try {
            return $cloudFrontClient->getSignedCookie([
                'policy' => $this->policy ?? $this->defaultPolicy(),
                'private_key' => $this->accessConfig->privateKey,
                'key_pair_id' => $this->accessConfig->keyPairId
            ]);
        } catch (AwsException $e) {
            return 'Error : ' . $e->getAwsErrorMessage();
        }
    }

    /**
     * @return string
     */
    protected function defaultPolicy(): string
    {
        return <<<POLICY
        {
            "Statement": [
                {
                    "Resource": "{$this->config->resourceKey}",
                    "Condition": {
                        "DateLessThan": {"AWS:EpochTime": {$this->config->expires}}
                    }
                }
            ]
        }
        POLICY;
    }
}