<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit();
}

// Verifica se o código_adm é igual a 18049
if (isset($_SESSION['codigo_adm']) && $_SESSION['codigo_adm'] !== '7415689lf') {
    header('Location: acesso_erro.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
    <title>Relatório de Horas Mensais Trabalhadas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="frontend/estiloGerenciar.css">
</head>

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
        height: 90vh;
    }


    h1 {
        color: #f0f0f0;
        /* Branco */
        margin-left: auto;
        margin-right: auto;


    }

    form {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
        margin-bottom: 20px;
        width: 90%;
    }

    label {
        display: block;
        margin-bottom: 10px;
        color: #333;
    }

    select,
    input {
        width: 90%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: #002d54;
        /* Verde */
        color: #ffffff;
        cursor: pointer;
    }

    #resumo {
        margin-top: 20px;
        padding: 10px;
        background-color: #f2f2f2;
        border-radius: 5px;
    }

    #resumo p {
        margin: 0;
        font-weight: bold;
        color: #333;
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
        width: 20%;
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

    .main-content {
        max-width: 700px;
        margin: 20px auto;
        padding: 20px;

        border-radius: 8px;
    }

    h1 {
        text-align: center;
    }

    table {
        width: 105%;
        border-collapse: collapse;
        margin-top: 10px;
        max-height: 200px;
        /* Altura máxima da tabela */
        overflow-y: auto;
        /* Adiciona scroll vertical se a tabela ultrapassar a altura máxima */
        background-color: #f2f2f2;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        white-space: nowrap;
        /* Evita a quebra de linha */
    }

    th {
        background-color: #f2f2f2;
    }

    td {
        flex: 1;
        margin: 0 2px;
        /* Adiciona margem entre as colunas */
    }

    #resumo {
        margin-top: 20px;
        padding: 10px;
        background-color: #f2f2f2;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
        width: 250px;
    }

    #resumo p {
        margin: 0;
        font-weight: bold;
        color: #333;
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

<body>
    <div class="main-content">
        <h1>Horas Mensais</h1>

        <!-- Formulário para selecionar o funcionário e o mês -->
        <form method="post" action="">
            <label for="funcionario">Selecione o Funcionário:</label>
            <select name="funcionario" id="funcionario">
                <!-- Popule esta lista com os funcionários do seu banco de dados -->
                <option value="1">Teste</option>
            </select>

            <label for="data_inicial">Selecione a Data Inicial:</label>
            <input type="date" name="data_inicial" id="data_inicial" required>

            <label for="data_final">Selecione a Data Final:</label>
            <input type="date" name="data_final" id="data_final" required>

            <input type="submit" value="Calcular Horas Mensais">
        </form>

        <!-- Tabela de registros diários e totais -->
        <?php
        include_once('backend/conexao.php');
        function calcularHorasTrabalhadas($conn, $usuario_id, $data, $nome)
        {
            $sql = "SELECT 
                SUM(
                    TIME_TO_SEC(
                        TIMEDIFF(
                            IFNULL(data_hora_almoco, '23:59:59'), 
                            IFNULL(data_hora_entrada, '00:00:00')
                        )
                    )
                ) 
             as segundos_trabalhados 
            FROM tabela_ponto 
            WHERE usuario_id = '$usuario_id' 
            AND DATE(data_hora_entrada) = '$data'";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['segundos_trabalhados'];
            } else {
                return 0; // Retorna 0 segundos se não houver registros
            }
        }

        function getNomeDoFuncionario($usuario_id)
        {
            $nomes = [
                '1' => 'Teste',

                // Adicione mais conforme necessário
            ];

            return isset($nomes[$usuario_id]) ? $nomes[$usuario_id] : 'Nome Desconhecido';
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $usuario_id = isset($_POST['funcionario']) ? $_POST['funcionario'] : '';
            $data_inicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : '';
            $data_final = isset($_POST['data_final']) ? $_POST['data_final'] : '';
            $nome2 = getNomeDoFuncionario($usuario_id);

            // Calcular horas trabalhadas para o intervalo de datas
            $total_segundos_mensais = 0;

            echo "<table border='1'>";
            echo "<tr><th>Data</th><th>Horas Diárias</th></tr>";

            $data_atual = $data_inicial;

            while (strtotime($data_atual) <= strtotime($data_final)) {
                $segundos_diarios = calcularHorasTrabalhadas($conn, $usuario_id, $data_atual, $nome2);

                if ($segundos_diarios) {
                    // Somar os segundos diários ao total mensal
                    $total_segundos_mensais += $segundos_diarios;

                    // Formatando o tempo para exibição no formato HH:MM:SS
                    $horas_diarias_formatado = gmdate("H:i:s", $segundos_diarios);

                    echo "<tr><td>$data_atual</td><td>$horas_diarias_formatado</td></tr>";
                }

                // Avançar para a próxima data
                $data_atual = date("Y-m-d", strtotime($data_atual . "+1 day"));
            }

            echo "</table>";

            // Calcular total em horas, minutos e segundos
            $horas = floor($total_segundos_mensais / 3600);
            $minutos = floor(($total_segundos_mensais % 3600) / 60);
            $segundos = $total_segundos_mensais % 60;

            echo "<div id='resumo'>";
            echo "<p>Total de Horas Mensais Trabalhadas por $nome2: $horas horas, $minutos minutos, $segundos segundos</p>";

            echo "</div>";
        }
        ?>
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
