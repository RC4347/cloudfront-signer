<?php

namespace RC4347\CloudFrontSigner;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class SignedUrlService extends Model
{
    const DEFAULT_DURATION = 300;
    public string $resourceKey;
    private int $expires;

    /**
     * @throws NotFoundHttpException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->expires = time() + self::DEFAULT_DURATION;
        if (!isset(Yii::$app->extensions['s3']['privateKey'])) {
            throw new NotFoundHttpException("Private Key not found in config extension");
        }
    }

    public function run()
    {
        $cloudFrontClient = new CloudFrontClient([
            'profile' => 'default',
            'version' => 'latest',
            'region' => env('S3_REGION')
        ]);

        return $this->getSignedUrl($cloudFrontClient);
    }

    protected function getSignedUrl($cloudFrontClient)
    {
        try {
            return $cloudFrontClient->getSignedUrl([
                'url' => $this->resourceKey,
                'expires' => $this->expires,
                'private_key' => Yii::$app->extensions['s3']['privateKey'],
                'key_pair_id' => env('S3_KEY_PAIR_ID')
            ]);

        } catch (AwsException $e) {
            return 'Error: ' . $e->getAwsErrorMessage();
        }
    }

}