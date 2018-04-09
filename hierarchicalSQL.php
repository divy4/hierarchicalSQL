<?php
namespace hierarchicalSQL;

require 'util.php';


/**
 * Parses $query into a hierarchical SQL statement.
 *
 * @param [String] $query The text of a query.
 * @param [String lambda(String)] $baseParser A lambda function that parses a basic query (i.e. no logic operators or brackets) into a SQL statement that contains 2 columns ('id' and 'score').
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @param [Integer] $maxDepth The maximum depth of brackets the query can contain.
 * @param [Integer] $maxSubQueries The maximum number of subqueries (i.e. the number of $baseParser calls) the query can contain.
 * @return [String] A SQL query.
 */
function queryToSQL($query, $baseParser, $brackets, $operators, $maxDepth, $maxSubQueries) {
    // check if query is valid
    \hierarchicalSQL\validateQuery($query, $brackets, $operators, $maxDepth, $maxSubQueries);
    // add missing brackets/operators
    // parse query
    return null;
}

?>