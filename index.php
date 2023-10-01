<?php
        require 'utils/imports.php';
        dbconnection::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
        $handlerFactory = new InsertHandlerFactory();

        $pageContent = '';

        $pageContent .= "<h3>Remove agent</h3>";
        $pageContent .= '<div class="mt-3 mb-3">';
        $pageContent .= '    <form class="form-inline" method="GET" action="delete.php">';
        $pageContent .= '        <input type="hidden" name="table" value="Agent">';
        $pageContent .= '        <div class="form-group">';
        $pageContent .= '            <input type="text" class="form-control" id="CodeName" name="CodeName" placeholder="Agent codename" value="' . htmlspecialchars($_GET['CodeName'] ?? '') . '">';
        $pageContent .= '        </div>';
        $pageContent .= '        <button style="margin-left:0.5em;" type="submit" class="btn btn-danger">Remove Agent</button>';
        $pageContent .= '    </form>';
        $pageContent .= '</div><hr>';

        $pageContent .= "<h3>FieldAgents</h3>" . tableFactory::createTable("FieldAgents");

        $attributeModalBuilder = (new ModalBuilder())
                ->setModalId('insertAttributeModal')
                ->setTableName("FieldAgentAttributes")
                ->setPostHandler($handlerFactory->createHandler('FieldAgentAttributes'))
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
                ->setPostHandler($handlerFactory->createHandler('Agent'))
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