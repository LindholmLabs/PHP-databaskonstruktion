<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();
    $procedureHandler = new ProcedureHandler();

    $incidentName = urldecode($_GET['IncidentName']);
    $incidentNumber = urldecode($_GET['IncidentNumber']);

    $pageContent = '';

    $queryOperations = "SELECT * FROM Operation WHERE IncidentName = '{$incidentName}' AND IncidentNumber = '{$incidentNumber}';";

    $pageContent .= "<h3>Operations in $incidentName</h3>" . tableFactory::createTableWithRedirect($queryOperations, "operation.php", ["OperationName", "StartDate", "IncidentName", "IncidentNumber"]);

    logg("Incident: " . "'{$incidentName}', '{$incidentNumber}'");

    $operationModalBuilder = (new ModalBuilder())
            ->setModalId('insertOperation')
            ->setTableName("Operation")
            ->setPostHandler($handlerFactory->createHandler('Operation'))
            ->addColumn("OperationName")
            ->addDateColumn("StartDate")
            ->addDateColumn("EndDate", true)
            ->addColumn("SuccessRate", true)
            ->addDropdownColumn("GroupLeader", getColumnValues("GroupLeaders", "CodeName"))
            ->addHiddenColumn("Incident", "{$incidentName}, {$incidentNumber}");

    $operationModalBuilder->handleData();
    $pageContent .= $operationModalBuilder->build();
    $pageContent .= $operationModalBuilder->generateOpenButton("Create Operation");

    $pageContent .= "<hr>";

    $queryReports = "SELECT * FROM Report WHERE IncidentName = '{$incidentName}' AND IncidentNumber = {$incidentNumber}";

    $pageContent .= "<h3>Reports</h3>" . tableFactory::createTableWithCallbackColumn($queryReports, 'archiveReport');

    function ArchiveReport($rowdata) {
        $dateCreated = isset($rowdata['DateCreated']) ? $rowdata['DateCreated'] : '';
        $title = isset($rowdata['Title']) ? $rowdata['Title'] : '';
    
        return "
        <form action='' method='POST' class='mb-0'>
            <input type='hidden' name='DateCreated' value='{$dateCreated}'>
            <input type='hidden' name='Title' value='{$title}'>
            <input type='hidden' name='OperationType' value='EXECUTE'>
            <input type='hidden' name='Function' value='ArchiveReport'>
            <button type='submit' class='btn btn-block btn-warning'>Archive</button>
        </form>";
    }

    //Run archive report procedure
    $procedureHandler->handlePostData($_POST);

    $WriteReportModal = (new ModalBuilder())
            ->setModalId('insertReport')
            ->setTableName("Report")
            ->setPostHandler($handlerFactory->createHandler('Report'))
            ->addColumn("Title")
            ->addDateColumn("DateCreated")
            ->addDropdownColumn("Author", getColumnValues("Agent", "CodeName"))
            ->addColumn("Content")
            ->addHiddenColumn("IncidentName", $incidentName)
            ->addHiddenColumn("IncidentNumber", $incidentNumber);

    $WriteReportModal->handleData();
    $pageContent .= $WriteReportModal->build();
    $pageContent .= $WriteReportModal->generateOpenButton("Write Report");

    $pageContent .= "<hr>";



    include 'utils/pageTemplate.php';
?>