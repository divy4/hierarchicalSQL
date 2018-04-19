<?php
namespace hierarchicalSQL;
require 'hierarchicalSQL.php';

class HierarchicalSQLTest extends \PHPUnit\Framework\TestCase {

    protected $parser;
    protected $brackets;
    protected $operators;

    protected function setUp() {
        $this->parser = function($str) {
            if ($str == "1") {
                return new SQLQuery('id, col1 as score', 'tbl1');
            } elseif ($str == "2") {
                return new SQLQuery('id, col2 as score', 'tbl2');
            } elseif ($str == "3") {
                return new SQLQuery('id, col3 as score', 'tbl');
            } elseif ($str == "4") {
                return new SQLQuery('id, col1 as score', 'tbl', 'col1 > 2');
            } else {
                return new SQLQuery('invalid as id, invalid as score', 'tbl1');
            }
        };
        $this->brackets = array('(' => ')',
                  '[' => ']',
                  '{' => '}');
        $this->operators = array('AND' => 't1.score * t2.score',
                                 'OR' => 'MAX(t1.score, t2.score)');
    }

    private function parseStrictQuery($query) {
        return parseStrictQuery($query, $this->parser, $this->brackets, $this->operators);
    }

    public function testSingleQuery() {
        $query = '1';
        $this->assertEquals('SELECT id,col1 as score FROM tbl1', (string)$this->parseStrictQuery($query));
    }

    public function testSingleQueryParen() {
        $query = '(1)';
        $this->assertEquals('SELECT id,col1 as score FROM tbl1', (string)parseStrictQuery($query, $this->parser, $this->brackets, $this->operators));
        $query = '(((1)))';
        $this->assertEquals('SELECT id,col1 as score FROM tbl1', (string)parseStrictQuery($query, $this->parser, $this->brackets, $this->operators));
    }

}

?>