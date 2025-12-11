<?php
include "../processos/config.php";

session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo_conta'] !== 'usuario') {
    header("Location:  ../paginas/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" href="../css/styleADM.css">
</head>

<body>

<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>


<ul class="navbar-nav me-auto">
    <li class="nav-item fancy-white">
        <span>
            Bem-vindo administrador, <?php echo $_SESSION['nome']; ?> 
        </span>
    </li>
</ul>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

          <a href="#">
            <img class="logo" src="../img/LOGO.png" alt="logo" />
          </a>

<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <img class="user" src="../img/clientes/user.png" alt="User" style="width: 38px; height: 38px;">
        </a>

        <ul class="dropdown-menu dropdown-menu-end" style="background-color: black;">
                <li><a class="dropdown-item text-white" href="logout.php" >Sair</a></li>
        </ul>

    </li>
</ul>

        </div>
      </div>
    </nav>
</header>


<main>

<form action="" class="area-pedidos">

<div class="container mt-5">
    <h2 class="text-center mb-4 " style="font-size:42px; font-weight: bold; ">Pedido dos Clientes</h2>

    <div class="row row-cols-1 row-cols-md-3 g-4">

     <?php
// Puxa todos os pedidos do banco
$sql = "SELECT 
pedido.*,
cliente.nome,
cliente.telefone
from pedido
join cliente
on pedido.cliente_id = cliente.id
ORDER by pedido.id desc";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
?>

<div class="col">
    <div class="card card-dark text-center h-100">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['nome']) ?></h5>
            <p class="card-text">
                <strong>ID do Pedido:</strong> <?= $row['id'] ?><br>
                <strong>Preço:</strong> R$ <?= number_format($row['total'], 2, ',', '.') ?><br>
                <strong>Status:</strong> <?= htmlspecialchars($row['status']) ?><br>
                       <strong>Endereço:</strong> <?= htmlspecialchars($row['endereco']) ?><br>
                       <strong>Telefone:</strong> <?= htmlspecialchars($row['telefone']) ?><br>
            </p>

            <!-- Botão visualizar -->
            <button type="button" class="btn btn-warning btn-sm visualizarPedido"
                    data-id="<?= $row['id'] ?>"
                    data-bs-toggle="modal" data-bs-target="#modalPedido">
                Visualizar Pedido
            </button>

            <!-- Finalizar -->
          <button type="button"
        class="btn btn-success btn-sm"
        onclick="abrirModalStatus(<?= $row['id'] ?>)">
    Atualizar Status
</button>

            <!-- EXCLUIR -->
            <button type="button" 
                    class="btn btn-danger btn-sm"
                    onclick="abrirModalExclusao(<?= $row['id'] ?>)">
                Excluir
            </button>

        </div>
    </div>
</div>

<?php
    }
} else {
    echo "<p class='text-center'>Nenhum pedido cadastrado.</p>";
}
?>
    </div>
  </div>

</form>

<div class="botoes-especificos">
    <button class="botao-pizzaria-especifico"
            onclick="window.location.href='listagem_cliente.php'">
        Visualizar Clientes
    </button>

    <button class="botao-pizzaria-especifico"
            onclick="window.location.href='../paginas/cadastro_cliente.php'">
        Cadastrar Clientes
    </button>
</div>

<div class="botoes-especificos">

    <button class="botao-pizzaria-especifico"
            onclick="window.location.href='../processos/listagem_func.php'">
        Visualizar Funcionarios
    </button>

    <button class="botao-pizzaria-especifico"
            onclick="window.location.href='../paginas/cadastro_admin.php'">
        Cadastrar Funcionarios
    </button>

</div>

<script>
document.querySelectorAll(".visualizarPedido").forEach(btn => {
    btn.addEventListener("click", function () {

        let pedidoID = this.getAttribute("data-id");

        document.getElementById("conteudoPedido").innerHTML = "Carregando...";

        fetch("../processos/get_pedido.php?id=" + pedidoID)
            .then(response => response.text())
            .then(html => {
                document.getElementById("conteudoPedido").innerHTML = html;
            });
    });
});
</script>

</main>

</body>


<div class="modal fade" id="modalPedido" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detalhes do Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
        <div id="conteudoPedido"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>



<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar Exclusão</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Tem certeza que deseja excluir este pedido?  
        Essa ação não pode ser desfeita.
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="botaoExcluirConfirmado" href="#" class="btn btn-danger">Excluir</a>
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="modalStatus" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="../processos/atualizar_status_pedido.php">
      <div class="modal-content">
<div class="modal-body">

  <input type="hidden" name="pedido_id" id="pedido_id_status">

  <div class="status-opcoes">

    <label class="status-item">
      <input type="radio" name="status" value="pendente" required>
      <span>Pendente</span>
    </label>

    <label class="status-item">
      <input type="radio" name="status" value="em preparo">
      <span>Em preparo</span>
    </label>

    <label class="status-item">
      <input type="radio" name="status" value="saiu para entrega">
      <span>Saiu para entrega</span>
    </label>

    <label class="status-item">
      <input type="radio" name="status" value="concluido">
      <span>Concluído</span>
    </label>

  </div>

</div>
 <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            Salvar
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
        </div>

        </div>

       
      </div>
    </form>
  </div>
</div>


<script>


function abrirModalExclusao(id) {

    let modal = new bootstrap.Modal(document.getElementById('modalConfirmarExclusao'));
    document.getElementById("botaoExcluirConfirmado").href =
        "../processos/excluir_pedido_admin.php?id=" + id;
    modal.show();
}

function abrirModalStatus(idPedido) {
    document.getElementById('pedido_id_status').value = idPedido;
    let modal = new bootstrap.Modal(document.getElementById('modalStatus'));
    modal.show();
}
</script>

</html>
