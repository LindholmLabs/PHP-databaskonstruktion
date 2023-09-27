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

    function getTableCount($table) {
        $db = dbconnection::getInstance();
        $pdo = $db->getPdo();
    
        try {
            $query = "SELECT COUNT(*) FROM $table";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }

    function RefreshTables() {
        $location = $_SERVER['PHP_SELF'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $location .= "?" . $_SERVER['QUERY_STRING'];
        }
        header("Location: " . $location);
        exit();
    }

    function HandleProcedure($postData) {
        if (isset($postData['OperationType']) && $postData['OperationType'] == 'EXECUTE') {
            
            try {
                $db = dbconnection::getInstance();
                $pdo = $db->getPdo();
    
                if (isset($postData['Function'])) {
                    $procedureName = $postData['Function'];
    
                    $dateCreated = $postData['DateCreated'];
                    $title = $postData['Title'];
    
                    $sql = "CALL {$procedureName}(:dateCreated, :title)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':dateCreated', $dateCreated);
                    $stmt->bindParam(':title', $title);
                        
                    $stmt->execute();
                }
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
    
            RefreshTables();
        }
    }
?>