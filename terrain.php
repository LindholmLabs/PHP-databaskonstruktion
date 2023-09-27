<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>Terrain</h3>" . tableFactory::createTable("Terrain");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Terrain")
            ->addColumn("TerrainCode")
            ->addColumn("TerrainName");

    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create Terrain");

    include 'pageTemplate.php';
?>