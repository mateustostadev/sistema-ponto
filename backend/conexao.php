<?php
$servername = "seu-servidor";
$username = "seu-usuario";
$password = "sua-senha";
$dbname = "seu-banco";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>