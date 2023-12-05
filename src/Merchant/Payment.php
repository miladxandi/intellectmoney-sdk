<?php

namespace Intellectmoney\Merchant;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Intellectmoney\Authenticate;
use Intellectmoney\Configurations;

class Payment extends Configurations
{
    private Client $client;
    private static string $baseUrl = "https://api.intellectmoney.ru/merchant/";
    public function __construct()
    {
        $this->client = new Client();
    }
    public function getPaymentWays($invoiceId)
    {
        $signature = Authenticate::generateSignature([
            self::$eshopId,
            $invoiceId,
            self::$preferences,
            parent::$secretKey,
        ]);

    }

    /**
     * @throws GuzzleException
     */
    public function createInvoice(
        $orderId,
        $recipientAmount,
        $email,
        $successUrl=null,
        $failUrl=null,
        $backUrl=null,
        $resultUrl=null,
        $recipientCurrency = "TST"
    )
    {
        $signature = Authenticate::generateSignature([
            self::$eshopId,
            $orderId,
            $recipientAmount,
            $recipientCurrency,
            $email,
            $successUrl,
            $failUrl,
            $backUrl,
            $resultUrl,
            self::$preferences,
            parent::$secretKey,
        ]);
        var_dump($signature);

        return $this->client->request('post',self::$baseUrl.'createInvoice',[
            RequestOptions::HEADERS=>parent::$headers,
            RequestOptions::FORM_PARAMS=>[
                'eshopId'=>self::$eshopId,
                'orderId'=>$orderId,
                'recipientAmount'=>$recipientAmount,
                'recipientCurrency'=>$recipientCurrency,
                'email'=>$email,
                'successUrl'=>$successUrl,
                'failUrl'=>$failUrl,
                'backUrl'=>$backUrl,
                'resultUrl'=>$resultUrl,
                'hash'=>$signature,
            ]
        ])->getBody()->getContents();
    }
}