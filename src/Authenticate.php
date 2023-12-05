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

    /*
     * requestData = [eshopid,invoice,amount]
     */
    public static function generateSignature(array $requestData): string
    {
        $i = 0;
        $signature = "";
        foreach ($requestData as $item){
            if ($i == 0 && $item!=null){
                $signature .= $item;
            }
            elseif ($i != 0 && $item!=null){
                $signature .="::$item";
            }
            $i++;
        }

        return md5($signature);
    }
}