<?php
include 'conexao.php';

class AnalyticsIdeias {
    private $conexao;
    
    public function __construct($conexao) {
        $this->conexao = $conexao;
    }
    
    // Buscar tags mais utilizadas
    public function getTagsMaisUsadas($limite = 10) {
        $sql = "SELECT t.NOME as tag, COUNT(it.IDEIA_ID) as total
                FROM tags t
                JOIN ideias_tags it ON t.ID_TAGS = it.TAGS_ID
                GROUP BY t.ID_TAGS, t.NOME
                ORDER BY total DESC
                LIMIT ?";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = $row;
        }
        
        return $tags;
    }
    
    // Buscar distribuição por estágio
    public function getDistribuicaoEstagios() {
        $sql = "SELECT ESTAGIO, COUNT(*) as total
                FROM ideia
                GROUP BY ESTAGIO
                ORDER BY FIELD(ESTAGIO, 'inicial', 'desenvolvimento', 'conclusao')";
        
        $result = $this->conexao->query($sql);
        
        $estagios = [];
        while ($row = $result->fetch_assoc()) {
            $estagios[] = $row;
        }
        
        return $estagios;
    }
    
    // Buscar total de ideias
    public function getTotalIdeias() {
        $sql = "SELECT COUNT(*) as total FROM ideia";
        $result = $this->conexao->query($sql);
        return $result->fetch_assoc()['total'];
    }
    
    // Buscar tags por estágio
    public function getTagsPorEstagio() {
        $sql = "SELECT i.ESTAGIO, t.NOME as tag, COUNT(*) as total
                FROM ideia i
                JOIN ideias_tags it ON i.ID_IDEIA = it.IDEIA_ID
                JOIN tags t ON it.TAGS_ID = t.ID_TAGS
                GROUP BY i.ESTAGIO, t.ID_TAGS, t.NOME
                ORDER BY i.ESTAGIO, total DESC";
        
        $result = $this->conexao->query($sql);
        
        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
        
        return $dados;
    }
}
?>