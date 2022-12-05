# Cloudfront Signer Service for PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/rc4347/cloudfront-signer.svg)](https://packagist.org/packages/rc4347/cloudfront-signer)
[![Total Downloads](https://img.shields.io/packagist/dt/rc4347/cloudfront-signer.svg)](https://packagist.org/packages/rc4347/cloudfront-signer)

This is a simple [PHP](https://php.net/) service for making it easy include the official
[AWS SDK for PHP](https://github.com/aws/aws-sdk-php) in your PHP applications.

It's helps you to sign your files or resources.

Jump To:
* [Getting Started](_#Getting-Started_)
* [More Resources](_#Resources_)

## Getting Started

### Installation
The PHP Service can be installed via [Composer](http://getcomposer.org) by requiring the
`rc4347/cloudfront-signer` package in your project's `composer.json`.

```json
{
    "require": {
        "rc4347/cloudfront-signer": "^0.0.2"
    }
}
```

## Usage
```php
use RC4347\CloudFrontSigner\credentials\AccessConfig;
use RC4347\CloudFrontSigner\credentials\ClientConfig;
use RC4347\CloudFrontSigner\credentials\ExpireConfig;
use RC4347\CloudFrontSigner\SignedUrlService;

# configuration
$client = new ClientConfig('latest', 'default', '<YOUR_S3_REGION>');
$expire = time() + 300; # returns 5 minutes
$access = new AccessConfig('<YOUR_PRIVATE_KEY>', '<YOUR_S3_KEY_PAIR_ID>'); # private key can get file or string 
$resourceKey = 'https://<YOUR_ID>.cloudfront.net/example/example.png';
$config = new ExpireConfig($resourceKey, $expire);
# Execution
/**
* @returns string "https://<YOUR_ID>.cloudfront.net/example/example.png?Expires=<EXPIRES>&Signature=<SIGNATURE>&Key-Pair-Id=<YOUR_S3_KEY_PAIR_ID>"
 */
$urlService = new SignedUrlService($config, $client, $access);
return $urlService->run();
/**
 * if policy param is empty it will be get default policy.
* @returns array|string [
        "CloudFront-Policy": <GENERATED_POLICY>,
        "CloudFront-Signature": <SIGNATURE>,
        "CloudFront-Key-Pair-Id": <YOUR_S3_KEY_PAIR_ID>
    ]
 */
$policy = <<<POLICY
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

$cookieService = new SignedCookieService($config, $client, $access, $policy) # policy is optional
return $cookieService->run();
```

## Resources
For more information:  
* [AWS SDK for PHP on Github](http://github.com/aws/aws-sdk-php/)
* [AWS SDK for PHP website](http://aws.amazon.com/sdkforphp/)