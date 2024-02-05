<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RootController extends Controller
{
    const DATA_TOO_LONG = 22001;

    protected static function getErrorMessage(int $errorCode)
    {
        $message = "";
        switch ($errorCode) {
            case self::DATA_TOO_LONG:
                $message = "Data too long";
                break;
            
            default:
                $message ="Unknown error";
                break;
        }

        return $message;
    }
}