<?php
    function logg($message) {
        global $console_logs;
        $console_logs[] = $message;
    }
    
    function outputConsoleLogs() {
        global $console_logs;
        echo '<script>';
        foreach ($console_logs as $msg) {
            echo "console.log(" . json_encode($msg) . ");";
        }
        echo '</script>';
    }
?>