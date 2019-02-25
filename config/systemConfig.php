<?php

class systemConfig
{
    var $dbConnect;
    const _WEBSITE_ = "http://localhost:8888/api/";
    const _HOST_  = "localhost";
    const _DB_ = "db_api";
    const _USER_ = "root";
    const _PASS_ = "root";

    function __construct()
    {

    }

    function __destruct()
    {
        $this->dbConnect->close();
    }

    function connectDB()
    {
        $this->dbConnect = new mysqli(self::_HOST_ , self::_USER_, self::_PASS_, self::_DB_);
        if ($this->dbConnect->connect_errno) {
            return null;
        } else {
            return $this->dbConnect;
        }
    }

    function getWebsite(){
        return self::_WEBSITE_;
    }

    function getStatusCodeMessage($status)
    {
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    function sendResponse($status = 200, $body = '', $content_type = 'application/json')
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->getStatusCodeMessage($status);
        header($status_header);
        header('Content-type: ' . $content_type);
        echo $body;
    }

    function error($message, $status= 200){
        $this->sendResponse($status,'{"success": "false" ,"message":"'.$message.'"}');
    }

    function success($data, $status = 200){
        $this->sendResponse($status,'{"success": "true" ,"result":'.$data.'}');
    }
}



?>