<?php

namespace Intellectmoney;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Authenticate extends Configurations
{
    private static string $baseUrl = "https://intellectmoney.ru/ru/";
    public function getUserToken(string $email, string $redirectUrl): void
    {
        $url = self::$baseUrl.'enter/?';
        header("Location: ".$url."returnUrl=".$redirectUrl."?"."&email=".$email);
    }


    public static function generateSignature(array $requestData): string
    {
        foreach ($requestData as &$item) {
            if ($item === null) {
                $item = '';
            }
        }

        $signatureString = implode('::', $requestData);
        return md5(mb_convert_encoding($signatureString, 'UTF-8'));
    }
}