<?php
    class tableFactory {

        //create table
        public static function createTable($query) {
            $sql = "SELECT * FROM $query;";

            $tableBuilder = (new TableBuilder($sql));
            return $tableBuilder->buildTable();
        }

        /**
         * Create table that redirects user to another address on click.
         * $query: The query to be displayed.
         * $adress: The redirect adress. If user clicks a row, redirect to relative adress.
         * $queryColumns: The columns that should be queried in the redirect. 
         * 
         * Ex: CreateTableWithRedirect("Operation", "operationdetails.php", ["OperationName"]),
         * Clicking "Operation1" would result in a redirect to "./operationdetails.php/?OperationName=Operation1"
         */
        public static function createTableWithRedirect($query, $address, $queryColumns) {
            $tableBuilder = (new TableBuilder($query));
            return $tableBuilder->setRedirect($address, $queryColumns)->buildTable();
        }

        /**
         * Create a custom table, that displays any table generated from an sql query.
         */
        public static function createCustomTable($query) {
            $tableBuilder = (new TableBuilder($query));
            return $tableBuilder->buildTable();
        }

        /**
         * Generate a card layout instead of a table layout. 
         * Only suitable for smaller datatables.
         */
        public static function createCardTable($tableName) {
            $db = dbconnection::getInstance();
            $pdo = $db->getPdo();
        
            $output = "<div class='card-columns'>";
        
            try {
                $headers = [];
                $firstRow = true;
                foreach($pdo->query('SELECT * FROM ' . $tableName . ';') AS $row) {
                    if($firstRow) {
                        foreach($row as $key => $value) {
                            if(!is_numeric($key)) { 
                                $headers[] = $key;
                            }
                        }
                        $firstRow = false;
                    }
        
                    $output .= "<div class='card'>";
                    $output .= "<div class='card-header'>$tableName</div>";
                    $output .= "<div class='card-body'>";
                    
                    for($i = 0; $i < count($headers); $i++) {
                        if(isset($row[$headers[$i]]) && !is_numeric($headers[$i])) {
                            $output .= "<p class='card-text'>" . $headers[$i] . ": " . $row[$headers[$i]] . "</p>";
                        }
                    }
                    
                    $output .= "</div>";
                    $output .= "</div>";
                }
                $output .= "</div>";
        
                return $output;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return '';
            }
        }

        /**
         * Specify a callback function which will be called when a user presses a row.
         * @param $function = the callback function (function foo($row))
         */
        public static function createTableWithCallbackColumn($query, $function) {
            $tableBuilder = (new TableBuilder($query));
            return $tableBuilder->addActionColumn($function)->buildTable();
        }
    }
?>