<?php include 'Components.php'; generateHead(); ?>

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