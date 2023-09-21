<?php include 'Components.php'; generateHead(); ?>

<body>
    <?php
        

        # --- HEAD --- #
        
        generateNavbar();

        # --- TABLES --- #
        echo "<div class=\"m-5\">";

        echo "<h2>Operations</h2>";
        displayTable("Operation");

        echo "</div>";

        # --- FOOTER --- #
        generateFooter();
    ?>
</body>

</html>