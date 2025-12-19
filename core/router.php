<?php

class Router
{
    const DEFAULT_PAGE = 'login';

    // Main routing function
    public static function routeRequest()
    {
        // Parse the request URI
        list($lang, $route) = self::getLangAndRoute();

        // Redirect to default page if language or route is missing
        if (empty($lang) || empty($route)) 
        {
            if (empty($lang)) $lang = Languages::getDefaultLanguage();
            if (empty($route)) $route = self::DEFAULT_PAGE;

            header("Location: /$lang/$route");
            exit();
        }
        
        // Set the language and request the page
        Languages::setLanguage($lang);
        Controller::requestPage($route);
    }

    /**
     * Extract language and route from the request path
     * @return array [language, route]
     */
    private static function getLangAndRoute() : array
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path_parts = explode('/', trim($path, '/'));

        $lang = "";
        $route = "";
        if (!empty($path_parts))
        {
            // Determine language
            if ( !self::isValidRoute($path_parts[0]) && Languages::isAcceptedLanguage($path_parts[0]) )
                $lang = $path_parts[0];
            
            // Determine route
            if ( self::isValidRoute($path_parts[0]) )
                $route = $path_parts[0];
            else if ( isset($path_parts[1]) && self::isValidRoute($path_parts[1]) )
                $route = $path_parts[1];
        }

        if ( (empty($lang) || empty($route)) && $path !== '/' )  
            Logger::log("Tryied to access: " . $path . "...");

        return array($lang, $route);
    }

    /**
     * Check if the given route corresponds to a valid controller
     * @param string $route The route to check
     * @return bool True if valid, false otherwise
     */
    private static function isValidRoute(string $route) : bool
    {
        if (Languages::isAcceptedLanguage($route))
            return false;

        return file_exists(CONTROLLER_PATH . $route . '_controller.php');
    }
}

// Initialize routing on load
Router::routeRequest();
