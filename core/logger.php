<?php
enum LogType : string
{
    case DEBUG = 'DEBUG';
    case INFO = 'INFO';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
}

class Logger
{
    const LOG_PATH = BASE_PATH . 'logs/';

    /**
     * Logs a debug message.
     * @param string $message The debug message to log.
     */
    static function debug(string $message) : void
    {
        if (getenv('APP_ENV') != ENVIRONMENT::DEVELOPMENT->value) return;

        $content = self::formatLogMessage(LogType::DEBUG, $message);
        self::writeToLogFile($content, self::getLogName());
    }

    /**
     * Logs an informational message.
     * @param string $message The message to log.
     */
    static function log(string $message) : void
    {
        $content = self::formatLogMessage(LogType::INFO, $message);
        self::writeToLogFile($content, self::getLogName());
    }

    /**
     * Logs a warning message.
     * @param string $message The warning message to log.
     */
    static function warning(string $message) : void
    {
        $content = self::formatLogMessage(LogType::WARNING, $message);
        self::writeToLogFile($content, self::getLogName());
        trigger_error($message, E_USER_WARNING);
    }

    /**
     * Logs an error message.
     * @param string $message The error message to log.
     */
    static function error(string $message, ?array $backtrace = null) : void
    {
        $content = self::formatLogMessage(LogType::ERROR, $message, $backtrace);
        self::writeToLogFile($content, self::getLogName());
        self::writeToLogFile($content, self::getLogName(error: true));
    }

    /**
     * Formats a log message with timestamp, type, process ID, and backtrace.
     * @param LogType $type The type of log message.
     * @param string $message The log message content.
     * @return string The formatted log message.
     */
    private static function formatLogMessage(LogType $type, string $message, ?array $backtrace = null) : string
    {
        // Prepare log message
        $date = date('Y-m-d H:i:s');
        $pid = colorString(StringColor::BLUE, (string)getmypid());
        $type_text = colorString(self::getTypeColor($type), $type->value, StringStyle::BOLD);
        $content = "[$date] [$type_text] [$pid] $message\n";

        // Get default backtrace and it's ordered like an error backtrace
        if (is_null($backtrace))
            $backtrace = array_reverse(debug_backtrace());

        // Append backtrace information
        $traceLines = [];

        foreach ($backtrace as $i => $line)
        {
            $file   = colorString(StringColor::CYAN, $line['file'] ?? '[internal]');
            $line   = colorString(StringColor::MAGENTA, $line['line'] ?? 0);
            $traceLines[] = "#$i $file ($line)";
        }

        $content .= implode("\n", $traceLines);
        $content .= "\n\n";

        return $content;
    }

    /**
     * Writes the log message to the specified log file.
     * @param string $message The log message to write.
     * @param string $filename The name of the log file.
     */
    private static function writeToLogFile(string $message, string $filename) : void
    {
        // Write to log file
        file_put_contents(self::LOG_PATH . $filename, $message, FILE_APPEND);
    }

    /**
     * Gets the color associated with a log type.
     * @param LogType $type The type of log message.
     * @return StringColor The color corresponding to the log type.
     */
    private static function getTypeColor(LogType $type) : StringColor
    {
        return match ($type) {
            LogType::DEBUG => StringColor::WHITE,
            LogType::INFO => StringColor::GREEN,
            LogType::WARNING => StringColor::YELLOW,
            LogType::ERROR => StringColor::RED,
        };
    }

    /**
     * Generates a log file name based on the current date.
     * @param bool $error Indicates whether the log is for errors.
     * @return string The generated log file name.
     */
    private static function getLogName(bool $error = false) : string
    {
        $date = date('Y-m-d');
        
        if ($error) return "error_$date.log";
        
        return "$date.log";
    }
}