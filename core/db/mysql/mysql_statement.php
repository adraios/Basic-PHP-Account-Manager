<?php
/**
 * $condition structure example:
 * $condition = [
 *  "AND" => [
 *      ["name", "=", "Markus"],
 *      ["alias", "!=", markus01""],
 *  ],
 *  "OR" => [
 *      ["money", ">=", 1000]
 *  ]
 * ];
 * 
 * 
 * INSERT $fields structure example:
 * $fields = [
 *      "name" => "Markus",
 *      "money" => 1100
 * ]
 */

class MySQLStatement extends DBStatement
{
    private array $params = [];

    public function getParams() : array
    {
        return $this->params;
    }

    public function queryToString() : string
    {
        if (!empty($this->query)) return $this->query;

        return match($this->type)
        {
            DBStatementType::SELECT => $this->genSelect(),
            DBStatementType::INSERT => $this->genInsert(),
            DBStatementType::UPDATE => $this->genUpdate(),
            DBStatementType::DELETE => $this->genDelete(),
            default => throw new InvalidArgumentException("Invalid DBStatementType!", 603)
        };
    }

    private function genSelect() : string
    {
        if (empty($this->fields)) throw new InvalidArgumentException("Cannot generate Select statement with empty fields!", 604);
        if (empty($this->table)) throw new InvalidArgumentException("Cannot generate Select statement with empty table!", 605);
        $table = $this->sanitize($this->table);

        $query = "SELECT ";
        
        $fields = "";
        foreach($this->fields as $field)
        {
            if (!empty($fields)) $fields .= ", ";
            
            $sanField = $this->sanitize($field);
            $fields .= "`$sanField`";
        }
        $query .= $fields;

        $query .= " FROM `$table`";
        $query .= " WHERE " . $this->genWhere($this->condition, $this->params);
        if (!empty($this->group)) $query .= $this->genGroupBy();
        if (!empty($this->order)) $query .= $this->genOrderBy();
        if (!empty($this->limit)) $query .= $this->genLimit();

        return $query;
    }

    private function genInsert() : string
    {
        if (empty($this->fields)) throw new InvalidArgumentException("Cannot generate Insert statement with empty fields!", 604);
        if (empty($this->table)) throw new InvalidArgumentException("Cannot generate Insert statement with empty table!", 605);
        $table = $this->sanitize($this->table);

        $query = "INSERT INTO `$table` (`";

        $fields = "";
        foreach ($this->fields as $key => $value)
        {
            $sanKey = $this->sanitize($key);
            $paramKey = ":$sanKey";
            $this->params[$paramKey] = $value;

            if (!empty($fields)) $fields .= ", ";
            $fields .= "`$sanKey`";
        }

        $query .= $fields . ") VALUES (";
        $query .= implode(", ", array_keys($this->params)) . ")";

        return $query;
    }

    private function genUpdate() : string
    {
        return "";
    }

    private function genDelete() : string
    {
        return "";
    }

    private function genWhere(array $conditions, array &$params = [], int &$count = 0) : string
    {
        $where = '';
        foreach ($conditions as $logic => $condition)
        {
            $logic = strtoupper($logic) === 'OR' ? $logic : 'AND';

            if ( !empty($where) ) $where .= ' AND ';

            $where .= '(';
            $inner = '';

            foreach ($condition as $subcondition)
            {
                if (!empty($inner)) $inner .= " $logic ";

                if (isset($subcondition['OR']) || isset($subcondition['AND']))
                {
                    $inner .= $this->genWhere($subcondition);
                    continue;
                }

                [$key, $operator, $value] = $subcondition;

                $key = $this->sanitize($key);
                $paramKey = ':' . $key . $count++;
                $params[$paramKey] = $value;
                $inner .= "`$key` $operator $paramKey";
            }

            $where .= $inner . ')';
        }

        return $where;
    }

    private function genGroupBy() : string
    {
        return "";
    }

    private function genOrderBy() : string
    {
        return "";
    }

    private function genLimit() : string
    {
        return "";
    }

    public function checkFormatValue($value) : bool
    {
        ///// TODO: Check with DB schema if the value accomplish with it's data type and lenght

        return true;
    }

    private function sanitize(string $value)
    {
        $value = trim($value);
        $value = preg_replace('/\W+/', '_', $value);

        return $value;
    }
}
