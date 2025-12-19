<?php
/**
 * The function returns {@see true} if the passed $haystack starts from the $needle string or {@see false} otherwise.
 * @param string $haystack The string to search in.
 * @param string $needle The substring to search for at the start of $haystack.
 * @param string $encoding The character encoding (default is 'UTF-8').
 * @return bool {@see true} if $haystack starts with $needle, {@see false} otherwise.
 */
function mbStartsWith(string $haystack, string $needle, string $encoding = 'UTF-8'): bool
{
    return mb_substr($haystack, 0, mb_strlen($needle, $encoding), $encoding) === $needle;
}

enum StringColor : string
{
    case BLACK = '30';
    case RED = '31';
    case GREEN = '32';
    case YELLOW = '33';
    case BLUE = '34';
    case MAGENTA = '35';
    case CYAN = '36';
    case WHITE = '37';
}

enum StringStyle : string
{
    case RESET = '0';
    case BOLD = '1';
    case UNDERLINE = '4';
    case BLINK = '5';
    case REVERSED = '7';
}

/**
 * Colors a string with the specified color and style for terminal output.
 * @param StringColor $color The color to apply.
 * @param string $text The text to color.
 * @param StringStyle $style The style to apply (default is RESET).
 * @return string The colored string.
 */
function colorString(StringColor $color, string $text, StringStyle $style = StringStyle::RESET): string
{
    return "\033[" . $style->value . ";" . $color->value . "m" . $text . "\033[0m";
}