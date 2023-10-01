<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $searchQuery = $_GET['query'] ?? null;

    $pageContent = '';

    $pageContent .= "<h3>Reports</h3>" . tableFactory::createCardTable("Report");
    
    $pageContent .= "<hr>";

    $pageContent .= "<h3>Archived Reports</h3>";

    $pageContent .= '<div class="mt-3 mb-3">';
    $pageContent .= '    <form class="form-inline" action="" method="GET">';
    $pageContent .= '        <div class="form-group">';
    $pageContent .= '            <input type="text" class="form-control" id="query" name="query" placeholder="Search for old report" value="' . htmlspecialchars($_GET['query'] ?? '') . '">';
    $pageContent .= '        </div>';
    $pageContent .= '        <button style="margin-left:0.5em;" type="submit" class="btn btn-primary">Search</button>';
    $pageContent .= '    </form>';
    $pageContent .= '</div>';

    $sqlQuery = $searchQuery ? "SELECT * FROM ArchivedReport WHERE Title LIKE '%{$searchQuery}%';" : "SELECT * FROM ArchivedReport;";
    $pageContent .= tableFactory::createCustomTable($sqlQuery, "ArchivedReport");

    include 'utils/pageTemplate.php';
?>