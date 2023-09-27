<?php
    session_start();

    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $operationName = urldecode($_GET['OperationName']);
    $startDate = urldecode($_GET['StartDate']);
    $incidentName = urldecode($_GET['IncidentName']);
    $incidentNumber = urldecode($_GET['IncidentNumber']);

    $query = "SELECT CodeName FROM OperatesIn 
              WHERE OperationName = '{$operationName}' 
              AND StartDate = '{$startDate}'
              AND IncidentName = '{$incidentName}'
              AND IncidentNumber = {$incidentNumber}";

    logg($query);

    $pageContent .= "<h3>Agents in $operationName</h3>" . tableFactory::createCustomTable($query);

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("OperatesIn")
            ->setInsertHandler($handlerFactory->createHandler('OperatesIn'))
            ->addHiddenColumn("OperationName", $operationName)
            ->addHiddenColumn("StartDate", $startDate)
            ->addHiddenColumn("IncidentName", $incidentName)
            ->addHiddenColumn("IncidentNumber", $incidentNumber)
            ->addDropdownColumn("CodeName", getColumnValues("FieldAgents", "CodeName"));


    $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Add agent");

    include 'pageTemplate.php';
?>