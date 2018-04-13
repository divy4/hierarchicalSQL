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
        print(gettype($selectOrText));
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
        throw new Exception("Not implemented!");
    }

    /**
     * Converts a string that matches constructFromParts()'s format A) for an argument into format B)
     *
     * @param [string] $str A string argument for constructFromParts().
     * @return [array[string]] An array of each element in $str.
     */
    private function componentStrToArray($str) {
        throw new Exception("Not implemented!");
    }

    /**
     * Prints the text of the SQLQuery.
     *
     * @return string
     */
    public function __toString() {
        return 'SELECT *';
    }

}


?>