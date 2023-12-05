<?php

namespace Intellectmoney;

abstract  class Configurations
{
    public static string $userToken = "";
    public static string $secretKey = "";
    public static string $bearerToken = "";
    public static int $eshopId;
    public static string $preferences="BankCard";

    protected static array $headers = [
        'Content-Type'=>"application/x-www-form-urlencoded; charset=utf-8'",
        'Accept'=>"text/json",
    ];

}