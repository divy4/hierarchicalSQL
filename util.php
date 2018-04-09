<?php
namespace hierarchicalSQL;


/**
 * Tests if a every bracket in a query is properly matched to its pair.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @return void
 */
function validateBrackets($query, $brackets) {
    $seenBrackets = new \SplStack();
    $qLen = strlen($query);
    // for char in query
    for ($i = 0; $i < $qLen; $i++) {
        $char = $query[$i];
        // open parenthesis
        if (array_key_exists($char, $brackets)) {
            $seenBrackets->push($char);
        // close parenthesis
        } elseif (in_array($char, $brackets)) {
            // extra bracket
            if ($seenBrackets->count() == 0) {

            }
            $lastBracket = $seenBrackets->pop();
        }
    }
    
}


/**
 * Tests if a query can be parsed. No action is taken when $query is valid, otherwise, an Exception with a descriptive string will be thrown.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative Array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @param [Integer] $maxDepth The maximum depth of brackets the query can contain.
 * @param [Integer] $maxSubQueries The maximum number of subqueries (i.e. the number of $baseParser calls) the query can contain.
 */
function validateQuery($query, $brackets, $operators, $maxDepth, $maxSubqueries) {
    \hierarchicalSQL\validateBrackets($query, $brackets);
}


?>