<?php
namespace hierarchicalSQL;

/**
 * Tests if a every bracket in a query is properly matched to its pair.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @return true if query has valid brackets.
 */
function validBrackets($query, $brackets) {
    $seenBrackets = new \SplStack();
    $qLen = strlen($query);
    // for char in query
    for ($i = 0; $i < $qLen; $i++) {
        $bracket = $query[$i];
        // open parenthesis
        if (array_key_exists($bracket, $brackets)) {
            $seenBrackets->push($bracket);
        // close parenthesis
        } elseif (in_array($bracket, $brackets)) {
            // too many closings
            if ($seenBrackets->count() == 0) {
                return false;
            }
            // last bracket is opener and is paired with current bracket
            $lastBracket = $seenBrackets->pop();
            if (!array_key_exists($lastBracket, $brackets) || $brackets[$lastBracket] != $bracket) {
                return false;
            }
        }
    }
    // too many openings
    return $seenBrackets->count() == 0;
}

/**
 * Calculates the depth of a query.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @return int
 */
function depth($query, $brackets) {
    $depth = 0;
    $max = 0;
    $qLen = strlen($query);
    // for char in query
    for ($i = 0; $i < $qLen; $i++) {
        $bracket = $query[$i];
        // open parenthesis
        if (array_key_exists($bracket, $brackets)) {
            $depth++;
            $max = max($max, $depth);
        // close parenthesis
        } elseif (in_array($bracket, $brackets)) {
            $depth--;
        }
    }
}


/**
 * Tests if a query can be parsed.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative Array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @param [Integer] $maxDepth The maximum depth of brackets the query can contain.
 * @param [Integer] $maxSubQueries The maximum number of subqueries (i.e. the number of $baseParser calls) the query can contain.
 */
function valideQuery($query, $brackets, $operators, $maxDepth, $maxSubqueries) {
    if (!validBrackets($query, $brackets)) {
        return false;
    }
    if (depth($query, $brackets) != $maxDepth) {
        return false;
    }
    return true;
}


?>