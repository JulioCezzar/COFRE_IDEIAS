<?php
include 'conexao.php';

// Verifica se foi passado um ID pela URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID inválido.");
}

// Antes de deletar, confirmar se a ideia existe
$sql = "SELECT * FROM ideia WHERE ID_IDEIA = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ideia não encontrada.");
}

// Primeiro remover os relacionamentos (tags ligadas à ideia)
$sqlDeleteTags = "DELETE FROM ideias_tags WHERE IDEIA_ID = ?";
$stmtDeleteTags = $conexao->prepare($sqlDeleteTags);
$stmtDeleteTags->bind_param("i", $id);
$stmtDeleteTags->execute();

// Depois remove a ideia em si
$sqlDeleteIdeia = "DELETE FROM ideia WHERE ID_IDEIA = ?";
$stmtDeleteIdeia = $conexao->prepare($sqlDeleteIdeia);
$stmtDeleteIdeia->bind_param("i", $id);
$stmtDeleteIdeia->execute();

echo "<script>alert('Ideia deletada com sucesso!'); window.location='visualizar_ideias.php';</script>";

$conexao->close();
?>