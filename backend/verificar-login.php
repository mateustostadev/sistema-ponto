<?php
session_start();

// Estabelecer conexão com o banco de dados
include_once('conexao.php'); // Substitua por seu arquivo de conexão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consulta para verificar as credenciais usando prepared statements
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['senha'])) {
            // Credenciais corretas: definir as informações do usuário na sessão e redirecionar para a página de sucesso
            $_SESSION['id'] = $row['id']; // Armazenar o ID do usuário na sessão
            $_SESSION['email'] = $email;
            $_SESSION['nome'] = $row['nome']; // Armazenar o nome do usuário na sessão
            $_SESSION['codigo_adm'] = $row['codigo_adm']; // Armazenar o código de administrador na sessão
            $_SESSION['logged_in'] = true; // Defina uma flag de login para indicar que o usuário está autenticado
            $_SESSION['regime'] = $row['regime'];
            
            // Insira detalhes do login na tabela log_login
            $usuario_id = $row['id'];
            $nome_usuario = $row['nome'];
            $acao = "Usuario Logado"; // Ação para registrar o login do usuário
            $sql_log = "INSERT INTO log_login (usuario_id, nome_usuario, acao) VALUES (?, ?, ?)";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->bind_param("iss", $usuario_id, $nome_usuario, $acao);
            $stmt_log->execute();

            header('Location: ../inicial.php'); // Redirecionar para a página de sucesso
            exit();
        } else {
            // Credenciais incorretas
            header('Location: login_erroPatch.php?error=credenciais_incorretas'); // Redirecionar para a página de login com feedback de erro
            exit();
        }
    } else {
        // Usuário não encontrado
        header('Location: login_erroPatch.php?error=usuario_nao_encontrado'); // Redirecionar para a página de login com feedback de erro
        exit();
    }
} else {
    // Redirecionar se o método de requisição não for POST
    header('Location: ../inicial.php'); // Redirecionar de volta para a página de login
    exit();
}

?>