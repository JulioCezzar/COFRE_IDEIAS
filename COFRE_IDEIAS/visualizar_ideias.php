<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Visualizar Ideias</title>
<link rel="stylesheet" href="theme_site/theme.css">
<link rel="stylesheet" href="Style_visualiza.css">
<link rel="stylesheet" href="theme_site/light-theme.css">
<link rel="stylesheet" href="theme_site/dark-theme.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
    /* ===== ESTILOS ORIGINAIS DO SITE ===== */
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

    .search-container {
        max-width: 800px;
        margin: 0 auto 30px auto;
    }

    .search-form {
        background: var(--card-bg);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .search-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .search-bar {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 1em;
        background: var(--bg-color);
        color: var(--text-color);
    }

    .search-btn, .pdf-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .pdf-btn {
        background: #dc3545;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.5em;
        color: var(--text-color);
    }

    .ideia-item {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.1);
    }

    .ideia-content {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }

    .ideia-image-container {
        flex-shrink: 0;
        width: 250px;
        text-align: center;
    }

    .ideia-image {
        max-width: 100%;
        max-height: 200px;
        border-radius: 10px;
        border: 2px solid #e1e5e9;
    }

    .no-image {
        width: 100%;
        height: 150px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-style: italic;
    }

    .ideia-text {
        flex: 1;
    }

    .ideia-item h3 {
        margin: 0 0 15px 0;
        color: var(--text-color);
        font-size: 1.4em;
        font-weight: 700;
    }

    .ideia-item p {
        margin: 0 0 15px 0;
        color: var(--text-color);
        line-height: 1.6;
        font-size: 1em;
    }

    .tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 15px 0;
    }

    .tag {
        background: var(--primary-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 500;
    }

    .ideia-meta {
        display: flex;
        gap: 20px;
        margin: 15px 0;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9em;
        color: var(--text-color);
    }

    .priority-alta {
        color: #dc3545;
        font-weight: bold;
    }

    .priority-media {
        color: #ffc107;
        font-weight: bold;
    }

    .priority-baixa {
        color: #28a745;
        font-weight: bold;
    }

    .ideia-actions {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .btn-editar, .btn-deletar {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9em;
    }

    .btn-editar {
        background: #007bff;
        color: white;
    }

    .btn-deletar {
        background: #dc3545;
        color: white;
    }

    .search-info {
        background: var(--card-bg);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid var(--primary-color);
    }

    .search-results-count {
        text-align: center;
        padding: 15px;
        background: var(--card-bg);
        border-radius: 8px;
        margin-top: 20px;
    }

    .no-ideas {
        text-align: center;
        padding: 50px 20px;
        background: var(--card-bg);
        border-radius: 12px;
        margin: 20px 0;
    }

    /* Botão de tema */
    .theme-btn-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .troca_tema {
        background: #000000;
        border: 2px solid #007bff;
        border-radius: 50%;
        padding: 12px;
        cursor: pointer;
        font-size: 1.4rem;
        transition: all 0.3s ease;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== ESTILOS ESPECÍFICOS PARA PDF ===== */
    
    /* Loader para geração de PDF */
    .pdf-loader {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        color: white;
        font-size: 1.2em;
    }
    
    .pdf-loader-spinner {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
        margin-bottom: 15px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Modal de prévia */
    .pdf-preview-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 10000;
        justify-content: center;
        align-items: center;
    }
    
    .pdf-preview-content {
        background: white;
        width: 90%;
        height: 90%;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .pdf-preview-header {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .pdf-preview-body {
        flex: 1;
        overflow: auto;
        padding: 20px;
        background: #f5f5f5;
    }
    
    .pdf-preview-actions {
        padding: 15px;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .preview-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
    }
    
    .preview-close {
        background: #6c757d;
        color: white;
    }
    
    .preview-download {
        background: #007bff;
        color: white;
    }
    
    .preview-print {
        background: #28a745;
        color: white;
    }
</style>
</head>
<body>

<!-- BOTÃO PARA TROCAR TEMA -->
<div class="theme-btn-container">
    <button id="trocar_tema" class="troca_tema">
        <span class="rock">🤘</span>
        <span class="peace">✌️</span>
    </button>
</div>

<!-- BOTÕES DE NAVEGAÇÃO -->
<div class="top-buttons">
    <a href="index.html" style="background: var(--primary-color);">
        🏠 Voltar ao Início
    </a>
    <a href="Registrar_nova_ideia.php" style="background: #28a745;">
        ➕ Nova Ideia
    </a>
    <a href="kanban.php" style="background: #17a2b8;">
        📋 Kanban
    </a>
    <a href="painel_analytics.php" style="background: #6f42c1;">
        📊 Analytics
    </a>
</div>

<!-- SISTEMA PARA BARRA DE BUSCA E PDF -->
<div class="search-container">
    <form action="visualizar_ideias.php" method="GET" class="search-form">
        <div class="search-wrapper">
            <input type="text" class="search-bar" name="busca" placeholder="🔍 Buscar ideias por tags (separadas por vírgula ou espaço)..." id="barra-busca" value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">
            <button type="submit" class="search-btn">Buscar</button>
            <button type="button" id="btn-pdf-preview" class="pdf-btn">
                📄 Gerar PDF
            </button>
        </div>
        <div class="search-example">
            <small>Exemplo: php, javascript, design</small>
        </div>
    </form>
</div>

<!-- Loader para geração de PDF -->
<div id="pdf-loader" class="pdf-loader">
    <div class="pdf-loader-spinner"></div>
    <p>Gerando PDF, por favor aguarde...</p>
</div>

<!-- Modal de prévia do PDF -->
<div id="pdf-preview-modal" class="pdf-preview-modal">
    <div class="pdf-preview-content">
        <div class="pdf-preview-header">
            <h3>Prévia do PDF</h3>
            <button id="preview-close" class="preview-btn preview-close">Fechar</button>
        </div>
        <div class="pdf-preview-body" id="pdf-preview-body">
            <!-- Conteúdo da prévia será inserido aqui -->
        </div>
        <div class="pdf-preview-actions">
            <button id="preview-download" class="preview-btn preview-download">Baixar PDF</button>
            <button id="preview-print" class="preview-btn preview-print">Imprimir</button>
        </div>
    </div>
</div>

<div class="container">
    <h1>💡 Suas Ideias</h1>

    <?php
        include 'conexao.php';
        include 'buscar_ideias.php';

        // Verificar se há busca
        $busca = isset($_GET['busca']) ? $_GET['busca'] : '';
        $resultadoBusca = null;
        
        if (!empty($busca)) {
            // Buscar ideias por tags
            $resultadoBusca = buscarIdeiasPorTags($busca, $conexao);
            
            // Mostrar mensagem de busca
            echo "<div class='search-info'>";
            echo "<p>🔍 Buscando por: <strong>" . htmlspecialchars($busca) . "</strong></p>";
            echo "</div>";
        }
        
        // Se não há busca ou busca retornou false, buscar todas as ideias
        if (empty($busca) || $resultadoBusca === false) {
            $result = buscarTodasIdeias($conexao);
        } else {
            $result = $resultadoBusca;
        }

        if ($result->num_rows > 0) {
            $contador = 0;
            while ($row = $result->fetch_assoc()) {
                $contador++;
                $prioridadeClass = 'priority-' . strtolower($row['PRIORIDADE']);
                
                echo "<div class='ideia-item' data-id='{$row['ID_IDEIA']}'>";
                echo "<div class='ideia-content'>";
                
                // LADO ESQUERDO: IMAGEM
                echo "<div class='ideia-image-container'>";
                if (!empty($row['IMAGEM']) && file_exists('uploads/' . $row['IMAGEM'])) {
                    echo "<img src='uploads/{$row['IMAGEM']}' alt='Imagem da ideia: {$row['TITULO']}' class='ideia-image'>";
                } else {
                    echo "<div class='no-image'>";
                    echo "📷<br>";
                    echo "<span style='font-size: 0.9em;'>Sem imagem</span>";
                    echo "</div>";
                }
                echo "</div>";
                
                // LADO DIREITO: CONTEÚDO
                echo "<div class='ideia-text'>";
                echo "<h3>{$row['TITULO']}</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['DESCRICAO'])) . "</p>";
                
                // TAGS
                if (!empty($row['TAGS'])) {
                    echo "<div class='tags'>";
                    $tags = explode(', ', $row['TAGS']);
                    foreach ($tags as $tag) {
                        if (!empty(trim($tag))) {
                            // Destacar tags que correspondem à busca
                            $tagClass = '';
                            if (!empty($busca)) {
                                $tagsBusca = preg_split('/[\s,]+/', strtolower($busca));
                                if (in_array(strtolower(trim($tag)), $tagsBusca)) {
                                    $tagClass = 'tag-destaque';
                                }
                            }
                            echo "<span class='tag {$tagClass}'>" . htmlspecialchars(trim($tag)) . "</span>";
                        }
                    }
                    echo "</div>";
                }
                
                // METADADOS
                echo "<div class='ideia-meta'>";
                echo "<span class='meta-item'>";
                echo "<strong>Prioridade:</strong> ";
                echo "<span class='{$prioridadeClass}'>" . htmlspecialchars($row['PRIORIDADE']) . "</span>";
                echo "</span>";
                
                echo "<span class='meta-item'>";
                echo "<strong>Estágio:</strong> ";
                $estagioText = [
                    'inicial' => '💡 Inicial',
                    'desenvolvimento' => '🚀 Desenvolvimento', 
                    'conclusao' => '✅ Concluído'
                ];
                $estagio = $row['ESTAGIO'] ?? 'inicial'; // Valor padrão se não existir
				echo $estagioText[$estagio] ?? $estagio;
                echo "</div>";

                // AÇÕES
                echo "<div class='ideia-actions'>";
                echo "<a href='editar_ideia.php?id={$row['ID_IDEIA']}' class='btn-editar'>✏️ Editar</a>";
                echo "<a href='deletar_ideia.php?id={$row['ID_IDEIA']}' onclick=\"return confirm('Tem certeza que deseja deletar esta ideia?');\" class='btn-deletar'>🗑️ Deletar</a>";
                echo "</div>";
                
                echo "</div>"; // fecha ideia-text
                echo "</div>"; // fecha ideia-content
                echo "</div>"; // fecha ideia-item
            }
            
            // Mostrar contador de resultados
            if (!empty($busca)) {
                echo "<div class='search-results-count'>";
                echo "<p>📊 <strong>{$contador}</strong> ideia(s) encontrada(s) para sua busca.</p>";
                echo "</div>";
            }
        } else {
            echo "<div class='no-ideas'>";
            if (!empty($busca)) {
                echo "<p>🔍 Nenhuma ideia encontrada para: <strong>" . htmlspecialchars($busca) . "</strong></p>";
                echo "<p>Tente buscar por outras tags ou <a href='visualizar_ideias.php' style='color: var(--primary-color);'>ver todas as ideias</a></p>";
            } else {
                echo "<p>📝 Nenhuma ideia encontrada.</p>";
                echo "<p>Que tal criar sua primeira ideia?</p>";
                echo "<a href='Registrar_nova_ideia.php' style='display: inline-block; margin-top: 15px; padding: 12px 25px; background: var(--primary-color); color: white; text-decoration: none; border-radius: 8px;'>➕ Criar Primeira Ideia</a>";
            }
            echo "</div>";
        }

        $conexao->close();
    ?>

</div>

<script src="theme_site/theme.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPdfPreview = document.getElementById('btn-pdf-preview');
        const pdfLoader = document.getElementById('pdf-loader');
        const pdfPreviewModal = document.getElementById('pdf-preview-modal');
        const pdfPreviewBody = document.getElementById('pdf-preview-body');
        const previewClose = document.getElementById('preview-close');
        const previewDownload = document.getElementById('preview-download');
        const previewPrint = document.getElementById('preview-print');
        
        let pdfBlob = null;
        
        // Função para gerar o conteúdo do PDF
        function generatePdfContent() {
            const cards = document.querySelectorAll('.ideia-item');
            const totalCards = cards.length;
            
            let pdfContent = `
                <div style="font-family: Arial, sans-serif; background: white; color: black; padding: 15mm;">
                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2c3e50; padding-bottom: 15px;">
                        <h1 style="color: #000000 !important; font-size: 24px; font-weight: bold; margin: 0 0 10px 0;">💡 Catálogo de Ideias</h1>
                        <div style="color: #7f8c8d; font-size: 12px;">
                            Total de ${totalCards} ideias | Gerado em ${new Date().toLocaleDateString('pt-BR')}
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
            `;
            
            // Adicionar todos os cards
            for (let i = 0; i < totalCards; i++) {
                const card = cards[i];
                
                // Clonar o card para manipulação
                const cardClone = card.cloneNode(true);
                
                // Remover botões de ação
                const actions = cardClone.querySelector('.ideia-actions');
                if (actions) actions.remove();
                
                // Extrair dados do card
                const title = cardClone.querySelector('h3').textContent;
                const description = cardClone.querySelector('p').textContent;
                
                // Extrair tags
                let tagsHtml = '';
                const tags = cardClone.querySelectorAll('.tag');
                if (tags.length > 0) {
                    tagsHtml = '<div style="display: flex; flex-wrap: wrap; gap: 5px; margin: 10px 0;">';
                    tags.forEach(tag => {
                        tagsHtml += `<span style="background: #3498db; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">${tag.textContent}</span>`;
                    });
                    tagsHtml += '</div>';
                }
                
                // Extrair metadados
                const metaItems = cardClone.querySelectorAll('.meta-item');
                let priority = '';
                let stage = '';
                
                metaItems.forEach(item => {
                    if (item.textContent.includes('Prioridade:')) {
                        const prioritySpan = item.querySelector('span');
                        if (prioritySpan) {
                            priority = prioritySpan.textContent;
                        }
                    }
                    if (item.textContent.includes('Estágio:')) {
                        stage = item.textContent.replace('Estágio:', '').trim();
                    }
                });
                
                // Determinar classe de prioridade
                let priorityColor = '#000';
                if (priority.toLowerCase().includes('alta')) priorityColor = '#e74c3c';
                else if (priority.toLowerCase().includes('média')) priorityColor = '#f39c12';
                else if (priority.toLowerCase().includes('baixa')) priorityColor = '#27ae60';
                
                // Extrair imagem
                let imageHtml = '';
                const image = cardClone.querySelector('.ideia-image');
                if (image && image.src) {
                    imageHtml = `
                        <div style="text-align: center; margin-bottom: 10px;">
                            <img src="${image.src}" alt="Imagem da ideia: ${title}" style="max-width: 100%; max-height: 120px; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    `;
                } else {
                    imageHtml = '<div style="padding: 20px; background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 4px; color: #6c757d; font-style: italic; text-align: center; margin-bottom: 10px;">📷 Sem imagem</div>';
                }
                
                // Criar card para PDF
                pdfContent += `
                    <div class="pdf-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); break-inside: avoid;">
                        <h3 style="color: #000000 !important; margin: 0 0 10px 0; font-size: 16px; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 8px;">${title}</h3>
                        ${imageHtml}
                        <div style="font-size: 13px; line-height: 1.4; margin-bottom: 10px; color: #555;">${description}</div>
                        ${tagsHtml}
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #7f8c8d;">
                            <span style="font-weight: bold; color: ${priorityColor};">${priority}</span>
                            <span style="font-weight: bold;">${stage}</span>
                        </div>
                    </div>
                `;
            }
            
            pdfContent += `
                    </div>
                </div>
            `;
            
            return pdfContent;
        }
        
        // Abrir prévia do PDF
        btnPdfPreview.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Mostrar loader
            pdfLoader.style.display = 'flex';
            
            // Gerar conteúdo do PDF
            const pdfContent = generatePdfContent();
            
            // Exibir prévia
            pdfPreviewBody.innerHTML = pdfContent;
            
            // Configurações do PDF
            const options = {
                margin: 10,
                filename: 'catalogo-ideias.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    scrollY: 0 // Importante: evita problemas de rolagem
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait' 
                }
            };
            
            // Gerar PDF como blob para prévia
            setTimeout(() => {
                html2pdf().from(pdfContent).set(options).output('blob').then(blob => {
                    pdfBlob = blob;
                    pdfLoader.style.display = 'none';
                    pdfPreviewModal.style.display = 'flex';
                }).catch(err => {
                    console.error('Erro ao gerar PDF:', err);
                    pdfLoader.style.display = 'none';
                    alert('Erro ao gerar PDF. Tente novamente.');
                });
            }, 500);
        });
        
        // Fechar prévia
        previewClose.addEventListener('click', function() {
            pdfPreviewModal.style.display = 'none';
        });
        
        // Baixar PDF
        previewDownload.addEventListener('click', function() {
            if (pdfBlob) {
                const url = URL.createObjectURL(pdfBlob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'catalogo-ideias.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
        });
        
        // Imprimir PDF
        previewPrint.addEventListener('click', function() {
            const pdfContent = generatePdfContent();
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Imprimir Catálogo de Ideias</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 0; 
                            padding: 15mm;
                            background: white; 
                            color: black;
                        }
                        .pdf-card { 
                            border: 1px solid #ddd; 
                            border-radius: 8px; 
                            padding: 15px; 
                            margin-bottom: 15px;
                            break-inside: avoid;
                        }
                        .pdf-card h3 { 
                            color: #000; 
                            margin: 0 0 10px 0; 
                            font-size: 16px; 
                            font-weight: bold; 
                            border-bottom: 1px solid #eee;
                            padding-bottom: 8px;
                        }
                        .pdf-header { 
                            text-align: center; 
                            margin-bottom: 20px; 
                            border-bottom: 2px solid #2c3e50; 
                            padding-bottom: 15px; 
                        }
                        .pdf-header h1 { 
                            color: #000; 
                            font-size: 24px; 
                            font-weight: bold; 
                            margin: 0 0 10px 0; 
                        }
                        @media print {
                            body { margin: 0; padding: 15mm; }
                        }
                    </style>
                </head>
                <body>
                    ${pdfContent}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });
        
        // Fechar modal ao clicar fora
        pdfPreviewModal.addEventListener('click', function(e) {
            if (e.target === pdfPreviewModal) {
                pdfPreviewModal.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>