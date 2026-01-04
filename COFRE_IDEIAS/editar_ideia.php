<?php
include 'conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: visualizar_ideias.php');
    exit;
}

// Buscar dados da ideia
$sql_ideia = "SELECT i.*, GROUP_CONCAT(t.NOME SEPARATOR ', ') as TAGS 
              FROM ideia i 
              LEFT JOIN ideias_tags it ON i.ID_IDEIA = it.IDEIA_ID 
              LEFT JOIN tags t ON it.TAGS_ID = t.ID_TAGS 
              WHERE i.ID_IDEIA = ? 
              GROUP BY i.ID_IDEIA";
$stmt = $conexao->prepare($sql_ideia);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ideia = $result->fetch_assoc();

if (!$ideia) {
    header('Location: visualizar_ideias.php');
    exit;
}

// Processar o formulário de edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["titulo"]);
    $descricao = trim($_POST["descricao"]);
    $prioridade = trim($_POST["prioridade"]);
    $estagio = trim($_POST["estagio"]);
    $tagsInput = trim($_POST["tags"]);
    $image_action = $_POST['image_action'] ?? 'manter';

    // Processar imagem baseado na ação escolhida
    $imagemNome = $ideia['IMAGEM']; // Mantém a imagem atual por padrão

    if ($image_action === 'remover' && !empty($ideia['IMAGEM'])) {
        // Remover imagem antiga do servidor
        $caminhoImagem = 'uploads/' . $ideia['IMAGEM'];
        if (file_exists($caminhoImagem)) {
            unlink($caminhoImagem);
        }
        $imagemNome = null;
    } elseif ($image_action === 'trocar' && isset($_FILES['nova_imagem']) && $_FILES['nova_imagem']['error'] === UPLOAD_ERR_OK) {
        // Nova imagem enviada
        $imagem = $_FILES['nova_imagem'];
        
        // Remover imagem antiga se existir
        if (!empty($ideia['IMAGEM'])) {
            $caminhoAntigo = 'uploads/' . $ideia['IMAGEM'];
            if (file_exists($caminhoAntigo)) {
                unlink($caminhoAntigo);
            }
        }
        
        // Processar nova imagem
        $pastaUpload = 'uploads/';
        $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($extensao, $extensoesPermitidas)) {
            $imagemNome = uniqid() . '_' . time() . '.' . $extensao;
            $caminhoImagem = $pastaUpload . $imagemNome;
            
            if (!move_uploaded_file($imagem['tmp_name'], $caminhoImagem)) {
                $imagemNome = $ideia['IMAGEM']; // Mantém a antiga em caso de erro
                $erroUpload = "Erro ao fazer upload da nova imagem.";
            }
        } else {
            $erroUpload = "Formato de imagem não permitido.";
            $imagemNome = $ideia['IMAGEM']; // Mantém a antiga
        }
    } elseif ($image_action === 'adicionar' && isset($_FILES['nova_imagem']) && $_FILES['nova_imagem']['error'] === UPLOAD_ERR_OK) {
        // Adicionar primeira imagem (quando não tinha antes)
        $imagem = $_FILES['nova_imagem'];
        
        $pastaUpload = 'uploads/';
        $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($extensao, $extensoesPermitidas)) {
            $imagemNome = uniqid() . '_' . time() . '.' . $extensao;
            $caminhoImagem = $pastaUpload . $imagemNome;
            
            if (!move_uploaded_file($imagem['tmp_name'], $caminhoImagem)) {
                $imagemNome = null; // Mantém sem imagem em caso de erro
                $erroUpload = "Erro ao fazer upload da imagem.";
            }
        } else {
            $erroUpload = "Formato de imagem não permitido.";
            $imagemNome = null;
        }
    }

    // Atualizar a ideia
    $stmt = $conexao->prepare("UPDATE ideia SET TITULO = ?, DESCRICAO = ?, PRIORIDADE = ?, ESTAGIO = ?, IMAGEM = ? WHERE ID_IDEIA = ?");
    $stmt->bind_param("sssssi", $titulo, $descricao, $prioridade, $estagio, $imagemNome, $id);
    
    if ($stmt->execute()) {
        // Remover tags antigas
        $conexao->query("DELETE FROM ideias_tags WHERE IDEIA_ID = $id");
        
        // Adicionar novas tags
        if (!empty($tagsInput)) {
            $tags = array_map('trim', explode(',', $tagsInput));
            
            foreach ($tags as $tagNome) {
                if ($tagNome == '') continue;
                
                // Verificar se a tag já existe
                $checkTag = $conexao->prepare("SELECT ID_TAGS FROM tags WHERE NOME = ?");
                $checkTag->bind_param("s", $tagNome);
                $checkTag->execute();
                $checkTag->bind_result($idTagExistente);
                $checkTag->fetch();
                $checkTag->close();
                
                if ($idTagExistente) {
                    $idTag = $idTagExistente;
                } else {
                    // Criar nova tag
                    $insertTag = $conexao->prepare("INSERT INTO tags (NOME) VALUES (?)");
                    $insertTag->bind_param("s", $tagNome);
                    $insertTag->execute();
                    $idTag = $insertTag->insert_id;
                    $insertTag->close();
                }
                
                // Fazer a ligação
                $link = $conexao->prepare("INSERT INTO ideias_tags (IDEIA_ID, TAGS_ID) VALUES (?, ?)");
                $link->bind_param("ii", $id, $idTag);
                $link->execute();
                $link->close();
            }
        }
        
        $sucesso = "Ideia atualizada com sucesso!";
        
        // Atualizar dados da ideia para mostrar no form
        $ideia['TITULO'] = $titulo;
        $ideia['DESCRICAO'] = $descricao;
        $ideia['PRIORIDADE'] = $prioridade;
        $ideia['ESTAGIO'] = $estagio;
        $ideia['TAGS'] = $tagsInput;
        $ideia['IMAGEM'] = $imagemNome;
    } else {
        $erroGeral = "Erro ao atualizar ideia: " . $conexao->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Ideia - Cofre de Ideias</title>
<link rel="stylesheet" href="theme_site/theme.css">
<link rel="stylesheet" href="theme_site/light-theme.css">
<link rel="stylesheet" href="theme_site/dark-theme.css">
<style>
    body {
        font-family: "Poppins", Arial, sans-serif;
        margin: 0;
        padding: 30px;
        min-height: 100vh;
    }

    .top-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .top-buttons a {
        text-decoration: none;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .top-buttons a:hover {
        filter: brightness(0.9);
        transform: translateY(-2px);
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2em;
    }

    form {
        padding: 40px;
        border-radius: 20px;
        max-width: 600px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    [data-theme="dark"] form {
        background: rgba(42, 42, 42, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
        font-size: 0.95em;
    }

    [data-theme="dark"] label {
        color: #cccccc;
    }

    input, textarea, select {
        width: 100%;
        margin-bottom: 20px;
        padding: 14px;
        border-radius: 10px;
        border: 2px solid #e1e5e9;
        font-size: 1em;
        font-family: "Poppins", Arial, sans-serif;
        background: #ffffff;
        color: #333;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    [data-theme="dark"] input, 
    [data-theme="dark"] textarea, 
    [data-theme="dark"] select {
        background: #2a2a2a;
        border: 2px solid #444;
        color: #ffffff;
    }

    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: #007bff;
        background: #f8fbff;
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.1);
        transform: translateY(-1px);
    }

    [data-theme="dark"] input:focus, 
    [data-theme="dark"] textarea:focus, 
    [data-theme="dark"] select:focus {
        border-color: #cc2233;
        background: #331111;
        box-shadow: 0 0 0 4px rgba(204, 34, 51, 0.15);
    }

    button, .btn-submit {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border: none;
        padding: 15px 25px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1em;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 10px;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    [data-theme="dark"] button, 
    [data-theme="dark"] .btn-submit {
        background: linear-gradient(135deg, #cc2233, #991a28);
        box-shadow: 0 4px 12px rgba(204, 34, 51, 0.3);
    }

    button:hover, .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 123, 255, 0.4);
    }

    [data-theme="dark"] button:hover, 
    [data-theme="dark"] .btn-submit:hover {
        box-shadow: 0 6px 18px rgba(204, 34, 51, 0.4);
    }

    small {
        color: #777;
        display: block;
        margin-top: -15px;
        margin-bottom: 20px;
        font-size: 0.85em;
    }

    [data-theme="dark"] small {
        color: #999;
    }

    .theme-btn-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .main-container {
        max-width: 800px;
        margin: 0 auto;
    }

    /* ESTILOS DA IMAGEM ATUAL */
    .current-image-container {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 2px dashed #dee2e6;
    }

    [data-theme="dark"] .current-image-container {
        background: #2a2a2a;
        border-color: #444;
    }

    .current-image {
        max-width: 300px;
        max-height: 200px;
        border-radius: 8px;
        margin: 10px 0;
        border: 2px solid #e1e5e9;
    }

    [data-theme="dark"] .current-image {
        border-color: #444;
    }

    .no-image {
        padding: 30px;
        background: #e9ecef;
        border-radius: 8px;
        color: #6c757d;
        font-style: italic;
        text-align: center;
        margin: 10px 0;
    }

    [data-theme="dark"] .no-image {
        background: #333;
        color: #999;
    }

    .image-options {
        display: flex;
        gap: 15px;
        margin: 15px 0;
        flex-wrap: wrap;
    }

    .image-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 15px;
        background: #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    [data-theme="dark"] .image-option {
        background: #333;
    }

    .image-option:hover {
        background: #dee2e6;
        transform: translateY(-2px);
    }

    [data-theme="dark"] .image-option:hover {
        background: #444;
    }

    .image-option input[type="radio"] {
        width: auto;
        margin: 0;
    }

    .image-option.selected {
        background: #007bff;
        color: white;
        border-color: #0056b3;
    }

    [data-theme="dark"] .image-option.selected {
        background: #cc2233;
        border-color: #991a28;
    }

    .new-image-upload {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-top: 15px;
        border: 2px dashed #adb5bd;
    }

    [data-theme="dark"] .new-image-upload {
        background: #2a2a2a;
        border-color: #555;
    }

    /* Mensagens */
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        text-align: center;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    [data-theme="dark"] .alert-error {
        background: #2d0b0e;
        color: #f8d7da;
        border-color: #721c24;
    }

    [data-theme="dark"] .alert-success {
        background: #0d3014;
        color: #d4edda;
        border-color: #155724;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .btn-cancel {
        background: #6c757d;
        color: white;
        text-decoration: none;
        padding: 15px 25px;
        border-radius: 10px;
        text-align: center;
        flex: 1;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }
</style>
</head>
<body>

<!-- Botão de Tema -->
<div class="theme-btn-container">
    <button id="trocar_tema" class="troca_tema">
        <span class="rock">🤘</span>
        <span class="peace">✌️</span>
    </button>
</div>

<!-- Botões de Navegação -->
<div class="top-buttons">
    <a href="visualizar_ideias.php" style="background: var(--primary-color);">
        📋 Voltar para Lista
    </a>
    <a href="index.html" style="background: #6c757d;">
        🏠 Página Inicial
    </a>
</div>

<!-- Container Principal -->
<div class="main-container">
    <h2>✏️ Editar Ideia</h2>

    <?php if (isset($sucesso)): ?>
        <div class="alert alert-success">
            ✅ <?php echo $sucesso; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($erroGeral)): ?>
        <div class="alert alert-error">
            ❌ <?php echo $erroGeral; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($erroUpload)): ?>
        <div class="alert alert-error">
            ❌ <?php echo $erroUpload; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label for="titulo">Título da Ideia:</label>
        <input type="text" id="titulo" name="titulo" required 
               value="<?php echo htmlspecialchars($ideia['TITULO']); ?>">

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" rows="5" required><?php echo htmlspecialchars($ideia['DESCRICAO']); ?></textarea>

        <label for="prioridade">Prioridade:</label>
        <select id="prioridade" name="prioridade" required>
            <option value="Alta" <?php echo ($ideia['PRIORIDADE'] == 'Alta') ? 'selected' : ''; ?>>🚨 Alta</option>
            <option value="Média" <?php echo ($ideia['PRIORIDADE'] == 'Média') ? 'selected' : ''; ?>>⚠️ Média</option>
            <option value="Baixa" <?php echo ($ideia['PRIORIDADE'] == 'Baixa') ? 'selected' : ''; ?>>✅ Baixa</option>
        </select>

        <label for="estagio">Estágio:</label>
        <select id="estagio" name="estagio" required>
            <option value="inicial" <?php echo ($ideia['ESTAGIO'] == 'inicial') ? 'selected' : ''; ?>>💡 Estágio Inicial</option>
            <option value="desenvolvimento" <?php echo ($ideia['ESTAGIO'] == 'desenvolvimento') ? 'selected' : ''; ?>>🚀 Em Desenvolvimento</option>
            <option value="conclusao" <?php echo ($ideia['ESTAGIO'] == 'conclusao') ? 'selected' : ''; ?>>✅ Concluído</option>
        </select>

        <!-- GERENCIAMENTO DE IMAGEM - VERSÃO CORRIGIDA -->
        <div class="current-image-container">
            <label style="font-weight: bold; font-size: 1.1em; margin-bottom: 15px; display: block;">
                🖼️ Gerenciar Imagem
            </label>
            
            <?php if (!empty($ideia['IMAGEM']) && file_exists('uploads/' . $ideia['IMAGEM'])): ?>
                <!-- CASO 1: Tem imagem - pode manter, remover ou trocar -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="uploads/<?php echo $ideia['IMAGEM']; ?>" 
                         alt="Imagem atual da ideia" 
                         class="current-image">
                    <div style="margin-top: 10px; color: #666; font-size: 0.9em;">
                        Imagem atual
                    </div>
                </div>
                
                <div class="image-options">
                    <label class="image-option" id="option-manter">
                        <input type="radio" name="image_action" value="manter" checked>
                        💾 Manter esta imagem
                    </label>
                    
                    <label class="image-option" id="option-remover">
                        <input type="radio" name="image_action" value="remover">
                        🗑️ Remover imagem
                    </label>
                    
                    <label class="image-option" id="option-trocar">
                        <input type="radio" name="image_action" value="trocar">
                        📁 Trocar por nova imagem
                    </label>
                </div>
                
            <?php else: ?>
                <!-- CASO 2: Não tem imagem - pode adicionar uma -->
                <div class="no-image">
                    📷 Nenhuma imagem associada a esta ideia
                </div>
                
                <div class="image-options">
                    <label class="image-option selected" id="option-adicionar">
                        <input type="radio" name="image_action" value="adicionar" checked>
                        ➕ Adicionar uma imagem
                    </label>
                    
                    <label class="image-option" id="option-manter">
                        <input type="radio" name="image_action" value="manter">
                        👌 Manter sem imagem
                    </label>
                </div>
            <?php endif; ?>
            
            <!-- ÁREA DE UPLOAD - APARECE QUANDO PRECISA -->
            <div id="newImageUpload" class="new-image-upload" style="display: none;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">
                    📂 Selecionar Nova Imagem:
                </label>
                <input type="file" name="nova_imagem" accept="image/*" style="margin-bottom: 10px;">
                <small>Formatos: JPG, PNG, GIF, WebP • Tamanho máximo: 2MB</small>
            </div>
        </div>

        <label for="tags">Tags:</label>
        <input type="text" id="tags" name="tags" 
               value="<?php echo htmlspecialchars($ideia['TAGS']); ?>"
               placeholder="Ex: faculdade, estudo, projeto, pessoal">
        <small>Separe várias tags com vírgula ( , )</small>

        <div class="form-actions">
            <a href="visualizar_ideias.php" class="btn-cancel">
                ❌ Cancelar
            </a>
            <button type="submit" class="btn-submit">
                💾 Salvar Alterações
            </button>
        </div>
    </form>
</div>

<script>
// Função para gerenciar as opções de imagem
function setupImageOptions() {
    const options = document.querySelectorAll('.image-option input[type="radio"]');
    const newImageUpload = document.getElementById('newImageUpload');
    
    options.forEach(option => {
        option.addEventListener('change', function() {
            // Remover classe selected de todas as opções
            document.querySelectorAll('.image-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Adicionar classe selected à opção atual
            this.closest('.image-option').classList.add('selected');
            
            // Mostrar/ocultar área de upload
            if (this.value === 'trocar' || this.value === 'adicionar') {
                newImageUpload.style.display = 'block';
            } else {
                newImageUpload.style.display = 'none';
            }
        });
    });
    
    // Inicializar estado
    const selectedOption = document.querySelector('.image-option input[type="radio"]:checked');
    if (selectedOption) {
        selectedOption.dispatchEvent(new Event('change'));
    }
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    setupImageOptions();
});
</script>

<script src="theme_site/theme.js"></script>

</body>
</html>
<?php $conexao->close(); ?>