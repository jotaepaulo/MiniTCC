<?php
include "config.php";
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo_conta'] !== 'usuario') {
    header("Location: ../paginas/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pedido_id = intval($_POST['pedido_id']);
    $status = $_POST['status'];

    $sql = "UPDATE pedido SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $pedido_id);

    if ($stmt->execute()) {
        header("Location: ../processos/painel_admin.php");
    } else {
        echo "Erro ao atualizar status.";
    }
}
