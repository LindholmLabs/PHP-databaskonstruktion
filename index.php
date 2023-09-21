<?php include 'Components.php'; generateHead(); ?>

<body>
    <?php
        

        # --- HEAD --- #
        
        generateNavbar();

        # --- TABLES --- #
        echo "<div class=\"m-5\">";

        echo "<h2>FieldAgents</h2>";
        displayTable("FieldAgents");

        echo "<h2>Managers</h2>";
        displayTable("Managers");

        echo "<h2>GroupLeaders</h2>";
        displayTable("GroupLeaders");

        echo "</div>";


        # --- FOOTER --- #
        generateFooter();
    ?>
</body>

</html>