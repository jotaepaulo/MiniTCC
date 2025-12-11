<?php
session_start();
require '../processos/config.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $msg = "Preencha todos os campos!";
    } else {
        $senhaHash = md5($senha);

        $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $nome, $email, $senhaHash);

        if ($stmt->execute()) {
            $msg = "Usuário cadastrado com sucesso!";
        } else {
            $msg = "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cadastro - Sistema</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/styleDados.css">
</head>
<body>

<form class="login-form" method="POST" novalidate>
    <div class="logo-container">
        <img src="../img/LOGO.png" class="logo" alt="Logo">
    </div>

    <h4 class="text-center mb-3 text-white">Cadastre seu funcionario</h4>
    <h6 class="text-center mb-3 text-white">Digite as informações!</h6>

    <input type="text" name="nome" class="form-control mb-2" placeholder="Nome Completo" required>

    <input type="email" name="email" class="form-control mb-2" placeholder="E-mail" required>

    <input type="password" name="senha" class="form-control mb-2" placeholder="Senha" required>

    <div class="d-flex gap-2">
        <button type="submit" class="btn-cadastrar">Cadastrar</button>
        <button type="button" class="btn-cadastrar" onclick="window.location.href='../processos/painel_admin.php'">Voltar</button>
    </div>

    <?php if ($msg !== ''): ?>
      <div class="alert alert-info text-center mt-3"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

</form>

</body>
</html>
