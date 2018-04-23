<?php
namespace hierarchicalSQL;
require 'SQLQuery.php';


class SQLQueryTest extends \PHPUnit\Framework\TestCase {

    public function testSingle() {
        $query = new SQLQuery('col1', 'col2', 'tbl1');
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1', (string)$query);
        $query = new SQLQuery('col1', 'col2', ['tbl1']);
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1', (string)$query);
    }

    public function testMultiple() {
        $query = new SQLQuery('col1', 'col2', 'tbl1,tbl2');
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1, tbl2', (string)$query);
        $query = new SQLQuery('col1', 'col2', ['tbl1', 'tbl2']);
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1, tbl2', (string)$query);
    }

    public function testWhitespace() {
        $query = new SQLQuery(' col1 ', ' col2 ', ' tbl1' );
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1', (string)$query);
        $query = new SQLQuery(' col1 ', ' col2 ', [' tbl1 ']);
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1', (string)$query);
    }

    public function testWhitespaceMultiple() {
        $query = new SQLQuery('  col1  ', '  col2 ', ' tbl1 , tbl2 ');
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1, tbl2', (string)$query);
        $query = new SQLQuery('  col1  ', '  col2 ', [' tbl1 ', ' tbl2 ']);
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1, tbl2', (string)$query);
    }

    public function testAs() {
        $query = new SQLQuery('col1', 'col2', 'tbl1 AS t1');
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1 AS t1', (string)$query);
        $query = new SQLQuery('col1', 'col2', ['tbl1 AS t1']);
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1 AS t1', (string)$query);
    }

    public function testWhere() {
        $query = new SQLQuery('col1', 'col2', 'tbl1', 'col1 > 0 AND col1 < 10');
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1 WHERE (col1 > 0 AND col1 < 10)', (string)$query);
        $query = new SQLQuery('col1', 'col2', ['tbl1'], ['col1 > 0', 'col1 < 10']);
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM tbl1 WHERE (col1 < 10) AND (col1 > 0)', (string)$query);
    }

    public function testMath() {
        $query = new SQLQuery('col1', 'col2 - col3', 'tbl1');
        $this->assertEquals('SELECT col1 AS id, col2 - col3 AS score FROM tbl1', (string)$query);
    }

    public function testSubQuery() {
        $query = new SQLQuery('col1', 'col2', '(SELECT col1, col2 FROM tbl2, tbl3) AS tbl1');
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM (SELECT col1, col2 FROM tbl2, tbl3) AS tbl1', (string)$query);
        $query = new SQLQuery('col1', 'col2', ['(SELECT col1, col2 FROM tbl2, tbl3) AS tbl1']);
        $this->assertEquals('SELECT col1 AS id, col2 AS score FROM (SELECT col1, col2 FROM tbl2, tbl3) AS tbl1', (string)$query);
    }

    public function testMerge() {
        $q1 = new SQLQuery('col1', 'col2', 'tbl1', 'score > 1');
        $q1Str = (string)$q1;
        $q2 = new SQLQuery('col1', 'col2', 'tbl2');
        $q2Str = (string)$q2;
        $score = 't1.score + t2.score';
        $merged = SQLQuery::merge($q1, $q2, $score);
        $this->assertEquals("SELECT t1.id AS id, t1.score + t2.score AS score FROM ($q1Str) AS t1, ($q2Str) AS t2 WHERE (t1.id = t2.id)", (string)$merged);
        
        $q3 = new SQLQuery('col2', 'col3', 'tbl1');
        $q3Str = (string)$q3;
        $merged = SQLQuery::merge($q1, $q3, $score);
        $this->assertEquals("SELECT t1.id AS id, t1.score + t2.score AS score FROM ($q1Str) AS t1, ($q3Str) AS t2 WHERE (t1.id = t2.id)", (string)$merged);

        $q4 = new SQLQuery('col1', 'col3', 'tbl1', 'score < 10');
        $q4Str = (string)$q4;
        $merged = SQLQuery::merge($q1, $q4, $score);
        $this->assertEquals("SELECT col1 AS id, (col2) + (col3) AS score FROM tbl1 WHERE (score < 10) AND (score > 1)", (string)$merged);
    }
    
}