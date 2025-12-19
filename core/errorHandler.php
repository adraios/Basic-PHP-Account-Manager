<?php
class ErrorHandler
{
    public static function handleThrow(Throwable $e) : void
    {
        // Log the error
        $code = $e->getCode();
        $message = "($code) - " . $e->getMessage();
        $http_code = $code;
        Logger::error($message, $e->getTrace());

        // Handle custom error
        if ($e instanceof Exception) $http_code = 500;
        
        $data = [
            'code' => $code,
            'message' => $message,
        ];
        http_response_code($http_code);
        Controller::requestPage("error", $data);
    }
}