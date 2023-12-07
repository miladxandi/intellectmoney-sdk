<?php

namespace Intellectmoney\Models;

use stdClass;

class Response
{
    public static function mapJsonToObj($json): stdClass
    {
        $data = json_decode($json, true);
        return self::mapArrayToObj($data);
    }

    private static function mapArrayToObj($data) {
        $obj = new stdClass();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $obj->{$key} = self::mapArrayToObj($value);
            } else {
                $obj->{$key} = $value;
            }
        }
        return $obj;
    }
}