<?php
    require 'utils/imports.php';
    $db = dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $pdo = $db->getPdo();

    if (isset($_GET['table']) && !empty($_GET['table'])) {
        $table = $_GET['table'];
        $conditions = [];

        foreach ($_GET as $key => $value) {
            if ($key !== 'table' && $key !== 'redir') {
                if (isset($value)) {
                    $value = urldecode($value);
                    $conditions[] = "$key = '$value'";
                }
                
            }
        }

        if (count($conditions) > 0) {
            $whereClause = implode(' AND ', $conditions);
            $sql = "DELETE FROM $table WHERE $whereClause";

            $pdo->prepare($sql)->execute();

            if (isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo "Unable to redirect to the previous page.";
            }

        } else {
            echo "No keys provided to identify a unique row.";
        }
    } else {
        echo "Table parameter is missing.";
    }
?>