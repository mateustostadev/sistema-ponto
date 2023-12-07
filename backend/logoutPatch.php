<?php
session_start();
include_once 'conexao.php'; // Substitua por seu arquivo de conexão

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $usuario_id = $_SESSION['id'];
    $nome_usuario = $_SESSION['nome'];
    $acao = "Usuario Deslogado";

    $sql_log = "INSERT INTO log_login (usuario_id, nome_usuario, acao) VALUES (?, ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("iss", $usuario_id, $nome_usuario, $acao);
    $stmt_log->execute();
}

session_unset();
session_destroy();
header('Location: ../index.html');
?>