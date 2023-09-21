<?php include 'Components.php'; generateHead(); ?>

<body>
    <?php
        

        # --- HEAD --- #
        
        generateNavbar();

        # --- TABLES --- #
        echo "<div class=\"m-5\">";

        echo "<h2>Terrain</h2>";
        displayTable("Terrain");

        echo "</div>";

        # --- FOOTER --- #
        generateFooter();
    ?>
</body>

</html>