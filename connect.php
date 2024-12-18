<?php

$host="localhost";
$user="root";
$pass=""; // i think no need to keep it safe
$db="login";
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
}
?>