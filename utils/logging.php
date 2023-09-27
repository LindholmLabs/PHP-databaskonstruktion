<?php
    function logg($message) {
        $timestamp = date("Y-m-d H:i:s");
        $logMessage = "{$timestamp} - {$message}\n";
        file_put_contents('utils/logs.txt', $logMessage, FILE_APPEND);
    }
?>