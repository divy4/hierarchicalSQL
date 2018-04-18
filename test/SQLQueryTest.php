<?php
namespace hierarchicalSQL;
require 'SQLQuery.php';


class SQLQueryTest extends \PHPUnit\Framework\TestCase {

    public function testSingle() {
        $query = new SQLQuery('col1', 'tbl1');
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
        $query = new SQLQuery(['col1'], ['tbl1']);
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
    }

    public function testMultiple() {
        $query = new SQLQuery('col1,col2', 'tbl1,tbl2');
        $this->assertEquals('SELECT col1,col2 FROM tbl1,tbl2', (string)$query);
        $query = new SQLQuery(['col1', 'col2'], ['tbl1', 'tbl2']);
        $this->assertEquals('SELECT col1,col2 FROM tbl1,tbl2', (string)$query);
    }

    public function testWhitespace() {
        $query = new SQLQuery(' col1 ', ' tbl1' );
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
        $query = new SQLQuery([' col1' ], [' tbl1 ']);
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
    }

    public function testWhitespaceMultiple() {
        $query = new SQLQuery(' col1 , col2 ', ' tbl1 , tbl2 ');
        $this->assertEquals('SELECT col1,col2 FROM tbl1,tbl2', (string)$query);
        $query = new SQLQuery([' col1 ', ' col2 '], [' tbl1 ', ' tbl2 ']);
        $this->assertEquals('SELECT col1,col2 FROM tbl1,tbl2', (string)$query);
    }

    public function testAs() {
        $query = new SQLQuery('col1 as c1', 'tbl1');
        $this->assertEquals('SELECT col1 as c1 FROM tbl1', (string)$query);
        $query = new SQLQuery(['col1 as c1'], ['tbl1']);
        $this->assertEquals('SELECT col1 as c1 FROM tbl1', (string)$query);
    }

    public function testWhere() {
        $query = new SQLQuery('col1', 'tbl1', 'col1 > 0 AND col1 < 10');
        $this->assertEquals('SELECT col1 FROM tbl1 WHERE (col1 > 0 AND col1 < 10)', (string)$query);
        $query = new SQLQuery(['col1'], ['tbl1'], ['col1 > 0', 'col1 < 10']);
        $this->assertEquals('SELECT col1 FROM tbl1 WHERE (col1 > 0) AND (col1 < 10)', (string)$query);
    }

    public function testMath() {
        $query = new SQLQuery('col1 - col2 as c1', 'tbl1');
        $this->assertEquals('SELECT col1 - col2 as c1 FROM tbl1', (string)$query);
        $query = new SQLQuery(['col1 - col2 as c1'], ['tbl1']);
        $this->assertEquals('SELECT col1 - col2 as c1 FROM tbl1', (string)$query);
    }

    public function testSubQuery() {
        $query = new SQLQuery('col1', '(SELECT col1, col2 FROM tbl2, tbl3) as tbl1');
        $this->assertEquals('SELECT col1 FROM (SELECT col1, col2 FROM tbl2, tbl3) as tbl1', (string)$query);
        $query = new SQLQuery(['col1'], ['(SELECT col1, col2 FROM tbl2, tbl3) as tbl1']);
        $this->assertEquals('SELECT col1 FROM (SELECT col1, col2 FROM tbl2, tbl3) as tbl1', (string)$query);
    }

    public function testMixed() {
        $query = new SQLQuery('col1', ['tbl1']);
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
    }

    public function testMerge() {
        $q1 = new SQLQuery('id, score', 'tbl1');
        $q1Str = (string)$q1;
        $q2 = new SQLQuery('id, score', 'tbl2');
        $q2Str = (string)$q2;
        $score = 't1.score + t2.score';
        $merged = SQLQuery::merge($q1, $q2, $score);
        $this->assertEquals("SELECT t1.id,(t1.score + t2.score) as score FROM ($q1Str) as t1,($q2Str) as t2", (string)$merged);
    }
    
}