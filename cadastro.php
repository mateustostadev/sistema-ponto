<?php
session_start();
include_once 'backend/conexao.php';

$mensagem = "";
$tipo_mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $eh_administrador = isset($_POST['eh_administrador']) ? $_POST['eh_administrador'] : 'off';
    $codigo_adm = $_POST['codigo_adm'];
    $codigo_fun = $_POST['codigo_fun'];
    $regime = $_POST['regime'];

    if ($codigo_fun !== '18049') {
        $mensagem = "Código de funcionário incorreto. Entre em contato com o setor de TI.";
        $tipo_mensagem = "erro";
    } elseif ($eh_administrador === 'on' && $codigo_adm !== '7415689lf') {
        $mensagem = "Código de administrador incorreto. Entre em contato com o setor de TI.";
        $tipo_mensagem = "erro";
    } else {
        $verificar_email = "SELECT email FROM usuarios WHERE email = '$email'";
        $resultado = $conn->query($verificar_email);

        if ($resultado->num_rows > 0) {
            $mensagem = "Já existe um usuário com este email!";
            $tipo_mensagem = "erro";
        } else {
            $sql = "INSERT INTO usuarios (nome, email, senha, codigo_adm, regime) VALUES ('$nome', '$email', '$senha', '$codigo_adm', '$regime')";

            if ($conn->query($sql) === TRUE) {
                $mensagem = "Usuário cadastrado com sucesso!";
                $tipo_mensagem = "sucesso";
            } else {
                $mensagem = "Erro ao cadastrar o usuário: " . $conn->error;
                $tipo_mensagem = "erro";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="frontend/estiloCadastro.css">
</head>

<body>
    <!-- Mensagem flutuante para sucesso -->
    <div class="mensagem-flutuante sucesso" style="<?php if (!empty($mensagem) && $tipo_mensagem === 'sucesso')
        echo 'display: block;'; ?>">
        <?php echo $mensagem; ?>
    </div>

    <!-- Mensagem flutuante para erro -->
    <div class="mensagem-flutuante erro" style="<?php if (!empty($mensagem) && $tipo_mensagem === 'erro')
        echo 'display: block;'; ?>">
        <?php echo $mensagem; ?>
    </div>

    <div class="sidebar">
        <h1>Cadastro de Usuário</h1>

        <form method="post" action="">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" required><br><br>
            <label for="email">E-mail:</label>
            <input type="text" name="email" required><br><br>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" required><br><br>
            <label for="codigo_fun">Código Funcionario:</label>
            <input type="text" name="codigo_fun" required><br><br>
            <label for="regime">Regime de Trabalho:</label>
            <select name="regime" id="regime">
                <option value="8">CLT / PJ / Estagio 6h</option>
                <option value="4">Estágio 4h</option>
            </select>
            <label for="eh_administrador">Administrador</label>
            <input type="checkbox" name="eh_administrador" id="eh_administrador">
            <div id="codigo_adm_field" style="display: none;">
                <label for="codigo_adm">Código do Administrador:</label>
                <input type="text" name="codigo_adm">
            </div>
            <input type="submit" value="Cadastrar">
        </form>

        <div class="back-button">
            <a class="button" href="index.html">Faça Login</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mostra o campo do código do administrador se a caixa de seleção for marcada
            const checkbox = document.getElementById('eh_administrador');
            checkbox.addEventListener('change', function () {
                const codigoAdmField = document.getElementById('codigo_adm_field');
                if (checkbox.checked) {
                    codigoAdmField.style.display = 'block';
                } else {
                    codigoAdmField.style.display = 'none';
                }
            });

            // Exibe a mensagem flutuante por um tempo e, em seguida, a oculta
            var mensagemFlutuante = document.querySelectorAll('.mensagem-flutuante');
            mensagemFlutuante.forEach(function (mensagem) {
                if (mensagem.style.display === 'block') {
                    setTimeout(function () {
                        mensagem.style.display = 'none';
                    }, 3000);
                }
            });
        });
    </script>
</body>

</html>
