<?php
    session_start();

    logg("starting user verification.");
    if (isset($_SESSION['UserName']) && isset($_SESSION['Password'])) {
        try {
            dbconnection::getInstance('mysql', 'a22willi', $_SESSION['UserName'], $_SESSION['Password']);
        } catch (PDOException $e){
            session_destroy();
            header('Location: login.php?error=InvalidCredentials');
            exit();
        }
    } else {
        logg("User not logged in, redirecting to login page.");
        header('Location: login.php');
    }
?>