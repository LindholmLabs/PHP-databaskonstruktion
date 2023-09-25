<?php
    require 'Database.php';
    include 'Components.php'; 

    Database::getInstance('mysql', 'a22willi', 'root', 'Safiren1');
    generateHead(); 
?>

<body>
    <?php
        # --- HEAD --- #
        generateNavbar();

        # --- TABLES --- #
        echo "<div class=\"m-5\">";

        echo "<h2>Operations</h2>";
        displayTable("Operation");
        generateInsertFunction("Operation");

        echo "</div>";

        # --- FOOTER --- #
        generateFooter();
    ?>
</body>

</html>