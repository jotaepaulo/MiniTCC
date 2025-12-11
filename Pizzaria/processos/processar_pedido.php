<?php
session_start();
include "../processos/config.php";
header('Content-Type: application/json');


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_conta'] !== 'cliente') {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado como cliente.']);
    exit;
}


if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do cliente não encontrado na sessão.']);
    exit;
}

$cliente_id = intval($_SESSION['id']);


$sql_check = $conn->prepare("SELECT id FROM cliente WHERE id = ?");
$sql_check->bind_param("i", $cliente_id);
$sql_check->execute();
$result_check = $sql_check->get_result();

if ($result_check->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cliente não encontrado no banco de dados.']);
    exit;
}


if (!isset($_POST['carrinho']) || !isset($_POST['endereco'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos enviados.']);
    exit;
}

$carrinho = json_decode($_POST['carrinho'], true);
$endereco = trim($_POST['endereco']);

if (empty($carrinho)) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio.']);
    exit;
}


$total = 0;
foreach ($carrinho as $item) {
    if (isset($item['preco'], $item['quantidade'])) {
        $total += $item['preco'] * $item['quantidade'];
    }
}

try {


    $sql_pedido = "INSERT INTO pedido (cliente_id, total, status, endereco) 
                   VALUES (?, ?, 'pendente', ?)";
    $stmt_pedido = $conn->prepare($sql_pedido);

    if (!$stmt_pedido) {
        throw new Exception("Erro ao preparar SQL: " . $conn->error);
    }

    $stmt_pedido->bind_param("ids", $cliente_id, $total, $endereco);

    if (!$stmt_pedido->execute()) {
        throw new Exception("Erro ao inserir pedido: " . $stmt_pedido->error);
    }

    $pedido_id = $stmt_pedido->insert_id;


    $tamanhos_ids = [
        'pequena' => 1,
        'media' => 2,
        'grande' => 3,
        'familia' => 4
    ];


    $sql_item = "INSERT INTO item_pedido (pedido_id, pizza_id, tamanho_id, quantidade) 
                 VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($carrinho as $item) {
        if (!isset($item['id'], $item['tamanho'], $item['quantidade'])) {
            continue;
        }

        $pizza_id = intval($item['id']);
        $tamanho_nome = $item['tamanho'];
        $quantidade = intval($item['quantidade']);

        if (!isset($tamanhos_ids[$tamanho_nome])) {
            continue;
        }

        $tamanho_id = $tamanhos_ids[$tamanho_nome];

        $stmt_item->bind_param("iiii", $pedido_id, $pizza_id, $tamanho_id, $quantidade);
        $stmt_item->execute();
    }


    echo json_encode([
        'success' => true,
        'message' => 'Pedido realizado com sucesso!',
        'pedido_id' => $pedido_id,
        'total' => number_format($total, 2, ',', '.'),
        'endereco' => $endereco
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
