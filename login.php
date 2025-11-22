<?php
// Inclui as variáveis de configuração e a função conectar_banco()
require_once('conexao/conexao.php');

// Inicia a sessão para armazenar mensagens de feedback
session_start();

// Define a conexão para o escopo global do script
global $table_name;
$conn = conectar_banco();

$message = '';
$message_type = '';

// --- LÓGICA DE PROCESSAMENTO ---

// Processamento do Formulário de Registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Validação básica
    if (empty($nome) || empty($email) || empty($senha)) {
        $message = "Erro: Por favor, preencha todos os campos obrigatórios.";
        $message_type = "error";
    } elseif ($senha !== $confirmar_senha) {
        $message = "Erro: As senhas não coincidem.";
        $message_type = "error";
    } else {
        // VERIFICAR: Vamos ver o que está sendo enviado
        error_log("Tentativa de cadastro - Nome: $nome, Email: $email, Senha: $senha");
        
        // Por enquanto, vamos salvar a senha em texto puro para testar
        $sql = "INSERT INTO $table_name (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            $message = "Erro na preparação: " . $conn->error;
            $message_type = "error";
        } else {
            $stmt->bind_param("sss", $nome, $email, $senha);
            
            if ($stmt->execute()) {
                $message = "Cadastro realizado com sucesso! Agora faça login.";
                $message_type = "success";
                error_log("Usuário cadastrado com ID: " . $stmt->insert_id);
            } else {
                $message = "Erro ao cadastrar: " . $stmt->error;
                $message_type = "error";
            }
            $stmt->close();
        }
    }
} 

// Processamento do Formulário de Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {

    $email_login = trim($_POST['email_login'] ?? '');
    $senha_login = $_POST['senha_login'] ?? '';

    error_log("Tentativa de login - Email: $email_login, Senha: $senha_login");

    $sql = "SELECT id, nome, senha FROM $table_name WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $message = "Erro na preparação: " . $conn->error;
        $message_type = "error";
    } else {
        $stmt->bind_param("s", $email_login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            error_log("Usuário encontrado: " . $user['nome']);
            error_log("Senha no banco: " . $user['senha']);
            error_log("Senha digitada: " . $senha_login);
            error_log("São iguais? " . ($user['senha'] === $senha_login ? 'SIM' : 'NÃO'));
            
            // Comparação direta (texto puro)
            if ($user['senha'] === $senha_login) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nome'] = $user['nome'];
                
                header("Location: comunidade.php");
                exit();
            } else {
                $message = "Erro de Login: Senha incorreta.";
                $message_type = "error";
            }
        } else {
            $message = "Erro de Login: E-mail não encontrado.";
            $message_type = "error";
        }
        $stmt->close();
    }
}

// Fecha a conexão
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Pet-code</title>
    <link rel="icon" href="Imagens/novo-logo.png" type="image/png">
    <link rel="stylesheet" href="Css/cadastrostyle.css">
</head>
<body>
    <!-- Cabeçalho -->
    <header class="header">
        <div class="container">
            <a href="index.html" class="navbar-brand">
                <img class="pet__logo" src="Imagens/novo-logo.png" alt="Logo Pet">
                <h1 class="logo">Pet-code</h1>
            </a>
            <nav class="nav">
                <ul>
                    <li><a href="index.html">Início</a></li>
                    <li><a href="servicos.html">Serviços</a></li>
                    <li><a href="login.php">Cadastrar</a></li>
                    <li><a href="comunidade.php">Comunidade</a></li>
                    <li><a href="contato.html">Contato</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="container">

        <?php if (!empty($message)): ?>
            <div class="message-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Seção Cadastro -->
        <section class="secao-cadastro">
            <h2>Crie sua conta na Pet-code 
                <img src="Imagens/icons8-cachorro-novo-cadastro.png" alt="Ícone dog cadastro">
            </h2>
            <p>Cadastre-se para acessar todos os recursos da plataforma e interagir com outros tutores!</p>

            <form class="form-cadastro" action="#" method="POST">
                <input type="hidden" name="register" value="1">

                <h3>
                    <img src="Imagens/icons8-pessoa-do-sexo-masculino-64-cadastro.png" alt="Ícone person cadastro">
                    Dados do Usuário
                </h3>
                <label for="nome">Nome completo:</label>
                <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Crie uma senha" required>

                <label for="confirmar_senha">Confirme sua senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>

                <h3>
                    <img src="Imagens/icons8-desbloquear-48.png" alt="Ícone cadeado">
                    Pergunta de segurança
                </h3>
                <label for="pergunta">Escolha uma pergunta:</label>
                <select id="pergunta" name="pergunta" required>
                    <option value="">Selecione...</option>
                    <option value="animal">Qual foi o nome do seu primeiro animal de estimação?</option>
                    <option value="escola">Qual era o nome da sua escola primária?</option>
                    <option value="cidade">Em que cidade você nasceu?</option>
                </select>

                <label for="resposta">Resposta:</label>
                <input type="text" id="resposta" name="resposta" placeholder="Digite sua resposta" required>

                <h3>
                    <img src="Imagens/icons8-dog-48.png" alt="Ícone dog">
                    Dados do Pet (opcional)
                </h3>
                <input type="text" name="nome_pet" placeholder="Nome do pet">
                <input type="text" name="especie" placeholder="Espécie (ex: cão, gato)">
                <input type="number" name="idade" placeholder="Idade do pet (em anos)" min="0" max="20">
             

                <button type="submit" class="btn btn-principal">Cadastrar</button>
                <p class="texto-login">Já tem uma conta? <a href="#">Faça login</a></p>
            </form>
        </section>

        <!-- Seção Login -->
        <section class="secao-login">
            <h2>Entrar na sua conta</h2>
            <form class="form-login" action="#" method="POST">
                  <input type="hidden" name="login" value="1">

                <label for="email_login">E-mail:</label>
                <input type="email" id="email_login" name="email_login" placeholder="Digite seu e-mail" required>

                <label for="senha_login">Senha:</label>
                <input type="password" id="senha_login" name="senha_login" placeholder="Digite sua senha" required>

                <button type="submit" class="btn btn-secundario">Entrar</button>

                <p>Ou entre com sua conta do Google:</p>
                <button type="button" class="btn-google">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20">
                    Entrar com o Google
                </button>

                <p class="esqueceu-senha"><a href="#">Esqueceu sua senha?</a></p>
            </form>
        </section>
    </main>

    <!-- Rodapé -->
    <footer class="footer">
        <p><strong>Projeto Acadêmico UMC - Pet-code</strong> | Mogi das Cruzes - SP</p>
        <p>Integrantes: Ana Julia Pinheiro da Silva, Giovanni Almeida Santos, Evelyn Kraus dos Santos.</p>
        <p>&copy; 2025 Pet-code | Incentivando a Conscientização e o Cuidado Animal.</p>
    </footer>
</body>
</html>