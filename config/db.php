<?php 
     $conn = mysqli_connect("localhost","shawn","test1234","hotel_management");


    if(!$conn) {
        echo "Connection error" . mysqli_connect_error();
    }
?>


