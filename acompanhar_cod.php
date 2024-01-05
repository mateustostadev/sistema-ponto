<?php
session_start();
include_once('backend/conexao.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit();
}

$sqlUsuarios = "SELECT DISTINCT u.id, u.nome FROM atestado_falta af
                JOIN usuarios u ON af.usuario_id = u.id";
$resultUsuarios = $conn->query($sqlUsuarios);

if (isset($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];

    // Consulta para obter os registros de atestado_falta com filtro de data
    $sql = "SELECT codigo_log, data_inicial, data_final, cod_atestado FROM atestado_falta WHERE usuario_id = ? AND data_inicial >= ? AND data_final <= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $usuario_id, $data_inicial, $data_final);
    $stmt->execute();
    $stmt->bind_result($codigo_log, $data_inicial, $data_final, $cod_atestado);
    $results = array();

    while ($stmt->fetch()) {
        $results[] = array(
            'codigo_log' => $codigo_log,
            'data_inicial' => $data_inicial,
            'data_final' => $data_final,
            'cod_atestado' => $cod_atestado
        );
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #002d54;
            /* Azul escuro */
            color: #000000;
            /* Texto branco */
            font-family: 'Bahnschrift', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .main-content {
            max-width: 400px;
            /* Ajuste a largura conforme necessário */
            width: 100%;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            /* Fundo branco */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        h1,
        h2,
        p {
            text-align: center;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #F5F5F5;
            /* Cor de fundo dos campos */
            color: #333;
            /* Cor do texto dos campos */
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            /* Azul claro */
            color: #ffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form label[for="data_inicial"],
        form label[for="data_final"] {
            display: block;
            margin-top: 10px;
        }

        form input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #F5F5F5;
            color: #333;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
            /* Cor de fundo do botão de envio ao passar o mouse */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ffffff;
            text-align: center;
        }

        th {
            background-color: #5a615a;
            /* Cinza mais claro */
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #3b403b;
            /* Cinza escuro */
            color: #ffffff;
        }

        tr:nth-child(odd) {
            background-color: #606961;
            /* Azul mais claro */
            color: #ffffff;
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
            margin-left: auto;
            margin-right: auto;
            width: 30%;
            /* ou qualquer outra largura desejada */
            text-align: center;
            /* centralizar o texto dentro da div */
            padding: 20px;
            transition: margin-left 0.3s ease;
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
    <title>Acompanhar Registros</title>
</head>

<body>
    <div class="main-content">
        <h1>Acompanhar Registros</h1>
        <form method="post" action="">
            <label for="usuario_id">Selecione o Usuário:</label>
            <select name="usuario_id" id="usuario_id" required>
                <option value="" disabled selected>Selecione um usuário</option>
                <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo $row['nome']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>

            <!-- Adicione campos de data inicial e data final -->
            <label for="data_inicial">Data Inicial:</label>
            <input type="date" name="data_inicial" id="data_inicial" required>
            <br>

            <label for="data_final">Data Final:</label>
            <input type="date" name="data_final" id="data_final" required>
            <br>

            <input type="submit" value="Buscar">
        </form>

        <?php if (isset($results) && !empty($results)): ?>
            <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                <h2>Registros para o Usuário:
                    <?php echo $row['nome']; ?>
                </h2>
            <?php endwhile; ?>
            <table border="1">
                <tr>
                    <th>Código</th>
                    <th>Data Inicial</th>
                    <th>Data Final</th>
                    <th>Código do Atestado</th>
                </tr>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td>
                            <?php echo $result['codigo_log']; ?>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($result['data_inicial'])); ?>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($result['data_final'])); ?>
                        </td>
                        <td>
                            <?php echo $result['cod_atestado']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
        <?php elseif (isset($usuario_id)): ?>
            <p>Nenhum registro encontrado para o Usuário:
                <?php echo $row['nome']; ?>
            </p>
        <?php endif; ?>
    </div>

    <i class="show-sidebar-icon fas fa-arrow-alt-circle-right" onclick="showSidebar()"></i>

    <div class="sidebar sidebar-closed" onmouseleave="handleMouseLeave()">
        <div class="sidebar">
            <div class="user-info">
                <div class="username">
                    <img src="imagens/usuario1.png" alt="Ícone de Usuario">
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

                <!-- Adicione os outros sublinks associados a Relatórios -->
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

                <!-- Adicione os outros sublinks associados a Relatórios -->
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

            <a class="service-link3" href="../consultalf/inicialPatch.php">
                <img src="imagens/usuario1.png" alt="Ícone de LF promotora">
                Sistema LF Promotora
            </a>

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