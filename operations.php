<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $query = "SELECT * FROM Operation;";

    $pageContent .= "<h3>Operations</h3>" . tableFactory::createTableWithRedirect($query, "operation.php", ["OperationName", "StartDate", "IncidentName", "IncidentNumber"], "Operation");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Operation")
            ->setPostHandler($handlerFactory->createHandler('Operation'))
            ->addColumn("OperationName")
            ->addDateColumn("StartDate")
            ->addDateColumn("EndDate")
            ->addColumn("SuccessRate", true)
            ->addDropdownColumn("GroupLeader", getColumnValues("GroupLeaders", "CodeName"))
            ->addDropdownColumn("Incident", GetCompositeKeyValues("Incident", ["IncidentName", "IncidentNumber"]));

    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create operation");

    include 'utils/pageTemplate.php';
?>