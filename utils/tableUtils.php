<?php

/**
 * Builds an html table from a given set of data.
 *
 * @param $data data which is used to build the table. The data must be an array containing associative arrays.
 * @param $classes additional classes for the table html element.
 */
function buildTable($data, $classes = "") {
    $counter = 0;

    echo "<table class='responsive-table striped ${classes}'>";

    foreach ($data as $row) {
        $columns = array_keys($row);

        // Print table header.
        if ($counter == 0) {
            echo "<thead>";
            echo "<tr>";
            for ($i = 0; $i < count($columns); $i++) {
                echo "<th>" . $columns[$i] . "</th>";
            }
            echo "</tr>";
            echo "</thead>";
        }

        // Print table content.
        echo "<tr>";
        for ($i = 0; $i < count($columns); $i++) {
            echo "<td>" . $row[$columns[$i]] . "</td>";
        }
        echo "</tr>";

        $counter++;
    }

    echo "</table>";
}