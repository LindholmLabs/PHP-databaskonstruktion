<?php
    include 'modalBuilder.php';

    function displayTable($tableName) {
        $db = Database::getInstance();
        $pdo = $db->getPdo();

        // Start the table
        $output = "<table class='table table-striped table-bordered'>";

        // Display table headers
        $firstRow = true;
        foreach($pdo->query('SELECT * FROM ' . $tableName . ';') AS $row) {
            if($firstRow) {
                $output .= "<thead class='thead-dark'><tr>";
                foreach($row as $key => $value) {
                    if(!is_numeric($key)) { // Avoid displaying numeric indices
                        $output .= "<th>" . safeHmlspecialchars($key) . "</th>";
                    }
                }
                $output .= "</tr></thead><tbody>";
                $firstRow = false;
            }
            $output .= "<tr>";
            foreach($row as $key => $value) {
                if(!is_numeric($key)) { // Avoid displaying numeric indices
                    $output .= "<td>" . safeHmlspecialchars($value) . "</td>";
                }
            }
            $output .= "</tr>";
        }
        $output .= "</tbody></table>";

        // Return the generated table
        echo $output;
    }

    function safeHmlspecialchars($value) {
        return empty($value) ? "" : htmlspecialchars($value);
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
        <br><br><br><br>
        <footer class='footer py-3 bg-dark text-white text-center'>
            <div class='container'>
                <p>&copy;{$year} PUCKO-PORTAL. All rights reserved.</p>
            </div>
        </footer>
        ";
    }

    function hasInsertPrivilege($tableName) {
        // Get the PDO instance from the Database singleton
        $db = Database::getInstance();
        $pdo = $db->getPdo();
    
        // Query the database to get the grants for the current user
        $sqlCheckPrivileges = "SHOW GRANTS FOR CURRENT_USER()";
        $stmtCheck = $pdo->prepare($sqlCheckPrivileges);
        $stmtCheck->execute();
        $grants = $stmtCheck->fetchAll(PDO::FETCH_COLUMN);
    
        // Check if any of the grants include the INSERT privilege for the specified table or for all tables
        foreach ($grants as $grant) {
            if (strpos($grant, "GRANT INSERT ON *.*") !== false) {
                return true; // Global insert privilege
            }
            if (strpos($grant, "GRANT INSERT ON `{$tableName}`") !== false || strpos($grant, "GRANT INSERT ON `$tableName`.*") !== false) {
                return true; // Specific table or database-wide insert privilege
            }
        }
    
        return false;
    }

    function insertDataIntoTable($pdo, $tableName) {
        $columns = array_keys($_POST);
        unset($columns[array_search('tableName', $columns)]); // Exclude tableName from columns
    
        $placeholders = rtrim(str_repeat('?,', count($columns)), ',');
        $sql = "INSERT INTO $tableName (" . implode(',', $columns) . ") VALUES ($placeholders)";
    
        $stmt = $pdo->prepare($sql);
        $values = array_values($_POST);
        array_pop($values); // Exclude tableName from values
        $stmt->execute($values);
    }
    
    function generateInsertFunction($tableName) {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
    
        if (isFormSubmitted() && isTableNameSet()) {
            insertDataIntoTable($pdo, $tableName);
            redirectToCurrentPage();
        }
    
        $columns = fetchTableColumns($pdo, $tableName);
        
        $modalBuilder = (new ModalBuilder())
            ->setModalId('insertModal')
            ->setTableName($tableName);
        
        foreach ($columns as $column) {
            $modalBuilder->addColumn($column);
        }
    
        echo $modalBuilder->generateOpenButton("Add Data");
        echo $modalBuilder->build();
    }
    
    function isFormSubmitted() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    function isTableNameSet() {
        return isset($_POST['tableName']);
    }
    
    function redirectToCurrentPage() {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    function fetchTableColumns($pdo, $tableName) {
        $stmt = $pdo->prepare('DESCRIBE ' . $tableName);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
?>