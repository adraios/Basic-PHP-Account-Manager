<?php
class ErrorController extends Controller implements ControllerInterface
{
    public static function render(array $data = array()) : void
    {
        if (!isset($data['code']) || !isset($data['message']))
            Logger::error("(530) Error controller requires 'code' and 'message' data.");

        $code = $data['code'] ?? 530;
        $message = $data['message'] ?? "Internal error: Missing error data.";

        $html_data = [
            'error_message' => $message,
            'default_redirection_time' => getenv('ERROR_REDIRECTION_TIME'),
        ];
        
        $scripts = ['utils/redirect.js'];
        $app_input = [
            'redirect_url' => "/" . Languages::getCurrentLanguage() . '/',
            'countdown' => getenv('ERROR_REDIRECTION_TIME')
        ];          
        $extra = parent::generateExtra($scripts, $app_input);

        parent::buildPage('error', $html_data, $extra);
    }
}