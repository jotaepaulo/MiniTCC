<?php
session_start();
include "config.php";

if (!isset($_SESSION['id'])) {
    header("Location: ../paginas/login.php");
    exit;
}

$cliente_id = $_SESSION['id'];

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
    die("Erro no upload da imagem.");
}

$tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg'];
if (!in_array($_FILES['foto']['type'], $tiposPermitidos)) {
    die("Formato inválido.");
}

$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
$nomeArquivo = "cliente_" . $cliente_id . "_" . time() . "." . $ext;

$destino = "../img/clientes/" . $nomeArquivo;

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
    die("Erro ao salvar imagem.");
}

$stmt = $conn->prepare("UPDATE cliente SET foto = ? WHERE id = ?");
$stmt->bind_param("si", $nomeArquivo, $cliente_id);
$stmt->execute();

header("Location: ../paginas/editarPerfil.php");
exit;
