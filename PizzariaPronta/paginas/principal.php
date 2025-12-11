<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo_conta'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

include "../processos/config.php";
$cliente_id = $_SESSION['id'];
/* Busca dados do cliente */
$sqlCliente = "SELECT nome, foto FROM cliente WHERE id = $cliente_id";
$resCliente = $conn->query($sqlCliente);
$cliente = $resCliente->fetch_assoc();

$fotoPerfil = $cliente['foto'] ?? 'user.png';


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
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Montserrat:wght@400;700&family=Poppins:wght@300;600&display=swap" rel="stylesheet">


  <!-- CSS -->
  <link rel="stylesheet" href="../css/styleTelas.css">
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
            Bem-vindo cliente, <?= htmlspecialchars($cliente['nome']) ?>
        </span>
    </li>
</ul>


        <div class="collapse navbar-collapse" id="navbarSupportedContent">

          <a href="#">
            <img class="logo" src="../img/LOGO.png" alt="logo" />
          </a>
      <a href="pedidos.php" class="btn-pedidos">Fazer Pedido</a>
<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
    <li class="nav-item dropdown">

        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <img class="user"  src="../img/clientes/<?= htmlspecialchars($fotoPerfil); ?>" alt="logo" style="width: 38px; height: 38px;">
        </a>

        <ul class="dropdown-menu dropdown-menu-end" style="background-color: black;">

            <li><a class="dropdown-item text-white" href="../paginas/perfil.php">Meu Perfil</a></li>
                <li><a class="dropdown-item text-white" href="../processos/logout.php">Sair</a></li>

        </ul>

    </li>

</ul>


        </div>
      </div>
    </nav>
  </header>

<main>
  <section class="netflix-carousel">

      <div class="carousel-slide active">
        
        <img src="../img/fundoPizza.png" alt="Poster Principal">
,
        <button onclick="window.location.href='pedidos.php'" class="botao-pizzaria">
          🍕 Pedir Agora
        </button>

      </div>
  </section>
</main>

<section class="sobre-nos">
    <div class="texto">
        <h2>Sobre nós</h2>
        <p>A loja em Criciúma, na região de Santa Catarina, é famosa pelo atendimento amigável e pelas pizzas artesanais de alta qualidade.</p>

        <p>Com um cardápio que vai dos clássicos aos sabores inovadores, oferecemos opções que agradam a todos os gostos.</p>

        <p>Estamos comprometidos em proporcionar uma experiência gastronômica de alta qualidade, seja para entrega, retirada ou consumo no local.</p>

        <p>Venha nos visitar em Criciúma e descubra o sabor que vai conquistar o seu paladar.</p>
    </div>

    <div class="imagem">
        <img src="../img/pizzaria.png" alt="Foto da pizzaria">
    </div>
</section>



<div class="area-pedidos">

    <h2>Seus Pedidos</h2>

    <div class="lista-pedidos">
        <?php
            include "../processos/config.php";
            $sql = "SELECT 
                        pedido.*,
                        cliente.nome
                    FROM pedido
                    JOIN cliente
                    ON pedido.cliente_id = cliente.id
                    ORDER BY pedido.id DESC";
            $resultado = $conn->query($sql);

            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
        ?>
        <div class="col">
            <div class="card card-dark text-center h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['nome']) ?></h5>

                    <p class="card-text">
                        <strong>Preço:</strong> R$ <?= number_format($row['total'], 2, ',', '.') ?><br>
                        <strong>Status:</strong> <?= htmlspecialchars($row['status']) ?><br>
                    </p>


                    <button type="button" 
                        class="btn btn-danger btn-sm"
                        onclick="abrirModalExclusao(<?= $row['id'] ?>)">
                        Excluir
                    </button>


                    <button type="button" 
                        class="btn btn-warning btn-sm visualizarPedido"
                        data-id="<?= $row['id'] ?>"
                        data-bs-toggle="modal" 
                        data-bs-target="#modalPedido">
                        Visualizar Pedido
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



<div class="modal fade" id="modalPedido" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detalhes do Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
        <div id="conteudoPedido">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>


<!--CARREGAR PEDIDO-->

<script>
document.querySelectorAll(".visualizarPedido").forEach(btn => {
    btn.addEventListener("click", function () {

        let pedidoID = this.getAttribute("data-id");

        document.getElementById("conteudoPedido").innerHTML = "<p>Carregando...</p>";

        fetch("../processos/get_pedido.php?id=" + pedidoID)
            .then(response => response.text())
            .then(html => {
                document.getElementById("conteudoPedido").innerHTML = html;
            });
    });
});
</script>



<!--CONFIRMAR EXCLUSÃO-->

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

<script>
function abrirModalExclusao(id) {
    document.getElementById("botaoExcluirConfirmado").href =
        "../processos/excluir_pedido.php?id=" + id;

    let modal = new bootstrap.Modal(document.getElementById("modalConfirmarExclusao"));
    modal.show();
}
</script>










</body>

</html>