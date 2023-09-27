<?php
    require 'imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');

    $pageContent = '';

    $pageContent .= "<h3>FieldAgents</h3>" . tableFactory::createTable("FieldAgents");
    $pageContent .= "<h3>GroupLeaders</h3>" . tableFactory::createTable("GroupLeaders");
    $pageContent .= "<h3>Managers</h3>" . tableFactory::createTable("Managers");

    $handlerFactory = new InsertHandlerFactory();

    $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName("Agent")
            ->setInsertHandler($handlerFactory->createHandler('Agent'))
            ->addColumn("CodeName")
            ->addColumn("FirstName")
            ->addColumn("LastName")
            ->addColumn("Salary", true)
            ->addDropdownColumn("IsFieldAgent", ['True', 'False'])
            ->addDropdownColumn("IsGroupLeader", ['False', 'True'])
            ->addDropdownColumn("IsManager", ['False', 'True']);

    $pageContent .= $modalBuilder->handleData();
    $pageContent .= $modalBuilder->build();
    $pageContent .= $modalBuilder->generateOpenButton("Hire agent");

    include 'pageTemplate.php';
?>