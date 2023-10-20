<?php
    require 'utils/imports.php';
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $pageContent .= '<form action="" method="post" class="mt-3">';
    $pageContent .= '    <div class="form-group">';
    $pageContent .= '        <label for="startDate">Start Date:</label>';
    $pageContent .= '        <input type="date" class="form-control" id="startDate" name="startDate">';
    $pageContent .= '    </div>';
    $pageContent .= '    <div class="form-group">';
    $pageContent .= '        <label for="endDate">End Date:</label>';
    $pageContent .= '        <input type="date" class="form-control" id="endDate" name="endDate">';
    $pageContent .= '    </div>';
    $pageContent .= '    <input type="hidden" name="OperationType" value="filter">';
    $pageContent .= '    <button type="submit" class="btn btn-primary">Filter</button>';
    $pageContent .= '</form><hr>';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["OperationType"]) && $_POST["OperationType"] == "filter") {
        $startDate = $_POST["startDate"];
        $endDate = $_POST["endDate"];
        $query = "CALL GetOperationsInRange('$startDate', '$endDate');";
    } else {
        $query = "SELECT * FROM Operation;";
    }

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
