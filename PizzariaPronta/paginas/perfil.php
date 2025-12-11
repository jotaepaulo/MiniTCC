<?php
session_start();
require '../processos/config.php';

/* Verifica login */
if (!isset($_SESSION['logado']) || $_SESSION['tipo_conta'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

$cliente_id = $_SESSION['id'];

/* Busca dados */
$sql = "SELECT nome, email, telefone, foto 
        FROM cliente 
        WHERE id = $cliente_id";

$result = $conn->query($sql);
$cliente = $result->fetch_assoc();

/* Foto padrão */
$fotoPerfil = $cliente['foto'] ?? 'user.png';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">
<title>Meu Perfil</title>
<link rel="stylesheet" href="../css/stylePerfil.css">


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Montserrat:wght@400;700&family=Poppins:wght@300;600&display=swap" rel="stylesheet">


</head>


<body>




  <header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

          <a href="#">
            <img class="logo" src="../img/LOGO.png" alt="logo" />
          </a>
          <a href="principal.php" class="btn-pedidos">Voltar</a>
<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
    <li class="nav-item dropdown">

        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <img class="user"  src="../img/clientes/<?= htmlspecialchars($fotoPerfil); ?>" alt="logo" style="width: 38px; height: 38px;">
        </a>

        <ul class="dropdown-menu dropdown-menu-end" style="background-color: black;">
                  <li><a href="pedidos.php" class="dropdown-item text-white">Fazer Pedido</a></li>
                <li><a class="dropdown-item text-white" href="../processos/logout.php">Sair</a></li>
        </ul>

    </li>

</ul>
        </div>
      </div>
    </nav>
  </header>



<form class="perfil-form">

    <!-- Avatar -->
    <div class="perfil-avatar">
    <img src="../img/clientes/<?= htmlspecialchars($cliente['foto']) ?>" 
         alt="Foto de perfil">
</div>

    <!-- Dados (APENAS TEXTO) -->
    <div class="perfil-dados">
        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($cliente['telefone']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></p>
    </div>

    <!-- Botões -->
    <div class="perfil-botoes">
        <button type="button" class="btn-atualizar"
                onclick="window.location.href='../paginas/editarPerfil.php'">
            Atualizar
        </button>

        <button type="button" class="btn-sair"
                onclick="window.location.href='../paginas/principal.php'">
            Sair
        </button>
    </div>

</form>

</body>
</html>
