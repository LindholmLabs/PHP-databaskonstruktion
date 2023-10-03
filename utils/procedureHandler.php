<?php
    class ProcedureHandler implements PostHandler {
        function handlePostData($postData) {
            if (isset($postData['OperationType']) && $postData['OperationType'] == 'EXECUTE') {
                try {
                    $db = dbconnection::getInstance();
                    $pdo = $db->getPdo();
        
                    if (isset($postData['Function'])) {
    
                        $procedureName = $postData['Function'];
                        unset($postData['Function'], $postData['OperationType']);
                    
                        $questionMarks = array_fill(0, count($postData), '?');
                        $sql = "CALL {$procedureName}(" . implode(", ", $questionMarks) . ")";
                    
                        $stmt = $pdo->prepare($sql);
                    
                        if (!$stmt->execute(array_values($postData)));
                    }
                } catch(PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
        
                RefreshTables();
            }
        }

        public function getOperationType() {
            return "INSERT";
        }
    }    
?>