<?php
include "config.php";

if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>Pedido inválido.</p>";
    exit;
}

$pedido_id = intval($_GET['id']);


$sqlInfo = "SELECT 
                p.id,
                p.total,
                p.status,
                c.nome AS cliente
            FROM pedido p
            JOIN cliente c ON c.id = p.cliente_id
            WHERE p.id = $pedido_id
            LIMIT 1";

$info = $conn->query($sqlInfo)->fetch_assoc();


$sqlItens = "SELECT 
                pi.nome AS pizza,
                pi.imagem AS imagem,
                SUM(ip.quantidade) AS quantidade_total
            FROM pedido p
            JOIN item_pedido ip ON p.id = ip.pedido_id
            JOIN pizza pi ON ip.pizza_id = pi.id
            WHERE p.id = $pedido_id
            GROUP BY pi.nome, pi.imagem
            ORDER BY pi.nome";

$result = $conn->query($sqlItens);

if ($result->num_rows == 0) {
    echo "<p>Este pedido não possui pizzas cadastradas.</p>";
    exit;
}
?>



<div class="mb-3 pb-2 border-bottom">
    <h4 class="fw-bold">
        Pedido #<?= $info['id'] ?>
    </h4>

    <p class="m-0">
        <strong>Cliente:</strong> <?= htmlspecialchars($info['cliente']) ?><br>
        <strong>Status:</strong> <?= htmlspecialchars($info['status']) ?><br>
        <strong>Total:</strong> R$ <?= number_format($info['total'], 2, ',', '.') ?>
    </p>
</div>



<h5 class="fw-semibold mb-3">Itens do Pedido</h5>

<?php while ($row = $result->fetch_assoc()) { ?>

    <div class="card mb-2 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body d-flex align-items-center">

            <!-- IMAGEM -->
            <img src="../img/pizzas/<?= $row['imagem'] ?>" 
                 class="shadow-sm"
                 style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px; margin-right: 15px;">

            <!-- TEXTO -->
            <div>
                <h6 class="fw-bold mb-1">
                    <?= htmlspecialchars($row['pizza']) ?>
                </h6>

                <p class="m-0 text-muted">
                    Quantidade: <?= $row['quantidade_total'] ?>
                </p>
            </div>
        </div>
    </div>

<?php } ?>
