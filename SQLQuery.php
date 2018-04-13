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
            $this->constructFromText($selectOrText);
        } else {
            $this->constructFromParts($selectOrText, $from, $where);
        }
    }

    /**
     * Constructs a SQLQuery from the text of the entire query.
     *
     * @param [String] $text
     * @return void
     */
    private function constructFromText(string $text) {
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
        $this->setKeywordValues('select', $select, ',');
        $this->setKeywordValues('from', $from, ',');
        $this->setKeywordValues('where', $where, null);
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
            // array
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
                } elseif ($depth == 0 && $pos == $splitEnd) {
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
            print("asdf2:$str\n");
            print("asdf:" . $this->componentArrayToStr($arr, '---') . "\n");
            $arr = [$str];
        }
        return $arr;
    }

    /**
     * Converts a string that matches constructFromParts()'s format B) for an argument into format A) 
     *
     * @param [array[string]] $arr An array of elements from the 
     * @return [string] A string of every element in $arr.
     */
    private function componentArrayToStr(array $arr, string $separator=',') {
        if ((string)implode($separator, $arr) == 'Array') {
            print_r($arr);
            $val = implode($separator, $arr);
        }
        return implode($separator, $arr);
    }

    /**
     * Prints the text of the SQLQuery.
     *
     * @return string
     */
    public function __toString() {
        // required
        $select = $this->componentArrayToStr($this->select);
        $from = $this->componentArrayToStr($this->from);
        $out = "SELECT $select FROM $from";
        // optional
        if ($this->where != null) {
            $out = "$out WHERE (" . $this->componentArrayToStr($this->where, ') AND (') . ')';
        }
        return $out;
    }

}


?>