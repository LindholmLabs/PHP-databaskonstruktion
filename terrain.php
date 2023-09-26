<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>Terrain</h3>" . displayTable("Terrain");

    include 'pageTemplate.php';
?>