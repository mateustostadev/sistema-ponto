<?php
session_start();

include_once('backend/conexao.php'); // Substitua por seu arquivo de conexão

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit();
}

if (!isset($_SESSION['regime']) || $_SESSION['regime'] !== '8') {
    header('Location: acesso_erro2.php');
    exit();
}


// Configurar o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Recuperar os horários registrados do banco de dados (substitua pelos dados reais do seu banco)
$query = "SELECT data_hora_entrada, data_hora_almoco, data_hora_retorno, data_hora_saida FROM tabela_ponto WHERE usuario_id = ? ORDER BY data_hora_entrada DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();

try {
    $stmt->execute();
} catch (Exception $e) {
    echo 'Erro na execução da consulta: ', $e->getMessage();
}

$result = $stmt->get_result();
$horarios = $result->fetch_assoc();


// Fechar a conexão com o banco de dados
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Ponto Eletrônico</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            /* Caminho para a imagem de fundo */
            background-size: cover;
            /* Ajusta o tamanho da imagem para cobrir todo o corpo */
            background-position: center;
            /* Centraliza a imagem */
            background-repeat: no-repeat;
            /* Evita a repetição da imagem */
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

        .ponto-container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
            height: 240px;
            width: 200px;
            justify-content: center;
            align-items: center;
        }

        .horario {
            margin-top: 5;
        }

        .mensagem-registro {
            font-size: 14px;
            /* Ajuste o tamanho da fonte conforme necessário */
            padding-top: 2px;
            margin-top: auto;
        }


        input[type="submit"] {
            background-color: #002d54;
            color: #ffffff;
            cursor: pointer;
            border: none;
            padding: 10px 20px;
        }

        select,
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .horario-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }

        .horario-container .horario {
            width: 100px;
            margin: 10px;
            padding: 10px;
            background-color: #f4f4f4;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
            display: flex;
            /* Adiciona display flex para alinhar os itens verticalmente */
            flex-direction: column;
            /* Empilha os itens verticalmente */
            align-items: center;
            /* Centraliza os itens horizontalmente */
        }

        .horario-container h4 {
            margin-bottom: 3px;
        }

        .horario-container .horario div {
            font-size: 18px;
            font-weight: bold;
        }

        /* Estilo para mensagens de sucesso/erro */
        .mensagem-container {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .mensagem-container h4 {
            margin: 0;
        }

        .mensagem-registro {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .mensagem-registro.success {
            color: #008000;
            /* Verde para sucesso */
        }

        .mensagem-registro.error {
            color: #ff0000;
            /* Vermelho para erro */
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

<body onmousemove="handleMouseMove(event)">
    <div class="main-content">
        <div class="ponto-container">
            <h3>Horário Atualizado</h3>
            <div id="horario"></div><br>
            <!-- Botão para registrar ponto -->
            <form method="post" action="backend/registrarPonto.php">
                <input type="submit" value="Registrar Ponto">
            </form>

            <div class="mensagem-container">
                <h4>Mensagem:</h4>
                <div id="mensagemRegistro" class="mensagem-registro"></div>
            </div>
        </div>
    </div>

    <!-- Adiciona os 4 contêineres pequenos para os horários registrados -->
    <div class="horario-container">
        <div class="horario">
            <h4>Chegada:</h4>
            <div id="chegada">
                <?php echo isset($horarios['data_hora_entrada']) ? date('H:i:s', strtotime($horarios['data_hora_entrada'])) : ''; ?>
            </div>
        </div>

        <div class="horario">
            <h4>Almoço:</h4>
            <div id="almoco">
                <?php echo isset($horarios['data_hora_almoco']) ? date('H:i:s', strtotime($horarios['data_hora_almoco'])) : ''; ?>
            </div>
        </div>

        <div class="horario">
            <h4>Retorno:</h4>
            <div id="retorno">
                <?php echo isset($horarios['data_hora_retorno']) ? date('H:i:s', strtotime($horarios['data_hora_retorno'])) : ''; ?>
            </div>
        </div>

        <div class="horario">
            <h4>Saída:</h4>
            <div id="saida">
                <?php echo isset($horarios['data_hora_saida']) ? date('H:i:s', strtotime($horarios['data_hora_saida'])) : ''; ?>
            </div>
        </div>
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
                    <img src="imagens/digital.png" alt="Ícone de CLT">CLT / Estágio 6h
                </a><br><br>

                <a href="inicial2.php">
                    <img src="imagens/digital.png" alt="Ícone de Estagio">Estágio 4h
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

        <script>
            function atualizarHorario() {
                var data = new Date();
                var horas = data.getHours();
                var minutos = data.getMinutes();
                var segundos = data.getSeconds();

                horas = (horas < 10) ? "0" + horas : horas;
                minutos = (minutos < 10) ? "0" + minutos : minutos;
                segundos = (segundos < 10) ? "0" + segundos : segundos;

                var horarioAtual = horas + ":" + minutos + ":" + segundos;

                document.getElementById("horario").innerHTML = horarioAtual;
            }

            // Atualizar o horário a cada segundo
            setInterval(atualizarHorario, 1000);

            // Inicializar o horário na carga da página
            atualizarHorario();


            function exibirMensagem(mensagem, sucesso) {
                const mensagemDiv = document.getElementById("mensagemRegistro");
                mensagemDiv.innerHTML = mensagem;

                // Defina a cor do texto com base no sucesso ou erro
                mensagemDiv.style.color = sucesso ? "green" : "red";

                // Agora, defina um temporizador para limpar a mensagem após 3 segundos
                setTimeout(function () {
                    mensagemDiv.innerHTML = "";
                }, 3000);
            }

            function atualizarHorariosRegistrados() {
                // Use Fetch API ou outra forma para obter os horários registrados do servidor
                fetch('backend/obterHorariosRegistrados.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        // Verifique se há dados e exiba "Pendente" se não houver registros
                        document.getElementById("chegada").innerHTML = data.data_hora_entrada ? formatarHorario(data.data_hora_entrada) : 'Pendente';
                        document.getElementById("almoco").innerHTML = data.data_hora_almoco ? formatarHorario(data.data_hora_almoco) : 'Pendente';
                        document.getElementById("retorno").innerHTML = data.data_hora_retorno ? formatarHorario(data.data_hora_retorno) : 'Pendente';
                        document.getElementById("saida").innerHTML = data.data_hora_saida ? formatarHorario(data.data_hora_saida) : 'Pendente';
                    })
                    .catch(error => {
                        console.error("Erro na solicitação:", error);
                    });
            }

            // Inicializar os horários registrados na carga da página
            atualizarHorariosRegistrados();

            // Atualizar os horários registrados a cada 1 segundo (ou o intervalo desejado)
            setInterval(atualizarHorariosRegistrados, 1000);



            // Função para formatar o horário
            function formatarHorario(horario) {
                return new Date(horario).toLocaleTimeString('en-GB');
            }


            document.addEventListener("DOMContentLoaded", function () {
                // Adicionar um ouvinte de evento para o formulário
                const form = document.querySelector("form");
                form.addEventListener("submit", function (event) {
                    event.preventDefault();

                    // Enviar o formulário usando Fetch API
                    fetch(form.action, {
                        method: form.method,
                        body: new FormData(form)
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Exibir mensagem com base na resposta do servidor
                            if (data.success) {
                                // Se o registro for bem-sucedido, atualize os horários imediatamente
                                atualizarHorariosRegistrados();
                                exibirMensagem("Registro realizado com sucesso.", true);
                            } else if (data.message === "Limite de registros atingido") {
                                exibirMensagem("Limite de registros atingido.", false);
                            } else {
                                exibirMensagem("Erro ao registrar Ponto Eletrônico", false);
                            }
                        })
                        .catch(error => {
                            console.error("Erro na solicitação:", error);
                            exibirMensagem("Erro ao registrar Ponto Eletrônico", false);
                        });
                });
            });
        </script>
