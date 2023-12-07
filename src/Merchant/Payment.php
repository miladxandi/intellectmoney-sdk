<?php

namespace Intellectmoney\Merchant;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Intellectmoney\Authenticate;
use Intellectmoney\Configurations;
use Intellectmoney\Models\Response;

class Payment extends Configurations
{
    private Client $client;
    private static string $baseUrl = "https://api.intellectmoney.ru/merchant/";
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function createInvoice(
        $orderId,
        $recipientAmount,
        $email,
        $recipientCurrency = "TST",
        $successUrl=null,
        $failUrl=null,
        $backUrl=null,
        $resultUrl=null,
        $userName=null,
        $serviceName=null,
        $expireDate=null,
        $holdModel=null,
        $purchaseHash=null
    ): \stdClass
    {
        try {
            $signature = Authenticate::generateSignature([
                parent::$eshopId,
                $orderId,
                $serviceName,
                $recipientAmount,
                $recipientCurrency,
                $userName,
                $email,
                $successUrl,
                $failUrl,
                $backUrl,
                $resultUrl,
                $expireDate,
                $holdModel,
                '',
                parent::$secretKey,
            ]);

            $response = Response::mapJsonToObj($this->client->request('post',self::$baseUrl.'createInvoice',[
                RequestOptions::HEADERS=>parent::$headers,
                RequestOptions::FORM_PARAMS=>[
                    'eshopId'=>parent::$eshopId,
                    'orderId'=>$orderId,
                    'serviceName'=>$serviceName,
                    'recipientAmount'=>$recipientAmount,
                    'recipientCurrency'=>$recipientCurrency,
                    'userName'=>$userName,
                    'email'=>$email,
                    'successUrl'=>$successUrl,
                    'failUrl'=>$failUrl,
                    'backUrl'=>$backUrl,
                    'resultUrl'=>$resultUrl,
                    'expireDate'=>$expireDate,
                    'holdModel'=>$holdModel,
                    'purchaseHash'=>$purchaseHash,
                    'hash'=>$signature,
                ]
            ])->getBody()->getContents());

            if ($response->Result->InvoiceId)
                return Response::mapJsonToObj(json_encode(['Result'=>['InvoiceId'=>$response->Result->InvoiceId],'Error'=>['Code'=>null,'Description'=>null]]));
            else
                return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$response->Result->State->Code,'Description'=>$response->Result->State->Desc]]));

        }catch (ClientException $exception){
            return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$exception->getCode(),'Description'=>$exception->getResponse()->getBody()]]));
        }
    }

    public function bankCardPayment(
        $invoiceId,
        $pan,
        $expiredMonth,
        $expiredYear,
        $returnUrl,
        $ipAddress,
        $cardHolder=null,
        $cvv = null,
    ): \stdClass
    {
        try {
            $signature = Authenticate::generateSignature([
                parent::$eshopId,
                $invoiceId,
                $pan,
                $cardHolder,
                $expiredMonth,
                $expiredYear,
                $cvv,
                $returnUrl,
                $ipAddress,
                parent::$secretKey,
            ]);

            $response = Response::mapJsonToObj($this->client->request('post',self::$baseUrl.'bankcardpayment',[
                RequestOptions::HEADERS=>parent::$headers,
                RequestOptions::FORM_PARAMS=>[
                    'eshopId'=>parent::$eshopId,
                    'invoiceId'=>$invoiceId,
                    'pan'=>$pan,
                    'cardHolder'=>$cardHolder,
                    'expiredMonth'=>$expiredMonth,
                    'expiredYear'=>$expiredYear,
                    'cvv'=>$cvv,
                    'returnUrl'=>$returnUrl,
                    'ipAddress'=>$ipAddress,
                    'hash'=>$signature,
                ]
            ])->getBody()->getContents());

            if ($response->Result->State->Code==0)
                return Response::mapJsonToObj(json_encode(['Result'=>['Code'=>$response->Result->State->Code,'Description'=>$response->Result->State->Desc],'Error'=>['Code'=>null,'Description'=>null]]));
            else
                return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$response->Result->State->Code,'Description'=>$response->Result->State->Desc]]));

        }catch (\Exception $exception){
            return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$exception->getCode(),'Description'=>$exception->getMessage()]]));
        }
    }

    public function pay(
        $invoiceId,
    ): \stdClass
    {
        try {

            return Response::mapJsonToObj(json_encode(['Result'=>['PaymentLink'=>"https://merchant.intellectmoney.ru/?invoiceId=".$invoiceId,'Description'=>"Redirect user to this link"],'Error'=>['Code'=>null,'Description'=>null]]));

        }catch (ClientException $exception){
            return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$exception->getCode(),'Description'=>$exception->getResponse()->getBody()]]));
        }
    }
    public function activationPay(
        $invoiceId,
        $activationAmount,
        $cvv = null,
    ): \stdClass
    {
        try {
            $signature = Authenticate::generateSignature([
                parent::$eshopId,
                $invoiceId,
                $activationAmount,
                $cvv,
                parent::$secretKey,
            ]);

            $response = Response::mapJsonToObj($this->client->request('post',self::$baseUrl.'activationpay',[
                RequestOptions::HEADERS=>parent::$headers,
                RequestOptions::FORM_PARAMS=>[
                    'eshopId'=>parent::$eshopId,
                    'invoiceId'=>$invoiceId,
                    'cvv'=>$cvv,
                    'activationAmount'=>$activationAmount,
                    'hash'=>$signature,
                ]
            ])->getBody()->getContents());

            if ($response->Result->State->Code==0)
                return Response::mapJsonToObj(json_encode(['Result'=>['Code'=>$response->Result->State->Code,'Description'=>$response->Result->State->Desc],'Error'=>['Code'=>null,'Description'=>null]]));
            else
                return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$response->Result->State->Code,'Description'=>$response->Result->State->Desc]]));

        }catch (ClientException $exception){
            return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$exception->getCode(),'Description'=>$exception->getResponse()->getBody()]]));
        }
    }

    public function getBankCardPaymentState(
        $invoiceId,
    ): \stdClass
    {
        try {
            $signature = Authenticate::generateSignature([
                parent::$eshopId,
                $invoiceId,
                parent::$secretKey,
            ]);

            $response = Response::mapJsonToObj($this->client->request('post',self::$baseUrl.'getbankcardpaymentstate',[
                RequestOptions::HEADERS=>parent::$headers,
                RequestOptions::FORM_PARAMS=>[
                    'eshopId'=>parent::$eshopId,
                    'invoiceId'=>$invoiceId,
                    'hash'=>$signature,
                ]
            ])->getBody()->getContents());

            if ($response->Result->State->Code==0)
                return Response::mapJsonToObj(json_encode(['Result'=>['Code'=>$response->Result->State->Code,'Description'=>$response->Result->State->Desc],'Error'=>['Code'=>null,'Description'=>null]]));
            else
                return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$response->Result->State->Code,'Description'=>$response->Result->State->Desc]]));

        }catch (ClientException $exception){
            return Response::mapJsonToObj(json_encode(['Result'=>null,'Error'=>['Code'=>$exception->getCode(),'Description'=>$exception->getResponse()->getBody()]]));
        }
    }
}