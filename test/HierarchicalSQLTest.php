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
                return new SQLQuery('id', 'col1', 'tbl1');
            } elseif ($str == "2") {
                return new SQLQuery('id', 'col2', 'tbl2');
            } elseif ($str == "3") {
                return new SQLQuery('id', 'col3', 'tbl3');
            } elseif ($str == "4") {
                return new SQLQuery('id', 'col3', 'tbl1');
            } elseif ($str == "5") {
                return new SQLQuery('id', 'col1', 'tbl1', 'col1 > 2');
            } else {
                return new SQLQuery('invalid', 'invalid', 'tbl1');
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
        $this->assertEquals('SELECT id AS id, col1 AS score FROM tbl1', (string)$this->parseStrictQuery($query));
    }

    public function testSingleQueryParen() {
        $query = '(1)';
        $this->assertEquals('SELECT id AS id, col1 AS score FROM tbl1', (string)$this->parseStrictQuery($query));
        $query = '(((1)))';
        $this->assertEquals('SELECT id AS id, col1 AS score FROM tbl1', (string)$this->parseStrictQuery($query));
    }

    public function testMultipleQueries() {
        $query1 = '(1) AND (2)';
        $parser = $this->parser;
        $q1Str = (string)$parser('1');
        $q2Str = (string)$parser('2');
        $merged12Str = (string)$this->parseStrictQuery($query1);
        $this->assertEquals("SELECT t1.id AS id, t1.score * t2.score AS score FROM ($q1Str) AS t1, ($q2Str) AS t2 WHERE (t1.id = t2.id)", $merged12Str);

        $query2 = '(1) AND (2) AND (3)';
        $q3Str = (string)$parser('3');
        $this->assertEquals("SELECT t1.id AS id, t1.score * t2.score AS score FROM ($q3Str) AS t2, ($merged12Str) AS t1 WHERE (t1.id = t2.id)", (string)$this->parseStrictQuery($query2));
    
        $query3 = '((1) AND (2)) OR (3)';
        $this->assertEquals("SELECT t1.id AS id, MAX(t1.score, t2.score) AS score FROM ($q3Str) AS t2, ($merged12Str) AS t1 WHERE (t1.id = t2.id)", (string)$this->parseStrictQuery($query3));
    }

    public function testMerge() {
        $query1 = '(1) AND (4)';
        $this->assertEquals("SELECT id AS id, (col1) * (col3) AS score FROM tbl1", (string)$this->parseStrictQuery($query1));

        $query2 = '(1) AND (5)';
        $this->assertEquals("SELECT id AS id, (col1) * (col1) AS score FROM tbl1 WHERE (col1 > 2)", (string)$this->parseStrictQuery($query2));
    }

    public function testQueryToSQL() {
        $parser = $this->parser;
        $q1Str = (string)$parser('1');
        $q2Str = (string)$parser('2');
        $q3Str = (string)$parser('3');
        $q4Str = (string)$parser('4');
        $q5Str = (string)$parser('5');
        $merged12Str = (string)$this->parseStrictQuery('(1) AND (2)');
        $query = '1';
        $this->assertEquals($q1Str, queryToSQL($query, $this->parser, $this->brackets, $this->operators, 10, 10));
        $query = '1 AND 2';
        $this->assertEquals("SELECT t1.id AS id, t1.score * t2.score AS score FROM ($q1Str) AS t1, ($q2Str) AS t2 WHERE (t1.id = t2.id)", queryToSQL($query, $this->parser, $this->brackets, $this->operators, 10, 10));
        $query = '1 AND 4';
        $this->assertEquals('SELECT id AS id, (col1) * (col3) AS score FROM tbl1', queryToSQL($query, $this->parser, $this->brackets, $this->operators, 10, 10));
        $query = '(1 AND 2) OR 3';
        $this->assertEquals("SELECT t1.id AS id, MAX(t1.score, t2.score) AS score FROM ($q3Str) AS t2, ($merged12Str) AS t1 WHERE (t1.id = t2.id)", queryToSQL($query, $this->parser, $this->brackets, $this->operators, 10, 10));
    }

}

?>