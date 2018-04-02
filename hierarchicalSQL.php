<?php
namespace hierarchicalSQL;

/**
 * Parses $query into a hierarchical SQL statement.
 *
 * @param [String] $query The text of a query.
 * @param [String lambda(String)] $baseParser A lambda function that parses a basic query (i.e. no logic operators or brackets) into a SQL statement that contains 2 columns ('id' and 'score').
 * @param [Array[String]] $brackets An array of strings, where each string is 2 chars long, the first being the starting bracket and the second the matching ending bracket.
 * @param [Array[String => String]] $operators An associative Array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @return [String] A SQL query.
 */
function parseQuery($query, $baseParser, $brackets, $operators) {
    
    return null;
}

?>