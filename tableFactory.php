<?php
    class tableFactory {

        //create table
        public static function createTable($tableName) {
            $db = dbconnection::getInstance();
            $pdo = $db->getPdo();

            $output = "<div class='overflow-auto'><table class='table table-striped table-bordered'>";

            try {
                $firstRow = true;
                foreach($pdo->query('SELECT * FROM ' . $tableName . ';') AS $row) {
                    if($firstRow) {
                        $output .= "<thead class='thead-dark'><tr>";
                        foreach($row as $key => $value) {
                            if(!is_numeric($key)) { 
                                $output .= "<th>" . $key . "</th>";
                            }
                        }
                        $output .= "</tr></thead><tbody>";
                        $firstRow = false;
                    }
                    $output .= "<tr>";
                    foreach($row as $key => $value) {
                        if(!is_numeric($key)) { 
                            $output .= "<td>" . $value . "</td>";
                        }
                    }
                }
                $output .= "</tbody></table></div>";

                return $output;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return '';
            }
        }

        /**
         * Create table that redirects user to another address on click.
         * $tableName: The table to display.
         * $adress: The redirect adress. If user clicks a row, redirect to relative adress.
         * $queryColumns: The columns that should be queried in the redirect. 
         * 
         * Ex: CreateTableWithRedirect("Operation", "operationdetails.php", ["OperationName"]),
         * Clicking "Operation1" would result in a redirect to "./operationdetails.php/?OperationName=Operation1"
         */
        //
        public static function createTableWithRedirect($tableName, $address, $queryColumns) {
            $db = dbconnection::getInstance();
            $pdo = $db->getPdo();
        
            $output = "<div class='overflow-auto'><table class='table table-striped table-bordered table-hover'>";
        
            try {
                $firstRow = true;
                foreach($pdo->query('SELECT * FROM ' . $tableName . ';') AS $row) {
                    if($firstRow) {
                        $output .= "<thead class='thead-dark'><tr>";
                        foreach($row as $key => $value) {
                            if(!is_numeric($key)) { 
                                $output .= "<th>" . $key . "</th>";
                            }
                        }
                        $output .= "</tr></thead><tbody>";
                        $firstRow = false;
                    }
        
                    $queryParams = [];
                    foreach ($queryColumns as $col) {
                        $queryParams[] = $col . "=" . urlencode($row[$col]);
                    }
                    $redirectUrl = $address . "?" . implode("&", $queryParams);
        
                    $output .= "<tr onclick=\"window.location.href = '" . $redirectUrl . "'\" style='cursor: pointer;'>";
                    foreach($row as $key => $value) {
                        if(!is_numeric($key)) { 
                            $output .= "<td>" . $value . "</td>";
                        }
                    }
                    $output .= "</tr>";
                }
                $output .= "</tbody></table></div>";
        
                return $output;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return '';
            }
        }

        /**
         * Create a custom table, that displays any table generated from an sql query.
         */
        public static function createCustomTable($query) {
            $db = dbconnection::getInstance();
            $pdo = $db->getPdo();
        
            $output = "<div class='overflow-auto'><table class='table table-striped table-bordered'>";
        
            try {
                $firstRow = true;
                foreach($pdo->query($query) AS $row) {
                    if($firstRow) {
                        $output .= "<thead class='thead-dark'><tr>";
                        foreach($row as $key => $value) {
                            if(!is_numeric($key)) { 
                                $output .= "<th>" . $key . "</th>";
                            }
                        }
                        $output .= "</tr></thead><tbody>";
                        $firstRow = false;
                    }
                    $output .= "<tr>";
                    foreach($row as $key => $value) {
                        if(!is_numeric($key)) { 
                            $output .= "<td>" . $value . "</td>";
                        }
                    }
                    $output .= "</tr>";
                }
                $output .= "</tbody></table></div>";
        
                return $output;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return '';
            }
        }
    }
?>