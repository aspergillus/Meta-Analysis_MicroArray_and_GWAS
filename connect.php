<?php
    //echo "before server";
    $servername = "localhost";
    $username = "biomitra";
    $password = '!q@w3E$r';
    $dbname = "biomitra";


    //echo "trying connect";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (mysqli_connect_errno()){
    echo "error";
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    // echo "Connected successfully";
?>