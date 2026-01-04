<?php
// Iniciar PHP no topo para processar o formulário
include 'conexao.php';

$sucesso = false;
$erroGeral = '';
$erroUpload = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["titulo"]);
    $descricao = trim($_POST["descricao"]);
    $prioridade = trim($_POST["prioridade"]);
    $tagsInput = trim($_POST["tags"]);
    
    // Processar upload da imagem
    $imagemNome = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagem'];
        
        // Configurações do upload
        $pastaUpload = 'uploads/';
        $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Verificar se a pasta existe, se não, criar
        if (!is_dir($pastaUpload)) {
            mkdir($pastaUpload, 0755, true);
        }
        
        // Validar extensão
        if (in_array($extensao, $extensoesPermitidas)) {
            // Gerar nome único para a imagem
            $imagemNome = uniqid() . '_' . time() . '.' . $extensao;
            $caminhoImagem = $pastaUpload . $imagemNome;
            
            // Mover arquivo para a pasta de uploads
            if (move_uploaded_file($imagem['tmp_name'], $caminhoImagem)) {
                // Upload bem sucedido
            } else {
                $imagemNome = null;
                $erroUpload = "Erro ao fazer upload da imagem.";
            }
        } else {
            $erroUpload = "Formato de imagem não permitido. Use JPG, PNG ou GIF.";
        }
    }

    // Inserir a ideia no banco
    $stmt = $conexao->prepare("INSERT INTO ideia (TITULO, DESCRICAO, PRIORIDADE, IMAGEM) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $titulo, $descricao, $prioridade, $imagemNome);
    
    if ($stmt->execute()) {
        $idIdeia = $stmt->insert_id;
        $stmt->close();

        // Processar tags
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

                // Fazer a ligação da ideia ↔ tag
                $link = $conexao->prepare("INSERT INTO ideias_tags (IDEIA_ID, TAGS_ID) VALUES (?, ?)");
                $link->bind_param("ii", $idIdeia, $idTag);
                $link->execute();
                $link->close();
            }
        }

        $sucesso = true;
    } else {
        $erroGeral = "Erro ao registrar ideia: " . $conexao->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Registrar Ideia - Cofre de Ideias</title>
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
        max-width: 500px;
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
        max-width: 600px;
        margin: 0 auto;
    }

    /* ESTILOS SIMPLIFICADOS DO UPLOAD */
    .image-upload-container {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 2px dashed #dee2e6;
    }

    [data-theme="dark"] .image-upload-container {
        background: #2a2a2a;
        border-color: #444;
    }

    .upload-label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        color: #333;
    }

    [data-theme="dark"] .upload-label {
        color: #ccc;
    }

    .file-input {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    [data-theme="dark"] .file-input {
        background: #333;
        border-color: #555;
        color: white;
    }

    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        margin: 10px 0;
        display: none;
        border: 2px solid #e1e5e9;
    }

    [data-theme="dark"] .image-preview {
        border-color: #444;
    }

    .remove-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 10px;
        display: none;
    }

    .remove-btn:hover {
        background: #c82333;
    }

    .image-info {
        font-size: 0.8em;
        color: #666;
        margin-top: 5px;
    }

    [data-theme="dark"] .image-info {
        color: #999;
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

    .success-message {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 30px;
        border-radius: 20px;
        max-width: 500px;
        margin: 30px auto;
        text-align: center;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    }

    .success-icon {
        font-size: 3em;
        margin-bottom: 15px;
        display: block;
    }

    .success-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .success-actions a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 25px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .success-actions a:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }
	
	 .remove-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 10px;
        display: none;
    }

    .remove-btn:hover {
        background: #c82333;
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
    <a href="index.html" style="background: var(--primary-color);">
        🏠 Voltar ao Início
    </a>
    <a href="visualizar_ideias.php" style="background: #28a745;">
        👁️ Visualizar Ideias
    </a>
</div>

<!-- Container Principal -->
<div class="main-container">
    <h2>💡 Registrar Nova Ideia</h2>

    <?php if ($sucesso): ?>
        <div class="success-message">
            <span class="success-icon">🎉</span>
            <h3>Ideia Registrada com Sucesso!</h3>
            <p>Sua ideia foi salva no cofre e está pronta para ser desenvolvida.</p>
            <div class="success-actions">
                <a href="registrar_ideia.php">➕ Registrar Outra Ideia</a>
                <a href="visualizar_ideias.php">👁️ Visualizar Todas as Ideias</a>
                <a href="kanban.php">📋 Ver no Kanban</a>
            </div>
        </div>
    <?php else: ?>
        <?php if ($erroGeral): ?>
            <div class="alert alert-error"><?php echo $erroGeral; ?></div>
        <?php endif; ?>

        <?php if ($erroUpload): ?>
            <div class="alert alert-error"><?php echo $erroUpload; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="titulo">Título da Ideia:</label>
            <input type="text" id="titulo" name="titulo" required placeholder="Digite um título criativo para sua ideia" value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="5" required placeholder="Descreva sua ideia em detalhes..."><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>

            <label for="prioridade">Prioridade:</label>
            <select id="prioridade" name="prioridade" required>
                <option value="">Selecione a prioridade</option>
                <option value="Alta" <?php echo (isset($_POST['prioridade']) && $_POST['prioridade'] == 'Alta') ? 'selected' : ''; ?>>🚨 Alta</option>
                <option value="Média" <?php echo (isset($_POST['prioridade']) && $_POST['prioridade'] == 'Média') ? 'selected' : ''; ?>>⚠️ Média</option>
                <option value="Baixa" <?php echo (isset($_POST['prioridade']) && $_POST['prioridade'] == 'Baixa') ? 'selected' : ''; ?>>✅ Baixa</option>
            </select>

           <!-- UPLOAD DE IMAGEM - VERSÃO CORRIGIDA -->
			<div class="image-upload-container">
				<label class="upload-label">📷 Imagem da Ideia (Opcional)</label>
				<button type="button" class="upload-btn" onclick="document.getElementById('imagem').click()">
				📁 Escolher Imagem
				</button>
				<input type="file" id="imagem" name="imagem" class="file-input" accept="image/*" onchange="previewImage(this)">
				<div class="image-info">Formatos: JPG, PNG, GIF, WebP • Máx: 2MB</div>
    
					<img id="imagePreview" class="image-preview" alt="Preview da imagem">
			<button type="button" id="removeImage" class="remove-btn" onclick="removeImage()">🗑️ Remover Imagem</button>
			</div>

            <label for="tags">Tags:</label>
            <input type="text" id="tags" name="tags" placeholder="Ex: faculdade, estudo, projeto, pessoal" value="<?php echo isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''; ?>">
            <small>Separe várias tags com vírgula ( , )</small>

            <button type="submit" class="btn-submit">🎯 Registrar Ideia</button>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('imagem');
    const preview = document.getElementById('imagePreview');
    const removeBtn = document.getElementById('removeImage');
    
    // Preview da imagem
    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (file.size > maxSize) {
                alert('A imagem é muito grande. Escolha uma imagem de até 2MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                removeBtn.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remover imagem
    removeBtn.addEventListener('click', function() {
        fileInput.value = '';
        preview.style.display = 'none';
        removeBtn.style.display = 'none';
    });
});
</script>

<script src="theme_site/theme.js"></script>

</body>
</html>