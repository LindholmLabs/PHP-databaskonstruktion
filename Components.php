<?php
    function displayTable($tableName) {
        $pdo = new PDO('mysql:dbname=a22willi;host=localhost;', "root", '');

        // Start the table
        $output = "<table class='table table-striped table-bordered'>";

        // Display table headers
        $firstRow = true;
        foreach($pdo->query('SELECT * FROM ' . $tableName . ';') AS $row) {
            if($firstRow) {
                $output .= "<thead class='thead-dark'><tr>";
                foreach($row as $key => $value) {
                    if(!is_numeric($key)) { // Avoid displaying numeric indices
                        $output .= "<th>" . htmlspecialchars($key) . "</th>";
                    }
                }
                $output .= "</tr></thead><tbody>";
                $firstRow = false;
            }
            $output .= "<tr>";
            foreach($row as $key => $value) {
                if(!is_numeric($key)) { // Avoid displaying numeric indices
                    $output .= "<td>" . htmlspecialchars($value) . "</td>";
                }
            }
            $output .= "</tr>";
        }
        $output .= "</tbody></table>";

        // Return the generated table
        echo $output;
    }

    function generateNavbar() {
        $currentPage = basename($_SERVER['SCRIPT_NAME']);
        $pages = [
            'index.php' => 'Agents',
            'incidents.php' => 'Incidents',
            'operations.php' => 'Operations',
            'terrain.php' => 'Terrain'
        ];
    
        echo "<nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
                <a class='navbar-brand' href='#'>PUCKO-PORTAL</a>
                <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='navbarNav'>
                    <ul class='navbar-nav'>";
    
        foreach ($pages as $file => $name) {
            $active = ($currentPage == $file) ? 'active' : '';
            echo "<li class='nav-item $active'>
                        <a class='nav-link' href='./$file'>$name</a>
                    </li>";
        }
    
        echo "</ul>
                </div>
            </nav>";
    }

    function generateHead() {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>PUCKO-PORTAL</title>
            <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
            <link rel='stylesheet' type='text/css' href='stylesheet.css'>

            <script defer src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
            <script defer src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js'></script>
            <script defer src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        </head>
        ";
    }

    function generateFooter() {
        $year = date('Y');
        echo "
        <footer class='footer py-3 bg-dark text-white text-center'>
            <div class='container'>
                <p>&copy;{$year} PUCKO-PORTAL. All rights reserved.</p>
            </div>
        </footer>
        ";
    }

    function generateInsertFunction($tableName) {
        $pdo = new PDO('mysql:dbname=a22willi;host=localhost;', 'root', '');
    
        // Fetch column names
        $stmt = $pdo->prepare('DESCRIBE ' . $tableName);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Generate the button to open the modal
        $button = "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#insertModal'>
                        Add Data
                    </button>";
    
        // Generate the modal
        $modal = generateStylizedModal($columns);
    
        echo $button . $modal;
    }

    function generateStylizedModal($columns) {
        // Generate the modal
        $modal = "<div class='modal fade' id='insertModal' tabindex='-1' role='dialog' aria-labelledby='insertModalLabel' aria-hidden='true'>
            <div class='modal-dialog' role='document'>
                <div class='modal-content rounded'>
                    <div class='modal-header'>
                        <button type='button' class='close ml-0' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>";
    
        foreach ($columns as $column) {
            $modal .= "<div class='form-group'>
                            <label for='$column'>$column</label>
                            <input type='text' class='form-control' id='$column' name='$column' placeholder='$column' required>
                        </div>";
        }
    
        $modal .= "</div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' onclick='saveData()'>Save</button>
                    </div>
                </div>
            </div>
        </div>";
    
        return $modal;
    }
    
?>