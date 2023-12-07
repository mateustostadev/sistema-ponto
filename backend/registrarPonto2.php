<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit();
}

// Configurar o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Conectar ao banco de dados (substitua os valores conforme necessário)
$servername = "seu-servidor";
$username = "seu-usuario";
$password = "sua-senha";
$dbname = "seu-banco";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se a entrada já foi registrada hoje
$sql = "SELECT * FROM tabela_ponto WHERE usuario_id = " . $_SESSION['id'] . " AND DATE(data_hora_entrada) = CURDATE()";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Já existe uma entrada registrada para hoje
    $row = $result->fetch_assoc();
    
    // Verificar se a saída já foi registrada para a entrada de hoje
    if ($row['data_hora_saida'] === NULL) {
        $ponto_id = $row['id'];
        
        // Atualizar a saída
        $sql_update = "UPDATE tabela_ponto SET data_hora_saida = NOW() WHERE id = $ponto_id";

        if ($conn->query($sql_update) === TRUE) {
            $response = array('success' => true);
        } else {
            $response = array('success' => false, 'message' => 'Erro ao atualizar saída: ' . $conn->error);
        }
    } else {
        $response = array('success' => false, 'message' => 'Saída já registrada para hoje.');
    }
} else {
    // Registre uma nova entrada para hoje
    $sql_insert = "INSERT INTO tabela_ponto (usuario_id, nome_fun, data_hora_entrada) VALUES (" . $_SESSION['id'] . ", '" . $_SESSION['nome'] . "', NOW())";

    if ($conn->query($sql_insert) === TRUE) {
        $response = array('success' => true);
    } else {
        $response = array('success' => false, 'message' => 'Erro ao registrar entrada: ' . $conn->error);
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
