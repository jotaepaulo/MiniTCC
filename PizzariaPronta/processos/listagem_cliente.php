<?php
include "../processos/config.php";
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo_conta'] !== 'usuario') {
    header("Location: ../paginas/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciamento de Clientes</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../css/styleListagem.css">
</head>

<body>

<header>

  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">

        <a href="#">
          <img class="logo" src="../img/LOGO.png" alt="Logo">
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">

          <a href="../processos/painel_admin.php" class="btn-pedidos">
            Voltar
          </a>

        </div>
      </div>
    </div>
  </nav>

</header>






<div class="container box-listagem">

  <h3 class="text-center titulo-listagem mb-4">
    Gerenciamento de Clientes
  </h3>

  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead class="table-warning">
        <tr>
          <th>Id</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Telefone</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>

        <?php
        $sql = "SELECT * FROM cliente ORDER BY id DESC";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "
                <tr>
                  <td>{$row['id']}</td>
                  <td>{$row['nome']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['telefone']}</td>
                  <td>
                    <a href='../processos/excluir_cliente.php?id={$row['id']}'
                       class='btn btn-sm btn-danger'
                       onclick='return confirmarExclusao()'>
                       Excluir
                    </a>
                  </td>
                </tr>";
            }
        } else {
            echo "<tr>
                    <td colspan='5' class='text-center'>
                      Nenhum cliente cadastrado
                    </td>
                  </tr>";
        }
        ?>

      </tbody>
    </table>
  </div>
</div>

<script>
function confirmarExclusao() {
  return confirm("Tem certeza que deseja excluir este cadastro? Esta ação não pode ser desfeita.");
}
</script>

</body>
</html>
