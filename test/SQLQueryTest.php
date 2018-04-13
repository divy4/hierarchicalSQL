<?php
namespace hierarchicalSQL;
require 'SQLQuery.php';


class SQLQueryTest extends \PHPUnit\Framework\TestCase {

    public function testConstruction() {
        $query = new SQLQuery("id", "name");
    }
    
}