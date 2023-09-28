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
    
                        $placeholders = array_keys($postData);
                        $sql = "CALL {$procedureName}(" . implode(", ", array_map(function($p) {
                            return ":$p";
                        }, $placeholders)) . ")";
    
                        $stmt = $pdo->prepare($sql);
    
                        foreach ($postData as $key => $value) {
                            $stmt->bindParam(":$key", $postData[$key]);
                        }
    
                        $stmt->execute();
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