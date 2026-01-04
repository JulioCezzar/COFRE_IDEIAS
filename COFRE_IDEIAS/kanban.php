<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>📋 Kanban - Cofre de Ideias</title>
    <link rel="stylesheet" href="theme_site/theme.css">
    <link rel="stylesheet" href="Style_visualiza.css">
	<link rel="stylesheet" href="theme_site/light-theme.css">
	<link rel="stylesheet" href="theme_site/dark-theme.css">
    <style>
        .kanban-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .kanban-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .kanban-column {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-height: 600px;
            transition: all 0.3s ease;
        }

        .column-header {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 1.2em;
        }

        .column-inicial .column-header {
            background: #ff6b6b;
            color: white;
        }

        .column-desenvolvimento .column-header {
            background: #4ecdc4;
            color: white;
        }

        .column-conclusao .column-header {
            background: #45b7d1;
            color: white;
        }

        .ideia-card {
            background: var(--bg-color);
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: grab;
            transition: all 0.3s ease;
            position: relative;
            user-select: none;
        }

        .ideia-card:active {
            cursor: grabbing;
            transform: rotate(2deg);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .ideia-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .ideia-card h4 {
            margin: 0 0 10px 0;
            color: var(--text-color);
            font-size: 1.1em;
        }

        .ideia-card p {
            margin: 0 0 10px 0;
            color: var(--text-color);
            font-size: 0.9em;
            line-height: 1.4;
        }

        .card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
        }

        .card-tag {
            background: var(--primary-color);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7em;
        }

        .card-priority {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7em;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .priority-ALTA { background: #ff4757; color: white; }
        .priority-MEDIA { background: #ffa502; color: white; }
        .priority-BAIXA { background: #2ed573; color: white; }

        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .card-actions a {
            padding: 4px 8px;
            font-size: 0.8em;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .btn-editar-card {
            background: #007bff;
            color: white;
        }

        .btn-deletar-card {
            background: #dc3545;
            color: white;
        }

        .btn-editar-card:hover, .btn-deletar-card:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        .drag-over {
            background: rgba(76, 175, 80, 0.1);
            border: 2px dashed #4CAF50;
        }

        .card-count {
            background: rgba(0,0,0,0.1);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8em;
            margin-left: 8px;
        }

        .navigation-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .btn-kanban {
            padding: 12px 25px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .btn-kanban:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .empty-column {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-style: italic;
        }

        /* Loading animation */
        .updating {
            opacity: 0.7;
            background: #f8f9fa !important;
        }

        .dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }

        .drag-ghost {
            opacity: 0.4;
            transform: scale(0.95);
        }

        /* Responsividade */
        @media (max-width: 1024px) {
            .kanban-board {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .kanban-column {
                min-height: auto;
            }
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

<div class="kanban-container">
    <!-- BOTÕES DE NAVEGAÇÃO -->
    <div class="navigation-buttons">
        <a href="index.html" class="btn-kanban">🏠 Início</a>
        <a href="visualizar_ideias.php" class="btn-kanban">💡 Lista de Ideias</a>
        <a href="Registrar_nova_ideia.php" class="btn-kanban">➕ Nova Ideia</a>
        <a href="painel_analytics.php" class="btn-kanban">📊 Analytics</a>
    </div>

    <h1 style="text-align: center; margin-bottom: 30px;">📋 Kanban - Gestão de Ideias</h1>

    <div class="kanban-board" id="kanbanBoard">
        <!-- Coluna 1: Estágio Inicial -->
        <div class="kanban-column column-inicial" data-estagio="inicial">
            <div class="column-header">
                💡 Estágio Inicial <span class="card-count" id="count-inicial">0</span>
            </div>
            <div class="column-content" id="content-inicial">
                <!-- Cards serão carregados aqui via JavaScript -->
            </div>
        </div>

        <!-- Coluna 2: Em Desenvolvimento -->
        <div class="kanban-column column-desenvolvimento" data-estagio="desenvolvimento">
            <div class="column-header">
                🚀 Em Desenvolvimento <span class="card-count" id="count-desenvolvimento">0</span>
            </div>
            <div class="column-content" id="content-desenvolvimento">
                <!-- Cards serão carregados aqui via JavaScript -->
            </div>
        </div>

        <!-- Coluna 3: Concluído -->
        <div class="kanban-column column-conclusao" data-estagio="conclusao">
            <div class="column-header">
                ✅ Concluído <span class="card-count" id="count-conclusao">0</span>
            </div>
            <div class="column-content" id="content-conclusao">
                <!-- Cards serão carregados aqui via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
class KanbanManager {
    constructor() {
        this.columns = document.querySelectorAll('.kanban-column');
        this.draggedCard = null;
        this.originalColumn = null;
        this.init();
    }

    init() {
        this.loadIdeias();
        this.setupDragAndDrop();
    }

    async loadIdeias() {
        try {
            const response = await fetch('kanban_api.php?action=getIdeias');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const ideias = await response.json();
            
            this.renderIdeias(ideias);
            this.updateCounters(ideias);
        } catch (error) {
            console.error('Erro ao carregar ideias:', error);
            this.showError('Erro ao carregar ideias. Tente recarregar a página.');
        }
    }

    renderIdeias(ideias) {
        // Limpar conteúdo das colunas
        document.querySelectorAll('.column-content').forEach(column => {
            column.innerHTML = '';
        });

        // Renderizar cada ideia na coluna correta
        ideias.forEach(ideia => {
            const column = document.getElementById(`content-${ideia.ESTAGIO}`);
            if (column) {
                column.appendChild(this.createCard(ideia));
            }
        });

        // Adicionar mensagem para colunas vazias
        this.updateEmptyColumns();
    }

    createCard(ideia) {
        const card = document.createElement('div');
        card.className = 'ideia-card';
        card.draggable = true;
        card.dataset.ideiaId = ideia.ID_IDEIA;
        card.dataset.estagio = ideia.ESTAGIO;
        
        const priorityClass = `priority-${ideia.PRIORIDADE}`;
        
        card.innerHTML = `
            <div class="card-priority ${priorityClass}">${ideia.PRIORIDADE}</div>
            <h4>${this.escapeHtml(ideia.TITULO)}</h4>
            <p>${this.escapeHtml(ideia.DESCRICAO.substring(0, 100))}${ideia.DESCRICAO.length > 100 ? '...' : ''}</p>
            ${ideia.TAGS ? `<div class="card-tags">${this.renderTags(ideia.TAGS)}</div>` : ''}
            <div class="card-actions">
                <a href="editar_ideia.php?id=${ideia.ID_IDEIA}" class="btn-editar-card">✏️ Editar</a>
                <a href="deletar_ideia.php?id=${ideia.ID_IDEIA}" 
                   onclick="return confirm('Tem certeza que deseja deletar esta ideia?');" 
                   class="btn-deletar-card">🗑️ Deletar</a>
            </div>`;

        // Adicionar eventos de drag diretamente no card
        card.addEventListener('dragstart', (e) => {
            this.handleDragStart(e, card);
        });

        card.addEventListener('dragend', (e) => {
            this.handleDragEnd(e, card);
        });

        return card;
    }

    renderTags(tagsString) {
        if (!tagsString) return '';
        const tags = tagsString.split(', ');
        return tags.map(tag => `<span class="card-tag">${this.escapeHtml(tag)}</span>`).join('');
    }

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    updateCounters(ideias) {
        const counts = {
            inicial: 0,
            desenvolvimento: 0,
            conclusao: 0
        };

        ideias.forEach(ideia => {
            counts[ideia.ESTAGIO]++;
        });

        Object.keys(counts).forEach(estagio => {
            const counter = document.getElementById(`count-${estagio}`);
            if (counter) {
                counter.textContent = counts[estagio];
            }
        });
    }

    updateEmptyColumns() {
        this.columns.forEach(column => {
            const content = column.querySelector('.column-content');
            const estagio = column.dataset.estagio;
            
            if (content.children.length === 0 || 
                (content.children.length === 1 && content.querySelector('.empty-column'))) {
                content.innerHTML = `<div class="empty-column">Nenhuma ideia neste estágio</div>`;
            }
        });
    }

    setupDragAndDrop() {
        // Configurar eventos para cada coluna
        this.columns.forEach(column => {
            column.addEventListener('dragover', (e) => this.handleDragOver(e));
            column.addEventListener('drop', (e) => this.handleDrop(e));
            column.addEventListener('dragenter', (e) => this.handleDragEnter(e));
            column.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        });

        // Prevenir comportamento padrão do drag
        document.addEventListener('dragover', (e) => e.preventDefault());
        document.addEventListener('drop', (e) => e.preventDefault());
    }

    handleDragStart(e, card) {
        console.log('Drag start:', card.dataset.ideiaId);
        this.draggedCard = card;
        this.originalColumn = card.closest('.kanban-column');
        
        if (!this.draggedCard || !this.originalColumn) {
            console.error('Elementos não encontrados durante drag start');
            return;
        }
        
        card.classList.add('dragging');
        
        // Usar setData para o drag funcionar
        e.dataTransfer.setData('text/plain', card.dataset.ideiaId);
        e.dataTransfer.effectAllowed = 'move';
        
        // Criar um ghost image personalizado
        setTimeout(() => {
            card.classList.add('drag-ghost');
        }, 0);
    }

    handleDragEnd(e, card) {
        console.log('Drag end');
        card.classList.remove('dragging', 'drag-ghost');
        
        // Remover classes de drag-over de todas as colunas
        this.columns.forEach(column => {
            column.classList.remove('drag-over');
        });
        
        // Limpar referências
        setTimeout(() => {
            this.draggedCard = null;
            this.originalColumn = null;
        }, 100);
    }

    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    handleDragEnter(e) {
        e.preventDefault();
        const column = e.target.closest('.kanban-column');
        if (column && this.draggedCard && column !== this.originalColumn) {
            column.classList.add('drag-over');
        }
    }

    handleDragLeave(e) {
        const column = e.target.closest('.kanban-column');
        if (column && !column.contains(e.relatedTarget)) {
            column.classList.remove('drag-over');
        }
    }

    async handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const column = e.target.closest('.kanban-column');
        
        // Verificações de segurança
        if (!column) {
            console.error('Coluna não encontrada');
            return;
        }
        
        if (!this.draggedCard) {
            console.error('Nenhum card sendo arrastado');
            return;
        }
        
        if (!this.originalColumn) {
            console.error('Coluna original não definida');
            return;
        }

        const ideiaId = this.draggedCard.dataset.ideiaId;
        const novoEstagio = column.dataset.estagio;
        const estagioAtual = this.draggedCard.dataset.estagio;

        // Validações
        if (!ideiaId || !novoEstagio || !estagioAtual) {
            console.error('Dados do card incompletos:', { ideiaId, novoEstagio, estagioAtual });
            return;
        }

        // Se for o mesmo estágio, não fazer nada
        if (estagioAtual === novoEstagio) {
            console.log('Mesmo estágio, ignorando...');
            column.classList.remove('drag-over');
            return;
        }

        console.log(`Movendo ideia ${ideiaId} de ${estagioAtual} para ${novoEstagio}`);

        // Adicionar efeito visual de atualização
        this.draggedCard.classList.add('updating');

        try {
            // 1. Primeiro atualiza no banco de dados
            console.log("Enviando requisição para o servidor...");
            const resultado = await this.updateEstagio(ideiaId, novoEstagio);
            console.log("Resposta do servidor:", resultado);
            
            if (resultado.success) {
                console.log("Sucesso! Atualizando interface...");
                
                // 2. Atualiza o dataset do card
                this.draggedCard.dataset.estagio = novoEstagio;
                
                // 3. Move visualmente o card para a nova coluna
                const novaColunaContent = column.querySelector('.column-content');
                if (novaColunaContent) {
                    // Remove mensagem de coluna vazia se existir
                    const emptyMsg = novaColunaContent.querySelector('.empty-column');
                    if (emptyMsg) {
                        emptyMsg.remove();
                    }
                    
                    // Adiciona o card na nova coluna
                    novaColunaContent.appendChild(this.draggedCard);
                    
                    // Atualiza a coluna original
                    this.updateEmptyColumns();
                    
                    // Atualiza contadores
                    this.updateCountersFromDOM();
                }
                
                this.showSuccess('Ideia movida com sucesso!');
            } else {
                throw new Error(resultado.message || 'Erro ao mover ideia');
            }
            
        } catch (error) {
            console.error('Erro ao atualizar estágio:', error);
            this.showError('Erro ao mover a ideia: ' + error.message);
            
            // Reverter visualmente para a coluna original
            if (this.originalColumn) {
                const originalContent = this.originalColumn.querySelector('.column-content');
                if (originalContent && this.draggedCard) {
                    originalContent.appendChild(this.draggedCard);
                }
            }
        } finally {
            if (this.draggedCard) {
                this.draggedCard.classList.remove('updating', 'drag-ghost');
            }
            column.classList.remove('drag-over');
            
            // Limpar referências
            this.draggedCard = null;
            this.originalColumn = null;
        }
    }

    async updateEstagio(ideiaId, novoEstagio) {
        const formData = new FormData();
        formData.append('ideia_id', ideiaId);
        formData.append('novo_estagio', novoEstagio);

        try {
            const response = await fetch('kanban_api.php?action=updateEstagio', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Erro na requisição HTTP: ' + response.status);
            }

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Erro na requisição fetch:', error);
            throw new Error('Falha na comunicação com o servidor');
        }
    }

    updateCountersFromDOM() {
        const counts = {
            inicial: 0,
            desenvolvimento: 0,
            conclusao: 0
        };

        // Contar cards em cada coluna baseado no DOM atual
        this.columns.forEach(column => {
            const estagio = column.dataset.estagio;
            const content = column.querySelector('.column-content');
            const cards = content.querySelectorAll('.ideia-card');
            counts[estagio] = cards.length;
        });

        // Atualizar os contadores na interface
        Object.keys(counts).forEach(estagio => {
            const counter = document.getElementById(`count-${estagio}`);
            if (counter) {
                counter.textContent = counts[estagio];
            }
        });
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showNotification(message, type) {
        const backgroundColor = type === 'error' ? '#dc3545' : '#28a745';
        
        // Remover notificações existentes
        document.querySelectorAll('.kanban-notification').forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = 'kanban-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${backgroundColor};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            max-width: 300px;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 500);
        }, 3000);
    }
}

// Inicializar o Kanban quando a página carregar
document.addEventListener('DOMContentLoaded', () => {
    new KanbanManager();
});

// Prevenir comportamento padrão em links durante o drag
document.addEventListener('dragstart', (e) => {
    if (e.target.tagName === 'A') {
        e.preventDefault();
    }
});
</script>

<script src="theme_site/theme.js"></script>

</body>
</html>