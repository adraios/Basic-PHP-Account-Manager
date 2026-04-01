<?php
class Languages
{
    private static $accepted_languages = array('en');
    private static $current_language = 'en';
    private static $language_strings = array();

    public static function loadAcceptedLanguages()
    {
        // Load accepted languages from DB
        $statement = DB::getDB()->getNewStatement(DBStatementType::SELECT, 'languages');
        $statement->setFields(['id']);
        $values = DB::getDB()->getValues($statement);

        self::$accepted_languages = array_column($values, "id");
    }

    public static function isAcceptedLanguage(string $lang_code)
    {
        return in_array($lang_code, self::$accepted_languages);
    }

    public static function getDefaultLanguage()
    {
        return getenv('DEFAULT_LANGUAGE');
    }

    public static function setLanguage(string $lang_code)
    {
        if (self::isAcceptedLanguage($lang_code))
            self::$current_language = $lang_code;

        /////////// TODO Load language file based on $lang_code ///////////
    }

    public static function getCurrentLanguage()
    {
        return self::$current_language;
    }

    public static function getString(string $key)
    {
        // Key not found
        if (empty(self::$language_strings) || !isset((self::$language_strings[$key])))
            return $key;

        return self::$language_strings[$key];
    }
}

// Initialize accepted languages on load
Languages::loadAcceptedLanguages();