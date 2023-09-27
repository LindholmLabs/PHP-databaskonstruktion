<?php
    require 'utils/imports.php';
    dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    $handlerFactory = new InsertHandlerFactory();

    $pageContent = '';

    $pageContent .= "<h3>FieldAgents</h3>" . tableFactory::createTable("FieldAgents");

    $attributeModalBuilder = (new ModalBuilder())
            ->setModalId('insertAttributeModal')
            ->setTableName("FieldAgentAttributes")
            ->setInsertHandler($handlerFactory->createHandler('FieldAgentAttributes'))
            ->addDropdownColumn("AgentCodeName", getColumnValues("FieldAgents", "CodeName"))
            ->addColumn("Specialty")
            ->addColumn("Competence");

    $pageContent .= $attributeModalBuilder->handleData();
    $pageContent .= $attributeModalBuilder->build();
    $pageContent .= $attributeModalBuilder->generateOpenButton("Set specialty");

    $pageContent .= "<hr>";

    $pageContent .= "<h3>GroupLeaders</h3>" . tableFactory::createTable("GroupLeaders");

    $pageContent .= "<hr>";

    $pageContent .= "<h3>Managers</h3>" . tableFactory::createTable("Managers");

    $agentModalBuilder = (new ModalBuilder())
            ->setModalId('insertAgentModal')
            ->setTableName("Agent")
            ->setInsertHandler($handlerFactory->createHandler('Agent'))
            ->addColumn("CodeName")
            ->addColumn("FirstName")
            ->addColumn("LastName")
            ->addColumn("Salary", true)
            ->addDropdownColumn("IsFieldAgent", ['True', 'False'])
            ->addDropdownColumn("IsGroupLeader", ['False', 'True'])
            ->addDropdownColumn("IsManager", ['False', 'True']);

    $agentModalBuilder->handleData();
    $pageContent .= $agentModalBuilder->build();
    $pageContent .= $agentModalBuilder->generateOpenButton("Hire agent");

    include 'utils/pageTemplate.php';
?>