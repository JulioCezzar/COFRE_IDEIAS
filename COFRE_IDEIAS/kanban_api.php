<?php
include 'conexao.php';

// Headers para CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Lidar com preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Log para debug (remova em produção)
error_log("Kanban API called: " . ($_GET['action'] ?? 'no action'));

try {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'getIdeias':
            getIdeias($conexao);
            break;
            
        case 'updateEstagio':
            updateEstagio($conexao);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
            break;
    }
} catch (Exception $e) {
    error_log("Erro no Kanban API: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}

function getIdeias($conexao) {
    $sql = "SELECT i.ID_IDEIA, i.TITULO, i.DESCRICAO, i.PRIORIDADE, i.ESTAGIO,
            GROUP_CONCAT(t.NOME SEPARATOR ', ') AS TAGS
            FROM ideia i
            LEFT JOIN ideias_tags it ON i.ID_IDEIA = it.IDEIA_ID
            LEFT JOIN tags t ON it.TAGS_ID = t.ID_TAGS
            GROUP BY i.ID_IDEIA
            ORDER BY i.PRIORIDADE DESC, i.ID_IDEIA DESC";

    $result = $conexao->query($sql);
    
    if (!$result) {
        throw new Exception('Erro na query: ' . $conexao->error);
    }
    
    $ideias = [];
    while ($row = $result->fetch_assoc()) {
        // Garantir que os valores estão corretos
        $row['ESTAGIO'] = $row['ESTAGIO'] ?? 'inicial';
        $row['PRIORIDADE'] = $row['PRIORIDADE'] ?? 'MEDIA';
        $ideias[] = $row;
    }
    
    echo json_encode($ideias);
}

function updateEstagio($conexao) {
    // Log dos dados recebidos
    $ideiaId = $_POST['ideia_id'] ?? '';
    $novoEstagio = $_POST['novo_estagio'] ?? '';
    
    error_log("Tentando atualizar: ideia_id=$ideiaId, novo_estagio=$novoEstagio");
    
    if (empty($ideiaId) || empty($novoEstagio)) {
        error_log("Dados incompletos: ideia_id=$ideiaId, novo_estagio=$novoEstagio");
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        return;
    }
    
    // Validar estágio
    $estagiosValidos = ['inicial', 'desenvolvimento', 'conclusao'];
    if (!in_array($novoEstagio, $estagiosValidos)) {
        error_log("Estágio inválido: $novoEstagio");
        echo json_encode(['success' => false, 'message' => 'Estágio inválido: ' . $novoEstagio]);
        return;
    }
    
    // Verificar se a ideia existe
    $check_sql = "SELECT ID_IDEIA FROM ideia WHERE ID_IDEIA = ?";
    $check_stmt = $conexao->prepare($check_sql);
    $check_stmt->bind_param("i", $ideiaId);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        error_log("Ideia não encontrada: $ideiaId");
        echo json_encode(['success' => false, 'message' => 'Ideia não encontrada']);
        $check_stmt->close();
        return;
    }
    $check_stmt->close();
    
    // Atualizar no banco
    $sql = "UPDATE ideia SET ESTAGIO = ? WHERE ID_IDEIA = ?";
    $stmt = $conexao->prepare($sql);
    
    if (!$stmt) {
        error_log("Erro no prepare: " . $conexao->error);
        echo json_encode(['success' => false, 'message' => 'Erro no prepare: ' . $conexao->error]);
        return;
    }
    
    $stmt->bind_param("si", $novoEstagio, $ideiaId);
    
    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        error_log("Update executado com sucesso. Linhas afetadas: $affected_rows");
        
        if ($affected_rows > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Estágio atualizado com sucesso',
                'ideia_id' => $ideiaId,
                'novo_estagio' => $novoEstagio
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhuma linha afetada - verifique o ID']);
        }
    } else {
        error_log("Erro no execute: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Erro ao executar update: ' . $stmt->error]);
    }
    
    $stmt->close();
}

$conexao->close();
?>