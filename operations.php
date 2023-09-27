<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>Operations</h3>" . tableFactory::createTable("Operation");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Operation")
            ->addColumn("OperationName")
            ->addColumn("StartDate")
            ->addColumn("EndDate", true)
            ->addColumn("SuccessRate", true)
            ->addDropdownColumn("GroupLeader", getColumnValues("GroupLeaders", "CodeName"))
            ->addDropdownColumn("Incident", ['True', 'False']); #add GetCompositeKeyValues();

    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create operation");

    include 'pageTemplate.php';
?>