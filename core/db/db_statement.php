<?php
enum DBStatementType
{
    case SELECT;
    case INSERT;
    case UPDATE;
    case DELETE;
    case CUSTOM;
}

abstract class DBStatement
{
    protected DBStatementType $type;
    protected ?string $table;
    protected string $query = "";
    protected array $fields;
    protected array $condition;
    protected array $group;
    protected array $order;
    protected array $limit;

    public function __construct(DBStatementType $type, ?string $table = null)
    {
        $this->type = $type;
        $this->table = $table;
    }

    public function setFields(array $fields) : void
    {
        /////// TODO: Check fields exist in table //////////
        $this->fields = $fields;
    }

    public function setCondition(array $condition) : void
    {
        /////// TODO: Check fields exist in table //////////
        $this->condition = $condition;
    }
    
    public function setGroup(array $group) : void
    {
        /////// TODO: Check fields exist in table //////////
        $this->group = $group;
    }

    public function setOrder(array $order) : void
    {
        /////// TODO: Check fields exist in table //////////
        $this->order = $order;
    }

    public function setLimit(array $limit) : void
    {
        $this->limit = $limit;
    }

    public function setCustomQuery(string $query) : void
    {
        $this->query = $query;
    }

    public abstract function queryToString() : string;
}