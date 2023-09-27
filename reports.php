<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>Reports</h3>" . tableFactory::createCardTable("Report");
    
    $pageContent .= "<hr>";
    
    $pageContent .= "<h3>Archived Reports</h3>" . tableFactory::createTable("ArchivedReport");

    include 'utils/pageTemplate.php';
?>