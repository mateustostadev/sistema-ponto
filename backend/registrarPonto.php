<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit();
}

$servername = "seu-servidor";
$username = "seu-usuario";
$password = "sua-senha";
$dbname = "seu-banco";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_SESSION['nome'])) {
    $nomeUsuario = $_SESSION['nome'];
} else {
    $nomeUsuario = "Usuário Desconhecido";
}

$usuario_id = $_SESSION["id"];

$dataHoraAtual = date("Y-m-d H:i:s");
$dataHoraAtualBrasilia = date("Y-m-d H:i:s", strtotime("$dataHoraAtual America/Sao_Paulo"));

// Verificar se já existe um registro para o usuário no dia atual
$sqlVerificarEntrada = "SELECT * FROM tabela_ponto WHERE usuario_id = '$usuario_id' AND DATE(data_hora_entrada) = CURDATE()";
$resultVerificarEntrada = $conn->query($sqlVerificarEntrada);

if ($resultVerificarEntrada->num_rows > 0) {
    $registroEntrada = $resultVerificarEntrada->fetch_assoc();

    // Já existe um registro de entrada
    if ($registroEntrada['data_hora_almoco'] == null) {
        // Horário de almoço ainda não registrado, então registre o horário de almoço
        $sqlRegistroAlmoco = "UPDATE tabela_ponto SET data_hora_almoco = '$dataHoraAtualBrasilia' WHERE usuario_id = '$usuario_id' AND DATE(data_hora_entrada) = CURDATE()";

        if ($conn->query($sqlRegistroAlmoco) === TRUE) {
            echo json_encode(["success" => true, "message" => "Registro de horário de almoço realizado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    } elseif ($registroEntrada['data_hora_retorno'] == null) {
        // Horário de retorno ainda não registrado, então registre o horário de retorno
        $sqlRegistroRetorno = "UPDATE tabela_ponto SET data_hora_retorno = '$dataHoraAtualBrasilia' WHERE usuario_id = '$usuario_id' AND DATE(data_hora_entrada) = CURDATE()";

        if ($conn->query($sqlRegistroRetorno) === TRUE) {
            echo json_encode(["success" => true, "message" => "Registro de horário de retorno realizado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    } elseif ($registroEntrada['data_hora_saida'] == null) {
        // Horário de saída ainda não registrado, então registre o horário de saída
        $sqlRegistroSaida = "UPDATE tabela_ponto SET data_hora_saida = '$dataHoraAtualBrasilia' WHERE usuario_id = '$usuario_id' AND DATE(data_hora_entrada) = CURDATE()";

        if ($conn->query($sqlRegistroSaida) === TRUE) {
            echo json_encode(["success" => true, "message" => "Registro de horário de saída realizado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Limite de registros atingido!"]);
    }
} else {
    // Ainda não há registro de entrada, então registre o horário de entrada
    $sqlRegistroEntrada = "INSERT INTO tabela_ponto (nome_fun, usuario_id, data_hora_entrada) VALUES ('$nomeUsuario', '$usuario_id', '$dataHoraAtualBrasilia')";

    if ($conn->query($sqlRegistroEntrada) === TRUE) {
        echo json_encode(["success" => true, "message" => "Registro de horário de entrada realizado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "error" => "Erro ao registrar Ponto Eletrônico: " . $conn->error]);
    }
}

$conn->close();
?>
