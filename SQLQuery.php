<?php
namespace hierarchicalSQL;

class SQLQuery {

    private $from;
    private $where;

    function __construct($from, $where) {
        
    }

    function __toString() {
        return "SQLQuery";
    }

}


?>