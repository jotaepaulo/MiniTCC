<?php
session_start();
include "../processos/config.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id'];

// BUSCAR DADOS
$sql = "SELECT * FROM cliente WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleEdit.css">



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
            <img class="user"  src="../img/clientes/<?= htmlspecialchars($dados['foto']); ?>" alt="logo" style="width: 38px; height: 38px;">
        </a>
              <ul class="dropdown-menu dropdown-menu-end" style="background-color: black;">
                <li><a class="dropdown-item text-white" href="../processos/atualizar.php">Meu Perfil</a></li>
                <li><a class="dropdown-item text-white" href="../processos/logout.php">Sair</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>


  </header>


<body>

    <form action="../processos/salvar_perfil.php" method="POST">
      <br>
        <h3>Editar Informações</h3>


        <div class="mb-3">
            <label class="form1-label">Nome</label>
            <input type="text" class="form-control" name="nome" value="<?= $dados['nome'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" value="<?= $dados['email'] ?>" required>
        </div>

     <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" class="form-control" name="telefone" value="<?= $dados['telefone'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nova Senha (opcional)</label>
            <input type="password" class="form-control" name="senha">
            <small class="text-muted" style="color:white  !important;">Deixe vazio se não quiser alterar.</small>
        </div>

        <button type="submit" class="btn btn-danger w-100">Salvar Alterações</button>

    </form>

    <!--ATUALIZA FOTO DE PERFIL-->
<form action="../processos/upload_foto.php"
      method="POST"
      enctype="multipart/form-data"
      class="mt-4">
<br><br><br><br><br>
    <h4 class="text-white text-center mb-3">Atualizar Foto de Perfil</h4>

    <div class="text-center mb-3">
        <img src="../img/clientes/<?= htmlspecialchars($dados['foto'] ?: 'user.png'); ?>"
             style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
    </div>

    <div class="mb-3">
        <input type="file"
               name="foto"
               class="form-control"
               accept="image/*"
               required>
    </div>

    <button type="submit" class="btn btn-danger w-100">
        Salvar Foto
    </button>
</form>


</body>
</html>
