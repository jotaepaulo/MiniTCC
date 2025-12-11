<?php
session_start();
include "../processos/config.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['id'];
$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$senha = $_POST['senha'];

if (!empty($senha)) {
    $senha_hash = md5($senha);

    $sql = "UPDATE cliente SET nome=?, telefone=?, email=?, senha=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $telefone, $email, $senha_hash, $id);
} else {
    $sql = "UPDATE cliente SET nome=?, telefone=?, email=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $telefone, $email,  $id);
}

if ($stmt->execute()) {
    header("Location: ../processos/listagem_cliente.php");
} else {
    echo "Erro ao atualizar.";
}
?>
