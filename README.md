# Sistema Web de Ponto Eletrônico 

## Introdução

Bem-vindo à documentação do Sistema Web de Ponto Eletrônico. Este sistema permite gerenciar a jornada de trabalho de funcionarios com escalas de 8/6 horas e 4 horas.

## Instalação e Configuração do Sistema

### Pré-requisitos

Antes de começar, certifique-se de ter os seguintes requisitos instalados em seu sistema:

1. [Composer](https://getcomposer.org/): Um gerenciador de dependências para PHP.
2. [XAMPP](https://www.apachefriends.org/index.html) ou outro servidor MySQL e PHP.
3. [Git](https://git-scm.com/downloads): Para controle de versão.

### Passos para Instalação

1. Clone o repositório:

    ```bash
    git clone https://github.com/mateustostadev/sistema-ponto.git
    ```

2.  Abra o arquivo `backend/conexao.php` e configure as variáveis de ambiente.

       ```dotenv
       $servername = "seu-servidor";
       $username = "seu-usuario";
       $password = "sua-senha";
       $dbname = "seu-banco";
       ```
3. Configure as variáveis de ambiente nos códigos:

- `obterHorariosRegistrados.php`
- `obterHorariosRegistrados2.php`
- `registrarPonto.php`
- `registrarPonto2.php`
- `gerar_pdf.php`
- `gerar_pdf2.php`


## Considerações Finais

Certifique-se de seguir as instruções e exemplos fornecidos para interagir corretamente com o sistema. Em caso de dúvidas ou problemas, consulte a documentação ou entre em contato.
