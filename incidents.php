<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $query = "SELECT * FROM Incident;";

    $pageContent .= "<h3>Incidents</h3>" . tableFactory::createTableWithRedirect($query, "incident.php", ["IncidentName", "IncidentNumber"], "Incident");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Incident")
            ->setPostHandler($handlerFactory->createHandler('Incident'))
            ->addColumn("RegionName")
            ->addColumn("Location")
            ->addColumn("IncidentName")
            ->addColumn("IncidentNumber")
            ->addDropdownColumn("Terrain", getColumnValues("Terrain", "TerrainCode"));

    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create incident");

    include 'utils/pageTemplate.php';
?>