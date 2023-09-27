<?php
    function displayTable($tableName) {
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
                $output .= "</tr>";
            }
            $output .= "</tbody></table></div>";
    
            return $output;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return '';
        }
    }
?>