<?php
    interface UpdateHandler {
        public function handleUpdate($data);
    }

    class UpdateHandlerFactory {
        public function createHandler($tableName, $condition) {
            switch ($tableName) {
                default:
                    return new GenericUpdateHandler($tableName, $condition);
            }
        }
    }

    class GenericUpdateHandler implements UpdateHandler {
        private $tableName;
        private $pdo;
        private $condition;

        public function __construct($tableName, $condition) {
            $this->condition = $condition;
            $this->tableName = $tableName;
            $db = dbconnection::getInstance();
            $this->pdo = $db->getPdo();
        }

        public function handleUpdate($data) {
            if (!isUpdate($data)) return;
            
            logg("Updating records in: " . $this->tableName);

            unset($data['tableName']);
            unset($data['operationType']);

            $setClause = [];
            foreach ($data as $column => $value) {
                $setClause[] = "$column = '{$value}'";
            }
            $setClause = implode(', ', $setClause);

            $sql = "UPDATE {$this->tableName} SET {$setClause} WHERE {$this->condition}";
            $stmt = $this->pdo->prepare($sql);

            if (!$stmt->execute()) {
                throw new Exception("Failed to update data in {$this->tableName}.");
            }

            RefreshTables();
        }
    }

    function isUpdate($postData) {
        return $postData["operationType"] === "UPDATE" ? true : false;
    }
?>