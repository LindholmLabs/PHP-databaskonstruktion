<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>FieldAgents</h3>" . displayTable("FieldAgents");
    $pageContent .= "<h3>GroupLeaders</h3>" . displayTable("GroupLeaders");
    $pageContent .= "<h3>Managers</h3>" . displayTable("Managers");

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Agent")
            ->addColumn("FirstName")
            ->addColumn("LastName")
            ->addColumn("Salary", true)
            ->addDropdownColumn("IsFieldAgent", ['True', 'False'])
            ->addDropdownColumn("IsGroupLeader", ['False', 'True'])
            ->addDropdownColumn("IsManager", ['False', 'True']);

    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Hire agent");

    include 'pageTemplate.php';
?>