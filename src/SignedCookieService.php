<?php

namespace RC4347\CloudFrontSigner;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use RC4347\CloudFrontSigner\base\BaseService;
use RC4347\CloudFrontSigner\credentials\AccessConfig;
use RC4347\CloudFrontSigner\credentials\ClientConfig;
use RC4347\CloudFrontSigner\credentials\ExpireConfig;

class SignedCookieService extends BaseService
{

    public ?string $policy;

    public function __construct(ExpireConfig $config, ClientConfig $clientConfig, AccessConfig $accessConfig, ?string $policy = null)
    {
        parent::__construct($config, $clientConfig, $accessConfig);
        $this->policy = $policy;
    }

    /**
     * @return array|string
     */
    public function run()
    {
        $cloudFrontClient = $this->getClient();
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