<?php
session_start();
require '../processos/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);
    $telefone = $_POST['telefone'];


    $sql = "INSERT INTO cliente (nome, email, senha,telefone) VALUES ('$nome', '$email', '$senha','$telefone')";
    if ($conn->query($sql)) {
        $msg = "Usuário cadastrado com sucesso!";
    } else {
        $msg = "Erro ao cadastrar: " . $conn->error;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Login - Sistema</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../css/styleDados.css">
</head>
<body>



<form class = "login-form" method="POST">
        <div class="logo-container">
    <img src="../img/LOGO.png" class="logo" alt="Logo">
</div>
<h4 class="text-center mb-3 text-white">Cadastre os clientes</h4>
<h6 class="text-center mb-3 text-white">Digite as informações do Cliente!</h6>

<input type="text" name = "nome" class="form-control mb-2" placeholder="Nome Completo" require>

<input type="email" name="email" class="form-control mb-2" placeholder="E-mail" require>

<input type="int" name="telefone" class="form-control mb-2" placeholder="Telefone" require>

<input type="password" name="senha" class="form-control mb-2" placeholder="Senha" require>

<button type="submit" class="btn-cadastrar">Entrar</button>

<button type="button" class="btn-cadastrar" onclick="window.location.href='../processos/painel_admin.php'">Voltar</button>
    </div>



<?php if(isset($msg)): ?>
  <div class = "alert alert-info text-center mt-3"> <?=$msg; ?> </div>

<?php endif; ?>

</form>
 
</body>
</html>
