<?php
    $pages = [
        'index.php' => 'Agents',
        'incidents.php' => 'Incidents',
        'operations.php' => 'Operations',
        'terrain.php' => 'Terrain'
    ];
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUCKO-PORTAL</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script defer src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script defer src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">PUCKO-PORTAL</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav"><li class="nav-item ">
                <?php 
                    $currentPage = basename($_SERVER['SCRIPT_NAME']);
                    foreach ($pages as $file => $name) {
                        $active = ($currentPage == $file) ? 'active' : '';
                        echo "<li class='nav-item $active'>
                            <a class='nav-link' href='./$file'>$name</a>
                        </li>";
                    }
                ?>
            </ul>
        </div>
    </nav>

    <br><br><br>
    
    <div class='m-md-5 m-sm-3 m-1'>
        <?php echo $pageContent; ?>
    </div>
    
    <br><br><br><br><br><br>

    <footer class="footer py-3 bg-dark text-white text-center fixed-bottom">
        <div class="container">
            <p>&copy;<?php echo date('Y');?> PUCKO-PORTAL. All rights reserved.</p>
        </div>
    </footer>
</body>