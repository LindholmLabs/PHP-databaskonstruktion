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

        echo "<h2>Incidents</h2>";
        displayTable("Incident");

        generateInsertFunction("Incident");

        echo "</div>";

        # --- FOOTER --- #
        generateFooter();
    ?>
</body>

</html>