<?php
include '.config.php';

function connectToDatabase() {
    global $Mylocalhost, $Myusername, $Mypassword, $dbname;
    $conn = new mysqli($Mylocalhost, $Myusername, $Mypassword, $dbname);
    if ($conn->connect_error) {
        die('<div><h1>An error occurred while connecting to the database.</h1></div>');
    }
    return $conn;
}

function delayHome() {
    echo '
    <script type="text/javascript">
        setTimeout(function() {
            window.location.href = "page_home.php";
        }, 500);
    </script>
    ';
}

?>