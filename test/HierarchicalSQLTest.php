<?php
namespace hierarchicalSQL;
require 'hierarchicalSQL.php';

class HierarchicalSQLTest extends \PHPUnit\Framework\TestCase {

    protected $brackets;
    protected $operators;

    protected function setUp() {
        $this->brackets = array('(' => ')',
                  '[' => ']',
                  '{' => '}');
        $this->operators = array('AND' => 't1.score * t2.score',
                                 'OR' => 'MAX(t1.score, t2.score)');
    }

}

?>