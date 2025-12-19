<?php
require_once "mysql_statement.php";

class MySQLDB extends DB
{
    private ?PDO $connection = null;
    private bool $multi_row = false;

    public function getNewStatement(DBStatementType $type) : DBStatement
    {
        return new MySQLStatement($type);
    }

    /**
     * @param MySQLStatement $stmt
     */
    public function get(DBStatement $stmt)
    {
        if (!$stmt instanceof MySQLStatement)
            throw new InvalidArgumentException("Expected MySQLStatement!", 601);

        
    }

    /**
     * @param MySQLStatement $statement
     */
    public function getValues($statement)
    {
        $this->multi_row = true;
    }

    /**
     * @param MySQLStatement $statement
     */
    public function insert($statement)
    {
        
    }

    /**
     * @param MySQLStatement $statement
     */
    public function update($statement)
    {
        
    }

    /**
     * @param MySQLStatement $statement
     */
    public function delete($statement)
    {
        
    }

    /**
     * @param MySQLStatement $statement
     */
    public function custom($statement)
    {
        
    }

    protected function connect()
    {
        if (!is_null(self::$connection)) return;

        // Try to connect to the DB
        try
        {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->name;charset=utf8", $this->user, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            throw new Exception("Cannot connect to the Database: " . $e->getMessage(), 602);
        }

        self::$connection = $conn;
    }

    public function disconnect()
    {
        if (is_null($this->connection)) return;

        $this->connection = null;
    }
}