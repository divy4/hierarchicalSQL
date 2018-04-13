<?php
namespace hierarchicalSQL;

class SQLQuery {

    private $select;
    private $from;
    private $where;

    /**
     * Undocumented function
     *
     * @param [string] $selectOrText
     * @param [string] $from
     * @param [type] $where
     */
    public function __construct($selectOrText, $from=null, $where=null) {
        if ($from == null) {
            constructFromText($selectOrText);
        } else {
            constructFromParts($selectOrText, $from, $where);
        }
    }

    /**
     * Constructs a SQLQuery from the text of the entire query.
     *
     * @param [String] $text
     * @return void
     */
    private function constructFromText($text) {
        throw new Exception("Not implemented!");
    }

    /**
     * Constructs a SQLQuery from a string or array of strings for each keyword.
     * Each parameter should either be (unless specified otherwise):
     *          A) A string that contains all text after the keyword (e.g. 'SELECT id, name as n' would be inputted as 'id, name as n') or,
     *          B) An array of strings, where each string is an item after the keyword (e.g. 'SELECT id, name as n' would be inputted as ['id', 'name as n'])
     * Note that each parameter contains the infromation after the SQL keyword that shares the same name.
     *
     * @param [string or array[string]] $select
     * @param [string or array[string]] $from
     * @param [string] $where
     * @return void
     */
    private function constructFromParts($select, $from, $where=null) {
        // array values
        setKeywordValues('select', $select);
        setKeywordValues('from', $from);
        // single string values
        if ($where == null) {
            $this->where = null;
        } else {
            $this->where = [$where];
        }
    }

    /**
     * Sets the value for a keyword member variable (i.e. $this->$partName) to the array equivalent of $value.
     *
     * @param [string] $partName The name of the variable being set.
     * @param [null, string, or array[string]] $value The value being set to that variable.
     * @return void
     */
    private function setKeywordValues($partName, $value) {
        // default
        if ($value == null) {
            $this->$partName = [];
        } else {
            // parse string into array
            if (is_a($value, 'string')) {
                $value = componentStrToArray($value);
            }
            // array
            $this->$partName = $value;
        }
    }

    /**
     * Converts a string that matches constructFromParts()'s format A) for an argument into format B)
     *
     * @param [string] $str A string argument for constructFromParts().
     * @return [array[string]] An array of each element in $str.
     */
    private function componentStrToArray($str) {
        //TODO: seperate string by comma
        return [$str];
    }

    /**
     * Converts a string that matches constructFromParts()'s format B) for an argument into format A) 
     *
     * @param [array[string]] $arr An array of elements from the 
     * @return [string] A string of every element in $arr.
     */
    private function componentArrayToStr($arr, $separator=',') {
        return implode($separator, $arr);
    }

    /**
     * Prints the text of the SQLQuery.
     *
     * @return string
     */
    public function __toString() {
        // required
        $select = componentArrayToStr($this->select);
        $from = componentArrayToStr($this->from);
        $out = "SELECT $select FROM $from";
        // optional
        if ($this->where != null) {
            $out = "$out WHERE (" . componentArrayToStr($this->where, ') AND (') . ')';
        }
        return $out;
    }

}


?>