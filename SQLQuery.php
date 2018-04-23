<?php
namespace hierarchicalSQL;

class SQLQuery {

    private $id;
    private $score;
    private $from;
    private $where;
    private $having;
    private $limit;

    /**
     * Creates a SQLQuery
     *
     * @param [string] $id The column that should be used for the id.
     * @param [stirng] $score The column, function, etc... that should be used for the score
     * @param [string or array[string]] $from The tables being selected.
     * @param [string or array[string]] $where Any constraint for what rows to consider.
     * @param [string or array[string]] $having Any constraint for what rows to return. If this value is not null, "ORDER BY id" will automatically be added to the query.
     * @param [Integer or null] $limit The maximum number of results to return.
     */
    public function __construct(string $id, string $score, $from=null, $where=null, $having=null, $limit=null) {
        $this->constructFromParts($id, $score, $from, $where, $having, $limit);
    }

    /**
     * Constructs a SQLQuery from a string or array of strings for each keyword.
     * Each parameter should either be (unless specified otherwise):
     *          A) A string that contains all text after the keyword (e.g. 'SELECT id, name as n' would be inputted as 'id, name as n') or,
     *          B) An array of strings, where each string is an item after the keyword (e.g. 'SELECT id, name as n' would be inputted as ['id', 'name as n'])
     * Note that each parameter contains the infromation after the SQL keyword that shares the same name.
     *
     * @param [string] $id The column that should be used for the id.
     * @param [stirng] $score The column, function, etc... that should be used for the score.
     * @param [string or array[string]] $from The tables being selected.
     * @param [string or array[string]] $where Any constraint for what rows to consider.
     * @param [string or array[string]] $having Any constraint for what rows to return. If this value is not null, "ORDER BY id" will automatically be added to the query.
     * @param [Integer or null] $limit The maximum number of results to return.
     * @return void
     */
    private function constructFromParts($id, $score, $from, $where=null, $having=null, $limit=null) {
        $this->id = \trim($id);
        $this->score = \trim($score);
        $this->setKeywordValues('from', $from, ',');
        $this->setKeywordValues('where', $where, null);
        $this->setKeywordValues('having', $having, null);
        if (is_null($limit)) {
            $this->limit = null;
        } else {
            $this->limit = $limit;
        }
    }

    /**
     * Sets the value for a keyword member variable (i.e. $this->$partName) to the array equivalent of $value.
     *
     * @param [string] $partName The name of the variable being set.
     * @param [null, string, or array[string]] $value The value being set to that variable.
     * @param [string or null] $separator A string that separates different values if $value is a string. If null, the entire string is considered a single element.
     * @return void
     */
    private function setKeywordValues(string $partName, $value, $separator) {
        // default
        if ($value == null) {
            $this->$partName = null;
        } else {
            // parse string into array
            if (is_string($value)) {
                $value = $this->componentStrToArray($value, $separator);
            }
            // trim whitespace from array
            $valueSize = count($value);
            for ($i = 0; $i < $valueSize; $i++) {
                $value[$i] = \trim($value[$i]);
            }
            // sort
            sort($value);
            // set keyword elements
            $this->$partName = $value;
        }
    }

    /**
     * Converts a string that matches constructFromParts()'s format A) for an argument into format B)
     *
     * @param [string] $str A string argument for constructFromParts().
     * @param [string or null] $separator A string that separates different elements in $str. If null, the entire string is considered a single element.
     * @return [array[string]] An array of each element in $str.
     */
    private function componentStrToArray(string $str, $separator) {
        // remove white space
        $str = \trim($str);
        $arr = null;
        // no separator
        if ($separator == null) {
            $arr = [$str];
        // seperator
        } else {
            $depth = 0;
            $arrSize = 0;
            $splitStart = 0;
            $splitEnd = strpos($str, $separator); // possible ending for splits
            $sepLen = strlen($separator);
            // for char in str
            $strLen = strlen($str);
            for ($pos = 0; $pos < $strLen; $pos++) {
                $char = $str[$pos];
                // open parenthesis
                if ($char == '(') {
                    $depth++;
                // close parenthesis
                } elseif ($char == ')') {
                    $depth--;
                    // find next separator if not inside parenthesis
                    if ($depth == 0) {
                        $splitEnd = strpos($str, $separator, $pos+1);
                    } 
                // not inside parenthesis and at separator
                } elseif ($depth == 0 && $pos == $splitEnd && is_int($splitEnd)) {
                    // add substring to array
                    $arr[$arrSize] = substr($str, $splitStart, $splitEnd - $splitStart);
                    $arrSize++;
                    // find next separator
                    $splitStart = $splitEnd + $sepLen;
                    $splitEnd = strpos($str, $separator, $splitEnd + $sepLen);
                }
            }
            // add remaining
            $arr[$arrSize] = substr($str, $splitStart, $strLen - $splitStart);
        }
        return $arr;
    }

    /**
     * Converts a string that matches constructFromParts()'s format B) for an argument into format A) 
     *
     * @param [array[string]] $arr An array of elements from the 
     * @return [string] A string of every element in $arr.
     */
    private function componentArrayToStr(array $arr, string $separator=', ') {
        if ((string)implode($separator, $arr) == 'Array') {
            $val = implode($separator, $arr);
        }
        return implode($separator, $arr);
    }

    /**
     * Merges component arrays from SQLQuery objects
     *
     * @param [null or array[string]] $arr1 The first component array.
     * @param [null or array[string]] $arr2 The second component array.
     * @return void
     */
    private static function mergeComponentArrays($arr1, $arr2) {
        $merged = null;
        if ($arr1 != null) {
            $merged = $arr1;
        }
        if ($arr2 != null) {
            if ($arr1 == null) {
                $merged = $arr2;
            } else {
                $merged = array_merge($arr1, $arr2);
                sort($merged);
            }
        }
        return $merged;
    }

    /**
     * Merges this and another SQLQuery into a singleSQLQuery.
     *
     * @param SQLQuery $q1 The first SQLQuery being merged.
     * @param SQLQuery $q2 The second SQLQuery being merged.
     * @param string $score A string that specifies how the scores of the two queries should be merged.
     *              The string should be considered as a column in a select statement between two tables, 't1' and 't2',
     *              that both contain the columns 'id' and 'score'. e.g. 't1.score + t2.score'.
     *              Note that the values t1.id and t2.id are always the same.
     * @return SQLQuery The merged result.
     */
    public static function merge(SQLQuery $q1, SQLQuery $q2, string $score) {
        $merged = null;
        // matching tables
        if ($q1->id == $q2->id && $q1->from == $q2->from) {
            // change operator to use values directly from query score functions
            $score = str_replace("t1.score", "($q1->score)", $score);
            $score = str_replace("t2.score", "($q2->score)", $score);
            // merge other components
            $where = SQLQuery::mergeComponentArrays($q1->where, $q2->where);
            $merged = new SQLQuery($q1->id, $score, $q1->from, $where);
        } else {
            $q1Str = (string)$q1;
            $q2Str = (string)$q2;
            $where = ['t1.id = t2.id'];
            $merged = new SQLQuery('t1.id', $score, ["($q1Str) AS t1", "($q2Str) AS t2"], $where);
        }
        return $merged;
    }

    /**
     * Prints the text of the SQLQuery.
     *
     * @return string
     */
    public function __toString() {
        // required
        $select = $this->componentArrayToStr(["$this->id AS id", "$this->score AS score"]);
        $from = $this->componentArrayToStr($this->from);
        $out = "SELECT $select FROM $from";
        // optional
        if ($this->where != null) {
            $out = "$out WHERE (" . $this->componentArrayToStr($this->where, ') AND (') . ')';
        }
        if ($this->having != null) {
            $out = "$out GROUP BY id HAVING (" . $this->componentArrayToStr($this->where, ') AND (') . ')';
        }
        if ($this->limit != null) {
            $out = "$out LIMIT $this->limit";
        }
        return $out;
    }

}


?>