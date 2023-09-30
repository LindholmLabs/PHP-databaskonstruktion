<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $searchQuery = $_GET['query'];
    $sqlQuery = isset($searchQuery) ? "SELECT * FROM ArchivedReport WHERE Title LIKE '{$searchQuery}%';" : "SELECT * FROM ArchivedReport;";

    logg($sqlQuery);

    $pageContent = '';

    $pageContent .= "<h3>Reports</h3>" . tableFactory::createCardTable("Report");
    
    $pageContent .= "<hr>";

    $pageContent .= "<h3>Archived Reports</h3>";

    $pageContent .= '<div class="container">';
    $pageContent .= '    <form class="form-inline" action="" method="GET">';
    $pageContent .= '        <div class="form-group">';
    $pageContent .= '            <input type="text" class="form-control me-1" id="query" name="query" placeholder="Search for old report" value="' . htmlspecialchars($_GET['query'] ?? '') . '">';
    $pageContent .= '        </div>';
    $pageContent .= '        <button type="submit" class="btn btn-primary">Search</button>';
    $pageContent .= '    </form>';
    $pageContent .= '</div>';
    
    $pageContent .= tableFactory::createCustomTable($sqlQuery);

    include 'utils/pageTemplate.php';
?>