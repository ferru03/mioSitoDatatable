<?php
    $hostname = "172.17.0.1:3306";
    $dbname = "mydb";
    $user = "root";
    $password = "my-secret-pw";
    
    $mysqli = new mysqli($hostname, $user, $password, $dbname);

    if (!$mysqli) {
      die("Could not connect to MYSQL Server");
    }
?>
