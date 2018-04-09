<?php
namespace hierarchicalSQL;

require 'util.php';

/**
 * Strict query format guidelines:
 * 
 * 
 * Relaxed query format guildlines:
 * Currently, these guidelines are the same as the strict query format guidelines. In future versions this requirement will be less strict.
 */

/**
 * If you're looking to parse a query, use queryToSQL().
 * Parses a query that follows the strict query guidelines into a SqlQuery.
 *
 * @param [String] $query The text of a query.
 * @param [String lambda(String)] $baseParser A lambda function that parses a basic query (i.e. no logic operators or brackets) into a SQL statement that contains 2 columns ('id' and 'score').
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @return SQLQuery object.
 */
function parseStrictQuery($query, $baseParser, $brackets, $operators) {

}

/**
 * Parses a query that follows the relaxed query guidelines into a hierarchical SQL statement.
 *
 * @param [String] $query The text of a query.
 * @param [String lambda(String)] $baseParser A lambda function that parses a basic query (i.e. no logic operators or brackets) into a SQL statement that contains 2 columns ('id' and 'score').
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @param [Integer] $maxDepth The maximum depth of brackets the query can contain.
 * @param [Integer] $maxSubQueries The maximum number of subqueries (i.e. the number of $baseParser calls) the query can contain.
 * @return [String] A SQL query if $query is valid, null otherwise.
 */
function queryToSQL($query, $baseParser, $brackets, $operators, $maxDepth, $maxSubQueries) {
    // add missing brackets/operators
    // TODO: add missing bracket/operator support
    // check if query is valid
    if (!validQuery($query, $brackets, $operators, $maxDepth, $maxSubQueries)) {
        return null;
    }
    // parse query
    return null;
}

?>