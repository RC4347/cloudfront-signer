<?php

namespace RC4347\CloudFrontSigner;

use Yii;
use yii\base\Model;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use yii\web\NotFoundHttpException;

class SignedCookieService extends Model
{
    const DEFAULT_DURATION = 300;
    public string $resourceKey;
    private int $expires;
    private string $url;

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

        return $this->getSignedCookie($cloudFrontClient);
    }

    protected function getSignedCookie($cloudFrontClient)
    {
        $this->url = $this->generateUrl($this->resourceKey);

        try {
            return $cloudFrontClient->getSignedCookie([
                'policy' => $this->generatePolicy(),
                'private_key' => Yii::$app->extensions['s3']['privateKey'],
                'key_pair_id' => env('S3_KEY_PAIR_ID')
            ]);
        } catch (AwsException $e) {
            return 'Error : ' . $e->getAwsErrorMessage();
        }
    }

    protected function generateUrl($resourceKey)
    {
        $splited = explode('/',$resourceKey);
        $removeKey = count($splited) - 1;
        unset($splited[$removeKey]);
        return implode('/', $splited) . '/*';
    }

    protected function generatePolicy()
    {
        return <<<POLICY
        {
            "Statement": [
                {
                    "Resource": "{$this->url}",
                    "Condition": {
                        "DateLessThan": {"AWS:EpochTime": {$this->expires}}
                    }
                }
            ]
        }
        POLICY;
    }
}