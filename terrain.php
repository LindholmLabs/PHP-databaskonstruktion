<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $pageContent .= tableFactory::createCardTable("Terrain");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Terrain")
            ->setInsertHandler($handlerFactory->createHandler('Terrain'))
            ->addHiddenColumn("TerrainCode", getTableCount('Terrain') + 1)
            ->addColumn("TerrainName"); 


    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create Terrain");

    include 'utils/pageTemplate.php';
?>