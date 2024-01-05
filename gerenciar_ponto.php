<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit();
}

// Verifica se o código_adm é igual a 18049
if (isset($_SESSION['codigo_adm']) && $_SESSION['codigo_adm'] !== '7415689lf') {
    header('Location: acesso_erro.php');
    exit();
}

// Estabelecer conexão com o banco de dados
include_once('backend/conexao.php'); // Substitua por seu arquivo de conexão

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_selecionada = $_POST['data_selecionada'];

    // Consultar os registros de ponto para a data selecionada
    $sql = "SELECT nome, data_hora_entrada, data_hora_almoco, data_hora_retorno, data_hora_saida
            FROM tabela_ponto
            INNER JOIN usuarios ON tabela_ponto.usuario_id = usuarios.id
            WHERE DATE(data_hora_entrada) = ?
            ORDER BY nome_fun, data_hora_entrada";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data_selecionada);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Inicializar a data para o dia atual se o formulário não foi enviado
    $data_selecionada = date('Y-m-d');

    // Consultar os registros de ponto para a data atual
    $sql = "SELECT nome, data_hora_entrada, data_hora_almoco, data_hora_retorno, data_hora_saida
            FROM tabela_ponto
            INNER JOIN usuarios ON tabela_ponto.usuario_id = usuarios.id
            WHERE DATE(data_hora_entrada) = ?
            ORDER BY nome_fun, data_hora_entrada";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data_selecionada);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Gerenciamento de Ponto</title>
    <style>
        body {
            font-family: 'Bahnschrift', sans-serif;
            background-color: #002d54;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #fff;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 50px;
        }

        label {
            margin-right: 10px;
            color: #fff;
            
        }

        input[type="date"] {
            padding: 8px;
            font-size: 16px;
            margin-right: 10px;
        }

        button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #fff;
            color: #008000;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            margin-right: 40px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            color: #000000;
        }

        th {
            background-color: #151fad;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        p {
            margin-top: 20px;
        }

        a {
            color: #fff;
        }


        h1 {
            color: #f0f0f0;
            /* Branco */
            margin-left: auto;
            margin-right: auto;


        }

        select,

        input[type="submit"] {
            background-color: #002d54;
            /* Verde */
            color: #002d54;
            cursor: pointer;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 190px;
            background-color: #f4f4f4;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            transition: transform 0.3s ease;
            overflow-y: auto;
            /* Isso permite que a barra lateral tenha uma rolagem vertical */
            max-height: 100%;
            /* Defina a altura máxima da barra lateral para ocupar 100% da altura da tela */
            scrollbar-width: thin;
            /* Define a largura da barra de rolagem para "thin" (fina) */
        }

        .show-sidebar-icon {
            position: fixed;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            background-color: #333;
            color: #fff;
            padding: 10px;
            cursor: pointer;
        }

        .sidebar-closed {
            transform: translateX(-190px);
            /* Oculta a barra lateral */
        }

        .user-info,
        .logout {
            padding: 20px;
        }

        .username {
            color: #000;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .username img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .logout {
            margin-top: 20px;
        }

        .content-container {
            text-align: center;
        }

        .main-content {
            
            display: center;
            width: 60%;
            /* ou qualquer outra largura desejada */
            text-align: center;
            /* centralizar o texto dentro da div */
            
        }

        .service-link {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            margin: 10px;
            background-color: #f4f4f4;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .service-link2 {
            display: flex;
            align-items: center;
            padding: 10px 10px;
            margin: 10px;
            background-color: #f4f4f4;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .service-link3 {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            margin: 5px;
            background-color: #f4f4f4;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .service-link4 {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            margin: 5px;
            background-color: #f4f4f4;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }


        .service-link:hover {
            background-color: #e0e0e0;
        }

        .service-link:active {
            animation: pulse 0.3s linear;
        }

        .service-link2:hover {
            background-color: #e0e0e0;
        }

        .service-link2:active {
            animation: pulse 0.3s linear;
        }

        .service-link3:hover {
            background-color: #e0e0e0;
        }

        .service-link3:active {
            animation: pulse 0.3s linear;
        }

        .service-link4:hover {
            background-color: #e0e0e0;
        }

        .service-link4:active {
            animation: pulse 0.3s linear;
        }

        .service-link img {
            margin-right: 10px;
            width: 30px;
            height: 30px;
            vertical-align: middle;
        }

        .service-link2 img {
            margin-right: 5px;
            width: 45px;
            height: 45px;
            vertical-align: middle;

        }

        .service-link3 img {
            margin-right: 5px;
            width: 35px;
            height: 35px;
            vertical-align: middle;
            filter: grayscale(100%) brightness(0%);
        }

        .service-link4 img {
            margin-right: 5px;
            width: 35px;
            height: 35px;
            vertical-align: middle;
            filter: grayscale(100%) brightness(0%);
        }

        .sub-links {
            display: none;
            align-items: center;
            padding: 5px 10px;
            /* Reduzindo o padding */
            margin: 5px;
            /* Diminuindo a margem */
            background-color: #f4f4f4;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 13px;
            /* Diminuindo o tamanho da fonte */
        }

        .sub-links a {
            text-decoration: none;
            /* Remove o sublinhado */
            color: #000;
            /* Define a cor do texto para preto */
        }

        .sub-links a:hover {
            background-color: #e0e0e0;
            color: #000;
            /* Cor do texto no hover para preto */
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 13px;
            /* Aumentar o tamanho da fonte */
            font-weight: bold;
            padding: 8px 0px;
            /* Ajustar o padding */
        }

        .sub-links a img {
            margin-right: 10px;
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }

        .sub-links a:active {
            animation: pulse 0.3s linear;
        }


        .sub-links2 {
            display: none;
            align-items: center;
            padding: 5px 10px;
            /* Reduzindo o padding */
            margin: 5px;
            /* Diminuindo a margem */
            background-color: #f4f4f4;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 13px;
            /* Diminuindo o tamanho da fonte */
        }

        .sub-links2 a {
            text-decoration: none;
            /* Remove o sublinhado */
            color: #000;
            /* Define a cor do texto para preto */

        }

        .sub-links2 a:hover {
            background-color: #e0e0e0;
            color: #000;
            /* Cor do texto no hover para preto */
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 13px;
            /* Aumentar o tamanho da fonte */
            font-weight: bold;
            padding: 8px 0px;
            /* Ajustar o padding */
        }


        .sub-links2 a img {
            margin-right: 10px;
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }

        .sub-links2 a:active {
            animation: pulse 0.3s linear;
        }

        /* Estilização da barra de rolagem no Chrome */
        .sidebar::-webkit-scrollbar {
            width: 3.5px;
            /* Largura da barra de rolagem */
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            /* Cor do fundo da área da barra de rolagem */
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #888;
            /* Cor do indicador da barra de rolagem */
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>

    <script>
        function toggleSubLinks(id) {
            const subLinks = document.getElementById(id);

            // Alternar a visibilidade dos sublinks
            if (subLinks.style.display === 'block') {
                subLinks.style.display = 'none';
            } else {
                subLinks.style.display = 'block';
            }
        }
    </script>

</head>

<body>
    <div class="main-content">
        <h2>Registros de Ponto por Usuário</h2>

        <form method="post" action="">
            <label for="data_selecionada">Selecione a data:</label>
            <input type="date" id="data_selecionada" name="data_selecionada" value="<?= $data_selecionada ?>" required>
            <button type="submit">Filtrar</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Entrada</th>
                        <th>Almoço</th>
                        <th>Retorno</th>
                        <th>Saída</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= $row['nome'] ?>
                            </td>
                            <td>
                                <?= $row['data_hora_entrada'] ? date('H:i', strtotime($row['data_hora_entrada'])) : 'Pendente' ?>
                            </td>
                            <td>
                                <?= $row['data_hora_almoco'] ? date('H:i', strtotime($row['data_hora_almoco'])) : 'Pendente' ?>
                            </td>
                            <td>
                                <?= $row['data_hora_retorno'] ? date('H:i', strtotime($row['data_hora_retorno'])) : 'Pendente' ?>
                            </td>
                            <td>
                                <?= $row['data_hora_saida'] ? date('H:i', strtotime($row['data_hora_saida'])) : 'Pendente' ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum registro encontrado para a data selecionada.</p>
        <?php endif; ?>
    </div>
    <i class="show-sidebar-icon fas fa-arrow-alt-circle-right" onclick="showSidebar()"></i>

    <div class="sidebar sidebar-closed" onmouseleave="handleMouseLeave()">
        <div class="sidebar">
            <div class="user-info">
                <div class="username">
                    <img src="imagens/usuario.png" alt="Ícone de Usuario">
                    <?php
                    if (isset($_SESSION['nome'])) {
                        echo $_SESSION['nome'];
                    } else {
                        echo "Usuário Desconhecido";
                    }
                    ?>
                </div>
            </div>

            <div class="service-link4" onclick="toggleSubLinks('adicionarRegistro')">
                <img src="imagens/digital.png" alt="Ícone de Adicionar Clientes">
                Registrar Ponto
            </div>
            <div id="adicionarRegistro" class="sub-links2">
                <a href="inicial.php">
                    <img src="imagens/digital.png" alt="Ícone de Registro">CLT / Estágio 6h
                </a><br><br>

                <a href="inicial2.php">
                    <img src="imagens/digital.png" alt="Ícone de Registro">Estágio 4h
                </a><br><br>

            </div>


            <div class="service-link4" onclick="toggleSubLinks('GerenciarRegistros')">
                <img src="imagens/gerenciar.png" alt="Ícone de Adicionar Clientes">
                Gerenciar Horas
            </div>
            <div id="GerenciarRegistros" class="sub-links2">
                <a href="gerenciar_meses.php">
                    <img src="imagens/gerenciar.png" alt="Ícone de Registro">CLT / Estágio 6h
                </a><br><br>

                <a href="gerenciar_meses2.php">
                    <img src="imagens/gerenciar.png" alt="Ícone de Registro">Estágio 4h
                </a><br><br>

            </div>

            <a class="service-link3" href="gerenciar_ponto.php">
                <img src="imagens/horarios.png" alt="Ícone de LF promotora">
                Gerenciar Ponto
            </a>

            <div class="service-link4" onclick="toggleSubLinks('GerenciarOcorrencias')">
                <img src="imagens/ocorrencia.png" alt="Ícone de Adicionar Clientes">
                Faltas e Atestados
            </div>
            <div id="GerenciarOcorrencias" class="sub-links2">
                <a href="registrar_cod.php">
                    <img src="imagens/ocorrencia.png" alt="Ícone de Registro">Registrar Faltas/Atestados
                </a><br><br>

                <a href="acompanhar_cod.php">
                    <img src="imagens/ocorrencia.png" alt="Ícone de Registro">Acompanhar Faltas/Atestados
                </a><br><br>
            
            </div>

            <div class="service-link4" onclick="toggleSubLinks('GerenciarRelatorios')">
                <img src="imagens/relatorio.png" alt="Ícone de Adicionar Clientes">
                Gerar Relatorio Mensal
            </div>
            <div id="GerenciarRelatorios" class="sub-links2">
                <a href="gerar_pdf.php">
                    <img src="imagens/relatorio.png" alt="Ícone de Registro">CLT / Estágio 6h
                </a><br><br>

                <a href="gerar_pdf2.php">
                    <img src="imagens/relatorio.png" alt="Ícone de Registro">Estágio 4h
                </a><br><br>
            
            </div>

            <div class="logout">
                <a class="service-link" href="backend/logoutPatch.php">
                    <img src="imagens/sair.png" alt="Ícone de Sair">
                    Sair
                </a>
            </div>
        </div>


        <div class="show-sidebar-icon" onmouseenter="handleMouseEnter()">
            <img src="arrasto.png" alt="Ícone de barra lateral" style="width: 30px; height: 30px;">
        </div>

        <script>
            let isMouseInSidebar = false;

            function toggleSidebar() {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');

                sidebar.classList.toggle('sidebar-closed');
                mainContent.classList.toggle('main-content-closed');
            }

            function handleMouseMove(event) {
                const x = event.clientX;
                const sidebarWidth = 160; // Largura da barra lateral

                if (x <= sidebarWidth || isMouseInSidebar) {
                    showSidebar();
                } else {
                    hideSidebar();
                }
            }

            function handleMouseEnter() {
                isMouseInSidebar = true;
                showSidebar();
            }

            function handleMouseLeave() {
                isMouseInSidebar = false;
                hideSidebar();
            }

            function showSidebar() {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');

                sidebar.classList.remove('sidebar-closed');
                mainContent.classList.remove('main-content-closed');
            }

            function hideSidebar() {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');

                sidebar.classList.add('sidebar-closed');
                mainContent.classList.add('main-content-closed');
            }
        </script>
</body>

</html>
