<?php
$servername = "seu-servidor";
$username = "seu-usuario";
$password = "sua-senha";
$dbname = "seu-banco";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o usuário está autenticado (adapte conforme necessário)
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die(json_encode(['error' => 'Usuário não autenticado']));
}

// Consulta para obter os horários registrados do banco de dados
$query = "SELECT data_hora_entrada, data_hora_saida FROM tabela_ponto WHERE usuario_id = ? ORDER BY data_hora_entrada DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();

$result = $stmt->get_result();
$horarios = $result->fetch_assoc();

// Fechar a conexão com o banco de dados
$stmt->close();
$conn->close();

// Retornar os horários como JSON
echo json_encode($horarios);
?>
