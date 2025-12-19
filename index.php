<?php
try
{
    require_once 'core/autoload.php';
}
catch (Throwable $e)
{
    if (!class_exists('Logger'))
    {
        echo "Critical error!\n";
        exit;
    }

    if (!class_exists('ErrorHandler'))
    {
        Logger::error("Critical error: " . $e->getMessage());
        echo "Critical error!\n";
        exit;
    }

    ErrorHandler::handleThrow($e);
    exit;
}

?>