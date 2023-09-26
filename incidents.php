<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>FieldAgents</h3>" . displayTable("Incident");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Incident")
            ->addColumn("RegionName")
            ->addColumn("Location")
            ->addDropdownColumn("Incident", ['True', 'False']) #add GetCompositeKeyValues();
            ->addDropdownColumn("Terrain", getColumnValues("Terrain", "TerrainCode")); #add GetColumnValues(table, column);

    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create incident");

    include 'pageTemplate.php';
?>