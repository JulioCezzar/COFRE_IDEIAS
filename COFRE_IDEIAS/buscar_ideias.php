<?php
include 'conexao.php';

function buscarIdeiasPorTags($busca, $conexao) {
    // Limpar e validar a busca
    $busca = trim($busca);
    
    if (empty($busca)) {
        return false;
    }
    
    // Separar tags por vírgula ou espaço
    $tags = preg_split('/[\s,]+/', $busca);
    
    // Remover tags vazias e aplicar segurança
    $tags = array_filter(array_map('trim', $tags));
    $tags = array_map(function($tag) use ($conexao) {
        return $conexao->real_escape_string($tag);
    }, $tags);
    
    if (empty($tags)) {
        return false;
    }
    
    // Construir a query para buscar por múltiplas tags
    $whereConditions = [];
    
    foreach ($tags as $tag) {
        $whereConditions[] = "t.NOME LIKE '%{$tag}%'";
    }
    
    $whereClause = implode(' OR ', $whereConditions);
    
    // CORREÇÃO: Incluir a coluna ESTAGIO na query
    $sql = "SELECT DISTINCT i.ID_IDEIA, i.TITULO, i.DESCRICAO, i.PRIORIDADE, i.ESTAGIO, i.IMAGEM,
            GROUP_CONCAT(t.NOME SEPARATOR ', ') AS TAGS,
            COUNT(DISTINCT t.ID_TAGS) as tag_count
            FROM ideia i
            LEFT JOIN ideias_tags it ON i.ID_IDEIA = it.IDEIA_ID
            LEFT JOIN tags t ON it.TAGS_ID = t.ID_TAGS
            WHERE {$whereClause}
            GROUP BY i.ID_IDEIA
            ORDER BY tag_count DESC, i.PRIORIDADE DESC, i.ID_IDEIA DESC";
    
    $result = $conexao->query($sql);
    
    return $result;
}

// Função para buscar todas as ideias (quando não há busca)
function buscarTodasIdeias($conexao) {
    // CORREÇÃO: Incluir a coluna ESTAGIO e IMAGEM na query
    $sql = "SELECT i.ID_IDEIA, i.TITULO, i.DESCRICAO, i.PRIORIDADE, i.ESTAGIO, i.IMAGEM,
            GROUP_CONCAT(t.NOME SEPARATOR ', ') AS TAGS
            FROM ideia i
            LEFT JOIN ideias_tags it ON i.ID_IDEIA = it.IDEIA_ID
            LEFT JOIN tags t ON it.TAGS_ID = t.ID_TAGS
            GROUP BY i.ID_IDEIA
            ORDER BY i.PRIORIDADE DESC, i.ID_IDEIA DESC";
    
    return $conexao->query($sql);
}
?>