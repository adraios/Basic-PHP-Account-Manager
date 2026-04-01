<?php
require_once "mysql_statement.php";

class MYSQLDB extends DB
{
    private ?PDO $connection = null;
    private bool $multi_row = false;

    public function getNewStatement(DBStatementType $type, ?string $table) : DBStatement
    {
        return new MYSQLStatement($type, $table);
    }

    /**
     * @param MYSQLStatement $stmt
     */
    public function get(DBStatement $stmt) : ?stdClass
    {
        $this->checkValidStatement($stmt);

        // Execute the query
        $statement = $this->execute($stmt);
        
        // Fetch results
        $result = $statement->fetchObject();
        if ($result === false) return null;

        return $result;
    }

    /**
     * @param MYSQLStatement $stmt
     */
    public function getValues(DBStatement $stmt) : array
    {
        $this->checkValidStatement($stmt);

        $this->multi_row = true;
        $statement = $this->execute($stmt);

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param MYSQLStatement $stmt
     */
    public function insert(DBStatement $stmt) : ?int
    {
        $this->checkValidStatement($stmt);

        $this->execute($stmt);
        
        $id = $this->connection->lastInsertId();
        if ($id === false) return null;

        return $id;
    }

    /**
     * @param MYSQLStatement $stmt
     */
    public function update(DBStatement $stmt) : void
    {
        $this->checkValidStatement($stmt);

        $this->execute($stmt);
    }

    /**
     * @param MYSQLStatement $stmt
     */
    public function delete(DBStatement $stmt) : int
    {
        $this->checkValidStatement($stmt);

        $statement = $this->execute($stmt);
        return $statement->rowCount();
    }

    /**
     * @param MYSQLStatement $stmt
     */
    public function custom(DBStatement $stmt)
    {
        $this->checkValidStatement($stmt);

        $this->execute($stmt);
    }

    protected function connect()
    {
        if (!is_null($this->connection)) return;

        // Try to connect to the DB
        try
        {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->name;charset=utf8", $this->user, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            throw new PDOException("Cannot connect to the Database: " . $e->getMessage(), 602);
        }

        $this->connection = $conn;
    }

    public function disconnect()
    {
        if (is_null($this->connection)) return;

        $this->connection = null;
    }

    private function checkValidStatement(DBStatement $stmt) : void
    {
        if (!$stmt instanceof MYSQLStatement)
            throw new InvalidArgumentException("Expected MYSQLStatement!", 601);
    }

    private function execute(MYSQLStatement $stmt) : PDOStatement
    {
        $query = $stmt->queryToString();
        $statement = $this->connection->prepare($query);
        if (!$statement->execute($stmt->getParams()))
        {
            $error = $this->connection->errorInfo();
            throw new PDOException($error[2] . ": " . $query, $error[1]);
        }

        return $statement;
    }
}