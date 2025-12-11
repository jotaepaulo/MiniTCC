<?php
require '../processos/config.php';

$id = intval($_GET['id']); 


$sqlCliente = "SELECT id FROM cliente WHERE id = $id LIMIT 1";
$resultCliente = $conn->query($sqlCliente);

if ($resultCliente->num_rows > 0) {


    $delete = "
         DELETE item_pedido 
        FROM item_pedido
        INNER JOIN pedido ON item_pedido.pedido_id = pedido.id
        WHERE pedido.cliente_id = $id;

        DELETE FROM pedido WHERE cliente_id = $id;

        DELETE FROM cliente WHERE id = $id;
    ";

    if ($conn->multi_query($delete)) {


        while ($conn->more_results() && $conn->next_result()) {;}

        header("Location: listagem_cliente.php");
        exit;
    } else {
        echo "Erro ao excluir cliente: " . $conn->error;
    }

} else {
    echo "ID não encontrado na tabela cliente.";
}
?>
