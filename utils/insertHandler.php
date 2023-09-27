<?php
    interface InsertHandler {
        public function handleInsert($data);
    }

    class InsertHandlerFactory {
        public function createHandler($tableName) {
            switch ($tableName) {
                case 'Operation':
                    return new OperationInsertHandler($tableName);
                default:
                    return new GenericInsertHandler($tableName);
            }
        }
    }
    
    class OperationInsertHandler implements InsertHandler {
        private $tableName;
        private $pdo;

        public function __construct($tableName) {
            $this->tableName = $tableName;
            $db = dbconnection::getInstance();
            $this->pdo = $db->getPdo();
        }

        public function handleInsert($data) {
            if (!isInsert($data)) return;

            logg("Inserting into: " . $this->tableName);

            $operationName = $data["OperationName"];
            $startDate = $data["StartDate"];
            $endDate = $data["EndDate"];
            $successRate = (bool) $data["SuccessRate"] ? true : false;
            $groupLeader = $data["GroupLeader"];
            $incident = $data["Incident"];
            list($incidentName, $incidentNumber) = explode(", ", $incident);

            $query = "INSERT INTO {$this->tableName} (OperationName, StartDate, EndDate, SuccessRate, GroupLeader, IncidentName, IncidentNumber) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(1, $operationName, PDO::PARAM_STR);
            $stmt->bindParam(2, $startDate, PDO::PARAM_STR);
            $stmt->bindParam(3, $endDate, PDO::PARAM_STR);
            $stmt->bindParam(4, $successRate, PDO::PARAM_BOOL);
            $stmt->bindParam(5, $groupLeader, PDO::PARAM_STR);
            $stmt->bindParam(6, $incidentName, PDO::PARAM_STR);
            $stmt->bindParam(7, $incidentNumber, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Failed to insert data into {$this->tableName}.");
            }

            RefreshTables();
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
            if (!isInsert($data)) return;

            logg("Inserting into: " . $this->tableName);

            unset($data['tableName']);
            unset($data['operationType']);

            $data = $this->convertStringsToAppropriateTypes($data);

            $columns = array_keys($data);
            $placeholders = array_fill(0, count($columns), '?');
            
            $sql = "INSERT INTO {$this->tableName} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->pdo->prepare($sql);

            if (!$stmt->execute(array_values($data))) {
                throw new Exception("Failed to insert data into {$this->tableName}.");
            }

            RefreshTables();
        }

        private function convertStringsToAppropriateTypes(array $data): array {
            foreach ($data as $key => $value) {
                if (strtolower($value) === 'true') {
                    $data[$key] = 1;
                } elseif (strtolower($value) === 'false') {
                    $data[$key] = 0;
                } elseif (is_string($value) && ctype_digit($value)) {
                    $data[$key] = (int) $value;
                }
            }
            return $data;
        }
    }

    function isInsert($postData) {
        return $postData["operationType"] === "INSERT" ? true : false;
    }
?>