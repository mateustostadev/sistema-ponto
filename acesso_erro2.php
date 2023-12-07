<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Acesso Negado!</title>
    <style>
        body {
            font-family: 'Bahnschrift', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #008000;
            /* Caminho para a imagem de fundo */
            background-size: cover;
            /* Ajusta o tamanho da imagem para cobrir todo o corpo */
            background-position: center;
            /* Centraliza a imagem */
            background-repeat: no-repeat;
            /* Evita a repetição da imagem */
        }

        .error-box {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .error-box h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <h2>Acesso Negado!</h2>
        <p class="error-message">Você não possui acesso a essa página! Por favor, tente novamente.</p>
        <p class="error-message"><a href="inicial2.php">Voltar para a página inicial</a></p>
    </div>
</body>

</html>