<?php
    function getColumnValues($table, $columnName) {
        $db = dbconnection::getInstance();
        $pdo = $db->getPdo();

        try {
            $query = "SELECT DISTINCT $columnName FROM $table";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
    
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $values = array_column($values, $columnName);
            
            return $values;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    function GetCompositeKeyValues($table, $columns) {
        $db = dbconnection::getInstance();
        $pdo = $db->getPdo();
    
        try {
            $query = "SELECT CONCAT_WS(', ', " . implode(", ", $columns) . ") AS composite_key FROM $table";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
    
            $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
            return $values;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
?>