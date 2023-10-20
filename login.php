<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_SESSION['UserName'] = $_POST['UserName'];
        $_SESSION['Password'] = $_POST['Password'];

        header('Location: ' . 'index.php');
        exit();
    }

    $error = '';
    if (isset($_GET['error']) && $_GET['error'] == 'InvalidCredentials') {
        $error = 'Invalid login credentials';
    }

    $pageContent = "
    <div class='container'>
        <div class='row justify-content-center align-items-center'>
            <div class='col-md-4'>
                <div class='card'>
                    <div class='card-body'>
                        <form method='post'>
                            <div class='form-group'>
                                <label for='UserName'>Username</label>
                                <input type='text' class='form-control' id='UserName' name='UserName' placeholder='UserName' required>
                            </div>
                            <div class='form-group'>
                                <label for='Password'>Password</label>
                                <input type='password' class='form-control' id='Password' name='Password' placeholder='Password' required>
                            </div>
                            <button type='submit' class='btn btn-primary'>Login</button>
                        </form>
                        " . ($error ? "<div class='alert alert-danger mt-3' role='alert'>$error</div>" : "") . "
                    </div>
                </div>
            </div>
        </div>
    </div>";

    include 'utils/pageTemplate.php';
?>