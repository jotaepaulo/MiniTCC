<?php
include "config.php";

if (!isset($_GET['id'])) {
    die("ID do pedido não informado.");
}

$pedido_id = intval($_GET['id']);


$sql_itens = "DELETE FROM item_pedido WHERE pedido_id = ?";
$stmt_itens = $conn->prepare($sql_itens);
$stmt_itens->bind_param("i", $pedido_id);
$stmt_itens->execute();


$sql_pedido = "DELETE FROM pedido WHERE id = ?";
$stmt_pedido = $conn->prepare($sql_pedido);
$stmt_pedido->bind_param("i", $pedido_id);

if ($stmt_pedido->execute()) {
    header("Location: ../processos/painel_admin.php");
    exit;
} else {
    echo "Erro ao excluir o pedido.";
}
?>
