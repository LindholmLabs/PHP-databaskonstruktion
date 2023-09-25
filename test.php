<?php

    
    include 'Components.php';
    include 'modalBuilder.php';

    generateHead();
    echo '<body>';
    $modalBuilder1 = (new ModalBuilder())
        ->setModalId('modal1')
        ->setTableName('SampleTable1')
        ->addColumn('FirstName')
        ->addColumn('LastName')
        ->addDropdownColumn('Foreign', ['hej', 'test', 'hello', 'asddfsdf']);

    $modalBuilder2 = (new ModalBuilder())
        ->setModalId('modal2')
        ->setTableName('SampleTable2')
        ->addColumn('Age');

    $modalBuilder1->generateOpenButton("Open Modal 1");
    $modalBuilder1->build();

    $modalBuilder2->generateOpenButton("Open Modal 2");
    $modalBuilder2->build();

    echo '</body>';
?>