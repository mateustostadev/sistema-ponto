<?php
session_start();

include_once('backend/conexao.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit();
}

if (isset($_SESSION['codigo_adm']) && $_SESSION['codigo_adm'] !== '7415689lf') {
    header('Location: acesso_erro.php');
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = isset($_POST['funcionario']) ? $_POST['funcionario'] : '';
    $data_inicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : '';
    $data_final = isset($_POST['data_final']) ? $_POST['data_final'] : '';
    $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : '';
    $cod_atestado = isset($_POST['cod_atestado']) ? $_POST['cod_atestado'] : '';

    // Validar datas
    if ($data_final < $data_inicial) {
        $mensagem = "A data final não pode ser anterior à data inicial.";
    } else {
        // Utilizar strtotime para converter as datas
        $timestamp_inicial = strtotime($data_inicial);
        $timestamp_final = strtotime($data_final);

        // Converter as datas para o formato 'Y-m-d'
        $data_inicial = date('Y-m-d', $timestamp_inicial);
        $data_final = date('Y-m-d', $timestamp_final);

        // Verificar se o código de atestado já existe
        $checkSql = "SELECT COUNT(*) FROM atestado_falta WHERE cod_atestado = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $cod_atestado);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            $mensagem = "O código de atestado já existe.";
        } else {
            // Usando prepared statements para evitar SQL injection
            $insertSql = "INSERT INTO atestado_falta (codigo_log, usuario_id, data_inicial, data_final, cod_atestado) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("issss", $motivo, $usuario_id, $data_inicial, $data_final, $cod_atestado);

            if ($stmt->execute()) {
                // Registro inserido com sucesso
                $mensagem = "Registro inserido com sucesso!";
                $_SESSION['toast_success'] = true; // Altere para true apenas se deseja exibir o toast de sucesso
            } else {
                // Erro ao inserir
                $mensagem = "Erro ao inserir o registro: " . $stmt->error;
                $_SESSION['toast_success'] = false; // Garante que o toast seja exibido como erro
            }
        
            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Registrar Código</title>
    <style>
        body {
            background-color: #002d54;
            /* Cor de fundo azul escuro */
            color: #101211;
            /* Cor do texto branco */
            font-family: 'Bahnschrift', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            max-width: 500px;
            padding: 20px;
            background-color: #FFFFFF;
            /* Cor de fundo do formulário */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select,
        input {
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

        select {
            background-color: #ECEFF1;
            /* Cor de fundo do select */
        }

        input[type="submit"] {
            background-color: #4CAF50;
            /* Cor de fundo do botão de envio */
            color: #FFFFFF;
            /* Cor do texto do botão de envio */
            cursor: pointer;
            border: none;
            border-radius: 4px;
            padding: 12px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
            /* Cor de fundo do botão de envio ao passar o mouse */
        }

        #campo_atestado {
            display: none;
        }

        #mensagem {
            color: #FFFFFF;
            /* Cor do texto branco */
            margin-bottom: 20px;
        }

        .toast {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            font-weight: bold;
        }

        .toast.success {
            background-color: #4CAF50;
            /* Cor de fundo do toast de sucesso */
            color: #FFFFFF;
            /* Cor do texto do toast de sucesso */
        }

        .toast.error {
            background-color: #D32F2F;
            /* Cor de fundo do toast de erro */
            color: #FFFFFF;
            /* Cor do texto do toast de erro */
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


    h1 {
        color:#ffffff;
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

         document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('toast');
            const toastSuccess = <?php echo isset($_SESSION['toast_success']) && $_SESSION['toast_success'] ? 'true' : 'false'; ?>;
            const errorMessage = '<?php echo $mensagem; ?>';

            if (errorMessage !== '' || !toastSuccess) {
                // Exibir toast de erro
                toast.innerText = errorMessage !== '' ? errorMessage : "Erro ao inserir o registro.";
                toast.classList.add('error');
                toast.style.display = 'block';
            } else {
                // Exibir toast de sucesso
                const successMessage = 'Registro inserido com sucesso!';
                toast.innerText = successMessage;
                toast.classList.add('success');
                toast.style.display = 'block';
            }

            // Limpar a variável de sessão após exibir o toast
            <?php unset($_SESSION['toast_success']); ?>

            // Ocultar o toast após alguns segundos
            setTimeout(function () {
                toast.style.display = 'none';
            }, 5000); // 5000 milissegundos = 5 segundos
        });

       
    </script>

</head>

<body>
    <div class="main-content">
        <h1>Registrar Ocorrência</h1>
        <form method="post" action="">
            <label for="funcionario">Funcionário:</label>
            <select name="funcionario" id="funcionario">
                <!-- Popule esta lista com os funcionários do seu banco de dados -->
                <option value="33">Adriana Santos</option>
                <option value="28">Jacilene Cristina</option>
                <option value="26">Jessica Carvalho</option>
                <option value="30">Jonathan Nascimento</option>
                <option value="39">Jocivaldo Santos</option>
                <option value="34">Kailani Vieira</option>
                <option value="43">Liziane França</option>
                <option value="42">Manoel Oliveira</option>
                <option value="21">Mateus Tosta</option>
                <option value="35">Pedro Gustavo</option>
                <option value="27">Pedro Pereira</option>
                <option value="18">Rafael Sacramento</option>
                <option value="41">Samira de Jesus</option>
                <option value="29">Taiane Aquino</option>
                <option value="23">Weber Sacramento</option>
                <!-- Adicione mais opções conforme necessário -->
            </select>

            <label for="data_cod">Data Inicial:</label>
            <input type="date" name="data_inicial" id="data_inicial" required>

            <label for="data_cod">Data Final:</label>
            <input type="date" name="data_final" id="data_final" required>

            <label for="motivo">Motivo:</label>
            <select name="motivo" id="motivo" required onchange="toggleAtestadoField()">
                <option value="1">Falta</option>
                <option value="2">Atestado</option>
            </select>

            <!-- Adicione um campo para inserir o código do atestado (opcional) -->
            <div id="campo_atestado" style="display: none;">
                <label for="atestado_id">Código do Atestado (máximo 5 caracteres):</label>
                <input type="text" name="cod_atestado" id="cod_atestado" placeholder="Utilizar letras e números."
                    maxlength="5">
            </div>

            <input type="submit" value="Registrar">
        </form>

            <div class="toast" id="toast"></div>

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

            <div class="service-link4" onclick="toggleSubLinks('adicionarregistro')">
                <img src="imagens/digital.png" alt="Ícone de Adicionar Clientes">
                Registrar Ponto
            </div>
            <div id="adicionarregistro" class="sub-links2">
                <a href="inicial.php">
                    <img src="imagens/digital.png" alt="Ícone de Registro">CLT / Estágio 6h
                </a><br><br>

                <a href="inicial2.php">
                    <img src="imagens/digital.png" alt="Ícone de Registro">Estágio 4h
                </a><br><br>

                <!-- Adicione os outros sublinks associados a Relatórios -->
            </div>


            <div class="service-link4" onclick="toggleSubLinks('gerenciarregistros')">
                <img src="imagens/gerenciar.png" alt="Ícone de Adicionar Clientes">
                Gerenciar Horas
            </div>
            <div id="gerenciarregistros" class="sub-links2">
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

            <div class="service-link4" onclick="toggleSubLinks('gerenciarocorrencias')">
                <img src="imagens/ocorrencia.png" alt="Ícone de Adicionar Clientes">
                Faltas e Atestados
            </div>
            <div id="gerenciarocorrencias" class="sub-links2">
                <a href="registrar_cod.php">
                    <img src="imagens/ocorrencia.png" alt="Ícone de Registro">Registrar Faltas/Atestados
                </a><br><br>

                <a href="acompanhar_cod.php">
                    <img src="imagens/ocorrencia.png" alt="Ícone de Registro">Acompanhar Faltas/Atestados
                </a><br><br>
            
            </div>


            <div class="service-link4" onclick="toggleSubLinks('gerenciarrelatorios')">
                <img src="imagens/relatorio.png" alt="Ícone de Adicionar Clientes">
                Gerar Relatorio Mensal
            </div>
            <div id="gerenciarrelatorios" class="sub-links2">
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

    <script>
        function toggleAtestadoField() {
            const motivoSelect = document.getElementById('motivo');
            const campoAtestado = document.getElementById('campo_atestado');

            if (motivoSelect.value == 2) {
                campoAtestado.style.display = 'block';
            } else {
                campoAtestado.style.display = 'none';
            }
        }

        // Exibir o toast de sucesso quando a página for carregada
        document.addEventListener('DOMContentLoaded', function () {
            const toastSuccess = document.getElementById('toastSuccess');
            toastSuccess.style.display = 'block';

            // Ocultar o toast após alguns segundos
            setTimeout(function () {
                toastSuccess.style.display = 'none';
            }, 5000); // 5000 milissegundos = 5 segundos
        });
    </script>
</body>

</html>