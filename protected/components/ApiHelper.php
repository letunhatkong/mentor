<?php
class ApiHelper
{
    public static function sendResponse($status = 200, $message = '', $data= '')
    {
        $message = ApiHelper::getStatusCodeMessage($status);
        
        $jsonResponse = new JsonResponse();
        
        $jsonResponse->status = $status;
        $jsonResponse->message = $message;
        $jsonResponse->data = $data;
        
        echo CJSON::encode($jsonResponse);
    }
    
    private static function getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}

class JsonResponse
{
    public $status = "";
    public $message = "";
    public $data = "";
}