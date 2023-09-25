<?php
    require 'Database.php';
    include 'Components.php';

    Database::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    
    function getForeignKeys($table) {
        // Get the database instance and PDO object
        $db = Database::getInstance();
        $pdo = $db->getPdo();
    
        // Fetch the current database name
        $currentDatabase = $pdo->query('SELECT DATABASE()')->fetchColumn();
    
        // Prepare and execute the statement to fetch foreign keys
        $stmt = $pdo->prepare("
            SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = :databaseName 
            AND TABLE_NAME = :tableName 
            AND REFERENCED_TABLE_NAME IS NOT NULL;
        ");
        
        $stmt->bindParam(':databaseName', $currentDatabase);
        $stmt->bindParam(':tableName', $table);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach (getForeignKeys('Operation') AS $op) {
        print_r($op);
    }
?>