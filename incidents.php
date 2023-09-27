<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>FieldAgents</h3>" . tableFactory::createTable("Incident");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Incident")
            ->addColumn("RegionName")
            ->addColumn("Location")
            ->addDropdownColumn("Incident", GetCompositeKeyValues("Incident", ["IncidentName", "IncidentNumber"])) #add GetCompositeKeyValues(table, [column1, column2...]); separate using ", "
            ->addDropdownColumn("Terrain", getColumnValues("Terrain", "TerrainCode"));

    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create incident");

    include 'pageTemplate.php';
?>