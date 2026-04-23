<?php
$conn = new mySQLi('MySQL-8.4', 'root', '', 'urban');

if ($conn->connect_error) {  
    echo("Ошибка: " . $conn->connect_error);  
} 
?>