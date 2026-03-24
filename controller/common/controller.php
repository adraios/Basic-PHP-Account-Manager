<?php

require_once 'controller_interface.php';

class Controller
{
    const VIEW_PATH = BASE_PATH . 'view/';
    const CONTROLLER_SUFIX = '_controller.php';

    public static function requestPage(string $controller, array $data = array()) : void
    {
        if (!file_exists(CONTROLLER_PATH . $controller . self::CONTROLLER_SUFIX))
            throw new Exception("Controller not found: " . $controller, 523);

        require_once CONTROLLER_PATH . $controller . self::CONTROLLER_SUFIX;
        $controller_class = str_replace('/', '', ucwords($controller, '/')) . 'Controller';
        if (!class_exists($controller_class))
            throw new Exception("Controller class not found: " . $controller_class, 524);
        
        // Call the render method
        $controller_class::render($data);

        // Clean DB connection
        DB::destroyDB();
    }

    protected static function buildPage(string $controller_path, array $data = array(), array $extra = array()) : void
    {
        // Check if controller exists
        if (!file_exists(self::VIEW_PATH . $controller_path . '_view.html')) {
            throw new Exception("Page not found: " . $controller_path, 400);
        }

        ob_start();
        include self::VIEW_PATH . $controller_path . '_view.html';
        $content = ob_get_clean();
        if ($content === false)
            throw new Exception("Failed to read page: " . $controller_path, 522);

        if (empty($extra)) $extra = self::generateExtra();

        // Load Head and Foot content
        $head_content = self::loadHead($controller_path, isset($extra['head']) ? $extra['head'] : []);
        $foot_content = self::loadFoot(isset($extra['foot']) ? $extra['foot'] : []);

        // Prepare page content
        $content = self::parseVariable($content, $data);

        // Output the final page
        echo $head_content . $content . $foot_content;
    }

    private static function loadHead(string $controller_path, array $extra) : string
    {
        ob_start();
        include self::VIEW_PATH . '/common/head.html';
        $content = ob_get_clean();
        if ($content === false)
            throw new Exception("Failed to read head page component", 520);

        $data = [
            'lang' => Languages::getCurrentLanguage(),
            'charset' => getenv('ENCODE'),
            'title' => 'Basic PHP Account Manager - ' . ucfirst(basename($controller_path))
        ];

        foreach ($extra as $key => $value)
        {
            // Special handling for extra scripts and app input
            if ($key === 'extra_scripts' && is_array($value))
            {
                $scripts_html = '';
                foreach ($value as $script)
                {
                    $scripts_html .= '<script src="/js/' . $script . '" defer></script>' . "\n";
                }
                $data[$key] = $scripts_html;
                continue;
            }
            else if ($key === 'app_input' && is_array($value))
            {
                $data[$key] = addslashes(json_encode($value));
                continue;
            }

            $data[$key] = $value;
        }

        return self::parseVariable($content, $data);
    }

    private static function loadFoot(array $extra) : string
    {
        ob_start();
        include self::VIEW_PATH . '/common/foot.html';
        $content = ob_get_clean();
        if ($content === false)
            throw new Exception("Failed to read foot page component", 521);

        $data = [];

        foreach ($extra as $key => $value)
        {
            $data[$key] = $value;
        }

        return self::parseVariable($content, $data);
    }

    private static function parseVariable(string $content, array $data) : string
    {
        $output = '';
        $lenght = mb_strlen($content, getenv('ENCODE'));

        $i = 0;
        while($i < $lenght)
        {
            // Detect variable start
            if ($i + 1 < $lenght && $content[$i] === '{' && $content[$i + 1] === '{')
            {
                $i += 2;
                $var_name = '';

                // Extract variable name
                while ( $i + 1 < $lenght && !($content[$i] === '}' && $content[$i + 1] === '}') )
                {
                    $var_name .= $content[$i];
                    $i++;
                }
                $var_name = trim($var_name);

                // Replace variable if exists
                if (array_key_exists($var_name, $data))
                {
                    $output .= $data[$var_name];
                }
                else if ( mbStartsWith($var_name, "str.", getenv('ENCODE')) )
                {
                    $loc_key = substr($var_name, 4);
                    $output .= Languages::getString($loc_key);
                }
                else
                {
                    $output .= '{{' . $var_name . '}}';
                }

                // Move index past the variable
                $i += 2;
            }
            else
            {
                $output .= $content[$i];
                $i++;
            }
        }

        return $output;
    }

    protected static function generateExtra(array $scripts = array(), array $app_input = array()) : array
    {
        $extra = [
            'head' => [
                 'extra_scripts' => $scripts,
                'app_input' => $app_input
            ],
            'foot' => [
                
            ]
        ];

        return $extra;
    }
}