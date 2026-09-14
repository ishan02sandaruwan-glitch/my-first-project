<?php
$conn = new mysqli('localhost', 'root', '', 'my_db');

if($conn->connect_error) { 
    echo "Error"; 
} else { 
    echo "Connected successfully!"; 
}
?>