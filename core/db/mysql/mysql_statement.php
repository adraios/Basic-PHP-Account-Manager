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

class MYSQLStatement extends DBStatement
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
            default => throw new InvalidArgumentException("Unsupported DBStatementType!", 603)
        };
    }

    private function genSelect() : string
    {
        $this->checkParams();

        $query = "SELECT ";
        
        $fields = "";
        foreach($this->fields as $field)
        {
            if (!empty($fields)) $fields .= ", ";
            
            $sanField = $this->sanitize($field);
            $fields .= "`$sanField`";
        }
        $query .= $fields;

        $query .= " FROM `$this->table`";
        $query .= $this->genWhere();
        $query .= $this->genGroupBy();
        $query .= $this->genOrderBy();
        $query .= $this->genLimit();

        return $query;
    }

    private function genInsert() : string
    {
        $this->checkParams();

        $query = "INSERT INTO `$this->table` (`";

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
        throw new ErrorException("Not yet implemented!", 1000);

        return "";
    }

    private function genDelete() : string
    {
        throw new ErrorException("Not yet implemented!", 1001);

        return "";
    }

    private function genWhere() : string
    {
        if (empty($this->condition)) return "";

        return " WHERE " . $this->genWhereRec($this->condition, $this->params);
    }

    private function genWhereRec(array $conditions, array &$params = [], int &$count = 0) : string
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

                if (!$this->allowedOperator($operator)) throw new InvalidArgumentException("Invalid Operator!", 606);

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
        if (empty($this->group)) return "";

        return " GROUP BY " . implode(", ", $this->group);
    }

    private function genOrderBy() : string
    {
        if (empty($this->order)) return "";

        return " ORDER BY " . implode(", ", $this->order);
    }

    private function genLimit() : string
    {
        if (empty($this->limit)) return "";
        if (sizeof($this->limit) > 2) $this->limit = array_slice($this->limit, 0, 2); // TEST!!!!!

        return " LIMIT " . implode(", ", $this->limit);
    }

    private function checkParams() : void
    {
        if (empty($this->fields)) throw new InvalidArgumentException("Cannot generate Insert statement with empty fields!", 604);
        if (empty($this->table)) throw new InvalidArgumentException("Cannot generate Insert statement with empty table!", 605);

        ////// TODO: Execute checkFormatValue //////
    }

    public function checkFormatValue($value) : bool
    {
        ///// TODO: Check with DB schema if the value accomplish with it's data type and lenght

        return true;
    }

    protected function sanitize(string $value) : string
    {
        $value = trim($value);
        $value = preg_replace('/\W+/', '_', $value);

        return $value;
    }

    private function allowedOperator(string $operator) : bool
    {
        $operators = ['=', '!=', '<>', '<', '>', '<=', '>='];

        return in_array($operator, $operators, true);
    }
}
