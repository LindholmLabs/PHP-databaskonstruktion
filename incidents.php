<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $pageContent .= "<h3>Incident</h3>" . tableFactory::createTable("Incident");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Incident")
            ->setInsertHandler($handlerFactory->createHandler('Incident'))
            ->addColumn("RegionName")
            ->addColumn("Location")
            ->addColumn("IncidentName")
            ->addColumn("IncidentNumber")
            ->addDropdownColumn("Terrain", getColumnValues("Terrain", "TerrainCode"));

    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create incident");

    include 'pageTemplate.php';
?>