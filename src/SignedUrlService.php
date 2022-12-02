<?php

namespace RC4347\CloudFrontSigner;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use RC4347\CloudFrontSigner\base\BaseService;

class SignedUrlService extends BaseService
{
    /**
     * @return string
     */
    public function run(): string
    {
        $cloudFrontClient = $this->getClient();
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