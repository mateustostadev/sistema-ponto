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

require('tcpdf/tcpdf.php');

// Função para gerar o relatório em PDF
function gerarRelatorioPDF($idFuncionario, $mes, $ano, $dadosPonto, $nomeFuncionario)
{
    $pdf = new TCPDF();
    $pdf->SetTitle('Relatório Mensal de Ponto');
    $pdf->SetFont('freeserif', '', 12); // Use uma fonte que suporte UTF-8
    $pdf->AddPage();

    // Título do Relatório
    $pdf->SetFont('freeserif', 'B', 16);
    $pdf->Cell(0, 10, 'Relatório Mensal de Ponto', 0, 1, 'C');

    // Informações do Funcionário
    $pdf->SetFont('freeserif', 'B', 14);
    $pdf->Cell(0, 10, "Funcionário: $nomeFuncionario", 0, 1, 'L');
    $pdf->Cell(0, 10, "Mês: $mes/$ano", 0, 1, 'L');

    // Tabela de Dados
    $pdf->SetFont('freeserif', 'B', 12);
    $pdf->Cell(30, 10, 'Data', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Entrada', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Saída', 1, 1, 'C');

    foreach ($dadosPonto as $dados) {
        $pdf->Cell(30, 10, $dados['data'], 1, 0, 'C');
        $pdf->Cell(30, 10, $dados['entrada'], 1, 0, 'C');
        $pdf->Cell(30, 10, $dados['saida'], 1, 1, 'C');
    }

   // Adicionar linhas de assinatura
   $pdf->Ln(10); // Adicionar espaço entre a tabela e as linhas de assinatura

   // Linha de assinatura do gerente
   $pdf->Cell(0, 10, 'CIDADE DA EMPRESA, __________ DE _________________________ DE _____________', 0, 1, 'L');
   $pdf->Ln(5); // Adicionar espaço entre as linhas de assinatura

   // Linha de assinatura do gerente
   $pdf->Cell(0, 10, 'Assinatura do Supervisor:', 0, 1, 'L');
   $pdf->Cell(0, 10, '________________________________', 0, 1, 'L');


   // Linha de assinatura do funcionário
   $pdf->Ln(5); // Adicionar espaço entre as linhas de assinatura
   $pdf->Cell(0, 10, 'Assinatura do Colaborador:', 0, 1, 'L');
   $pdf->Cell(0, 10, '________________________________', 0, 1, 'L');

   // Nome do arquivo
   $nomeArquivo = "relatorio_funcionario.pdf";

   // Saída para o navegador
   $pdf->Output($nomeArquivo, 'D');
}




// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados do formulário
    $idFuncionario = $_POST['funcionario'];
    $dataInicial = $_POST['data_inicial'];
    $dataFinal = $_POST['data_final'];

    $conn = new mysqli("seu-servidor", "seu-usuario", "sua-senha", "seu-banco");
    $conn->set_charset("utf8");

    // Verificar a conexão
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Consultar dados do banco de dados com base no ID do funcionário e nas datas
    $sql = "SELECT usuario_id, data_hora_entrada, data_hora_saida
FROM tabela_ponto
WHERE usuario_id = ? AND DATE(data_hora_entrada) BETWEEN ? AND ?
ORDER BY data_hora_entrada";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $idFuncionario, $dataInicial, $dataFinal);
    $stmt->execute();
    $result = $stmt->get_result();

    $dadosPonto = [];

    while ($row = $result->fetch_assoc()) {
        $dadosPonto[] = [
            'data' => date('d/m/Y', strtotime($row['data_hora_entrada'])),
            'entrada' => date('H:i', strtotime($row['data_hora_entrada'])),
            'saida' => date('H:i', strtotime($row['data_hora_saida'])),
        ];
    }

    // Obter o nome do funcionário
    $nomeFuncionario = obterNomeFuncionario($conn, $idFuncionario);

    // Chamar a função para gerar o relatório em PDF
    gerarRelatorioPDF($idFuncionario, date('m', strtotime($dataInicial)), date('Y', strtotime($dataInicial)), $dadosPonto, $nomeFuncionario);

    // Fechar a conexão
    $stmt->close();
    $conn->close();
}

// Função para obter o nome do funcionário
function obterNomeFuncionario($conn, $idFuncionario)
{
    $sql = "SELECT nome FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $idFuncionario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['nome'];
}

?>

<!-- Formulário HTML -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Relatório do Funcionário</title>
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
        height: 100vh;
    }

    h2 {
        color: #f0f0f0;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        width: 90%;
        max-width: 400px;
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

    button {
        background-color: #002d54;
        color: #ffffff;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #001e38;
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
        <h2>Relatório Mensal de Ponto</h2>
        <form method="post" action="">
            <label for="funcionario">Selecione o Funcionário:</label>
            <select name="funcionario" id="funcionario">
                <!-- Popule esta lista com os funcionários do seu banco de dados -->
                <option value="45">Teste</option>
            </select>
            <br>

            <label for="data_inicial">Data Inicial:</label>
            <input type="date" id="data_inicial" name="data_inicial" required>
            <br>

            <label for="data_final">Data Final:</label>
            <input type="date" id="data_final" name="data_final" required>
            <br>

            <button type="submit">Gerar Relatório</button>
        </form>
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
</body>

</html>
