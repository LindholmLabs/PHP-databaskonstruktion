<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>Operations</h3>" . displayTable("Operation");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Operation")
            ->addColumn("OperationName")
            ->addColumn("StartDate")
            ->addColumn("EndDate", true)
            ->addColumn("SuccessRate", true)
            ->addDropdownColumn("GroupLeader", ['True', 'False']) #add GetColumnValues(table, column);
            ->addDropdownColumn("Incident", ['True', 'False']); #add GetCompositeKeyValues();

    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Create operation");

    include 'pageTemplate.php';
?>