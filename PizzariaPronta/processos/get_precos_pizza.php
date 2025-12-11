<?php
include "../processos/config.php";

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

$pizza_id = $_GET['id'];

$sql = "SELECT 
    tamanho_pizza.nome as tamanho,
    pizza_preco.preco
FROM pizza_preco
JOIN tamanho_pizza ON pizza_preco.tamanho_id = tamanho_pizza.id
WHERE pizza_preco.pizza_id = ?
ORDER BY tamanho_pizza.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pizza_id);
$stmt->execute();
$result = $stmt->get_result();

$precos = [];
while ($row = $result->fetch_assoc()) {
    $precos[$row['tamanho']] = floatval($row['preco']);
}

echo json_encode(['success' => true, 'precos' => $precos]);
?>