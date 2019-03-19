<?php

/**
 * Builds an HTML table from a given set of data.
 *
 * @param $data array which is used to build the table. The data must be an array containing associative arrays.
 * @param $classes string classes for the table html element.
 */
function printHtmlTableFromAssocArray(array $data, string $classes = "")
{
    echo "<table class='responsive-table striped ${classes}'>";

    // Print table header
    $columns = array_keys($data[0]);
    echo "<thead>";
    echo "<tr>";
    for ($i = 0; $i < count($columns); $i++) {
        echo "<th>" . $columns[$i] . "</th>";
    }
    echo "</tr>";
    echo "</thead>";

    // Print table content
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $column)
            echo "<td>" . $column . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}