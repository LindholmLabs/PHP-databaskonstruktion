<?php
    require 'utils/imports.php';
    
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $pageContent .= tableFactory::createCardTable("Terrain");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Terrain")
            ->setPostHandler($handlerFactory->createHandler('Terrain'))
            ->addHiddenColumn("TerrainCode", getTableCount('Terrain') + 1)
            ->addColumn("TerrainName"); 


    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create Terrain");

    include 'utils/pageTemplate.php';
?>