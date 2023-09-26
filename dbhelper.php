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
?>