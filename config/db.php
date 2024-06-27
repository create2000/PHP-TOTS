<?php 
     $conn = mysqli_connect("localhost","shawn","test1234","mydatabase");


    if(!$conn) {
        echo "Connection error" . mysqli_connect_error();
    }
?>


