<?php
require_once('db_statement.php');

enum DBType : string
{
    case SQL = "sql";
    case MYSQL = "mysql";
    case POSTGRE = "postgre";
    case ORACLE = "oracle";
    case MONGODB = "mongodb";
}

abstract class DB
{
    private static ?DB $database = null;
    protected string $host;
    protected string $user;
    protected string $password;
    protected string $name;

    private function __construct()
    {
        $this->host = getenv('DB_HOST');
        $this->user = getenv('DB_USER');
        $this->password = getenv('DB_PASS');
        $this->name = getenv('DB_NAME');

        $this->connect();
    }

    public static function getDB() : DB
    {
        if (!is_null(self::$database)) return self::$database;
        
        $type = DBType::tryFrom(getenv("DB_TYPE"));
        if (is_null($type) || !is_dir(DB_PATH . strtolower($type->value)))
        {
            throw new UnexpectedValueException("DB type not supported!", 600);
        }

        require_once(DB_PATH . strtolower($type->value) . "/" . strtolower($type->value) . "_db.php");

        $class = $type->name . "DB";
        $db = new $class();

        self::$database = $db;
        return $db;
    }

    public static function destroyDB()
    {
        if (!is_null(self::$database)) self::$database->disconnect();
    }

    public abstract function getNewStatement(DBStatementType $type, ?string $table) : DBStatement;

    public abstract function get(DBStatement $statement);
    public abstract function getValues(DBStatement $statement);
    public abstract function insert(DBStatement $statement);
    public abstract function update(DBStatement $statement);
    public abstract function delete(DBStatement $statement);
    public abstract function custom(DBStatement $statement);

    protected abstract function connect();
    public abstract function disconnect();
}