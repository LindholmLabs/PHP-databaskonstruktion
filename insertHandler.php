<?php
    interface InsertHandler {
        public function handleInsert($data);
    }

    class InsertHandlerFactory {
        public function createHandler($tableName) {
            switch ($tableName) {
                case 'Agent':
                    return new GenericInsertHandler($tableName);
                case 'Incident':
                    return new IncidentInsertHandler();
                default:
                    throw new Exception("No handler found for table: $tableName");
            }
        }
    }
    
    class OrdersInsertHandler implements InsertHandler {
        public function handleInsert($data) {
            // Logic to insert data into the 'orders' table
        }
    }

    class GenericInsertHandler implements InsertHandler {
        private $tableName;
        private $pdo;

        public function __construct($tableName) {
            $this->tableName = $tableName;
            $db = dbconnection::getInstance();
            $this->pdo = $db->getPdo();
        }

        public function handleInsert($data) { 
            unset($data['tableName']);

            $data = $this->convertBooleanStringsToInt($data);

            $columns = array_keys($data);
            $placeholders = array_fill(0, count($columns), '?');
            
            $sql = "INSERT INTO {$this->tableName} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            
            if (!$stmt->execute(array_values($data))) {
                throw new Exception("Failed to insert data into {$this->tableName}.");
            }
        }

        private function convertBooleanStringsToInt(array $data): array {
            foreach ($data as $key => $value) {
                if (strtolower($value) === 'true') {
                    $data[$key] = 1;
                } elseif (strtolower($value) === 'false') {
                    $data[$key] = 0;
                }
            }
            return $data;
        }
    }
?>