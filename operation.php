<?php
    session_start();

    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();
    $updateFactory = new UpdateHandlerFactory();

    $pageContent = '';

    $operationName = urldecode($_GET['OperationName']);
    $startDate = urldecode($_GET['StartDate']);
    $incidentName = urldecode($_GET['IncidentName']);
    $incidentNumber = urldecode($_GET['IncidentNumber']);

    $queryCodeName = "SELECT CodeName AS Agents FROM OperatesIn 
        WHERE OperationName = '{$operationName}' 
        AND StartDate = '{$startDate}'
        AND IncidentName = '{$incidentName}'
        AND IncidentNumber = {$incidentNumber}";

    $pageContent .= "<h3>Agents in $operationName</h3>" . tableFactory::createCustomTable($queryCodeName);

    $addAgentModalBuilder = (new ModalBuilder())
        ->setModalId('insertModal')
        ->setTableName("OperatesIn")
        ->setPostHandler($handlerFactory->createHandler('OperatesIn'))
        ->addHiddenColumn("OperationName", $operationName)
        ->addHiddenColumn("StartDate", $startDate)
        ->addHiddenColumn("IncidentName", $incidentName)
        ->addHiddenColumn("IncidentNumber", $incidentNumber)
        ->addDropdownColumn("CodeName", getColumnValues("FieldAgents", "CodeName"));


    $addAgentModalBuilder->handleData();
    $pageContent .= $addAgentModalBuilder->build();
    $pageContent .= $addAgentModalBuilder->generateOpenButton("Add agent");

    $pageContent .= "<hr>";
    
    $queryGroupLeader = "SELECT GroupLeader FROM Operation 
        WHERE OperationName = '{$operationName}'
        AND StartDate = '{$startDate}'
        AND IncidentName = '{$incidentName}'
        AND IncidentNumber = {$incidentNumber}";

    $pageContent .= "<h3>Groupleader for $operationName</h3>" . tableFactory::createCustomTable($queryGroupLeader);

    $condition = "OperationName = '{$operationName}'
        AND StartDate = '{$startDate}'
        AND IncidentName = '{$incidentName}'
        AND IncidentNumber = {$incidentNumber}";

    $updateGroupLeaderModalBuilder = (new ModalBuilder())
        ->setModalId('updateModal')
        ->setTableName("Operation")
        ->setPostHandler($updateFactory->createHandler("Operation",  $condition))
        ->addDropdownColumn("GroupLeader", getColumnValues("GroupLeaders", "CodeName"));

    $updateGroupLeaderModalBuilder->handleData();
    $pageContent .= $updateGroupLeaderModalBuilder->build();
    $pageContent .= $updateGroupLeaderModalBuilder->generateOpenButton("Change GroupLeader");

    include 'utils/pageTemplate.php';
?>