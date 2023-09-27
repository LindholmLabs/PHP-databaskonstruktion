<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $pageContent .= "<h3>Terrain</h3>" . tableFactory::createTable("Terrain");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Terrain")
            ->setInsertHandler($handlerFactory->createHandler('Terrain'))
            ->addColumn("TerrainCode")
            ->addColumn("TerrainName");


    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create Terrain");

    include 'utils/pageTemplate.php';
?>