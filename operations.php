<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $pageContent .= "<h3>Operations</h3>" . tableFactory::createTableWithRedirect("Operation", "operation.php", ["OperationName", "StartDate", "IncidentName", "IncidentNumber"]);

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Operation")
            ->setInsertHandler($handlerFactory->createHandler('Operation'))
            ->addColumn("OperationName")
            ->addColumn("StartDate")
            ->addColumn("EndDate", true)
            ->addColumn("SuccessRate", true)
            ->addDropdownColumn("GroupLeader", getColumnValues("GroupLeaders", "CodeName"))
            ->addDropdownColumn("Incident", GetCompositeKeyValues("Incident", ["IncidentName", "IncidentNumber"]));

    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create operation");

    include 'pageTemplate.php';
?>