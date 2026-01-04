<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>📊 Analytics - Suas Ideias</title>
    <link rel="stylesheet" href="theme_site/theme.css">
    <link rel="stylesheet" href="Style_visualiza.css">
	<link rel="stylesheet" href="theme_site/light-theme.css">
	<link rel="stylesheet" href="theme_site/dark-theme.css">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <style>
        .analytics-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .chart-title {
            text-align: center;
            margin-bottom: 20px;
            color: var(--text-color);
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .navigation-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .btn-analytics {
            padding: 12px 25px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-analytics:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .estagio-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            margin: 2px;
        }
        
        .estagio-inicial { background: #ff6b6b; color: white; }
        .estagio-desenvolvimento { background: #4ecdc4; color: white; }
        .estagio-conclusao { background: #45b7d1; color: white; }

        /* Responsividade */
        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                padding: 15px;
            }
            
            .navigation-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-analytics {
                width: 200px;
                text-align: center;
                justify-content: center;
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

<div class="analytics-container">
    <!-- BOTÕES DE NAVEGAÇÃO -->
    <div class="navigation-buttons">
        <a href="index.html" class="btn-analytics">🏠 Início</a>
        <a href="visualizar_ideias.php" class="btn-analytics">💡 Ver Ideias</a>
        <a href="Registrar_nova_ideia.php" class="btn-analytics">➕ Nova Ideia</a>
        <a href="kanban.php" class="btn-analytics">📋 Kanban</a>
    </div>

    <h1 style="text-align: center; margin-bottom: 30px;">📊 Analytics das Ideias</h1>

    <?php
    include 'graficos.php';
    
    $analytics = new AnalyticsIdeias($conexao);
    
    // Buscar dados
    $totalIdeias = $analytics->getTotalIdeias();
    $tagsMaisUsadas = $analytics->getTagsMaisUsadas(10);
    $distribuicaoEstagios = $analytics->getDistribuicaoEstagios();
    $tagsPorEstagio = $analytics->getTagsPorEstagio();
    ?>

    <!-- CARDS DE ESTATÍSTICAS -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total de Ideias</h3>
            <div class="stat-number"><?php echo $totalIdeias; ?></div>
            <p>Ideias registradas no sistema</p>
        </div>
        
        <div class="stat-card">
            <h3>Tags Únicas</h3>
            <div class="stat-number"><?php echo count($tagsMaisUsadas); ?></div>
            <p>Tags diferentes utilizadas</p>
        </div>
        
        <div class="stat-card">
            <h3>Estágios</h3>
            <div class="stat-number">3</div>
            <p>Fases do processo</p>
        </div>
    </div>

    <!-- GRÁFICOS -->
    <div class="charts-grid">
        <!-- Gráfico de Distribuição por Estágio -->
        <div class="chart-container">
            <div class="chart-title">📈 Distribuição por Estágio</div>
            <canvas id="estagioChart"></canvas>
        </div>
        
        <!-- Gráfico de Tags Mais Usadas -->
        <div class="chart-container">
            <div class="chart-title">🏷️ Tags Mais Utilizadas</div>
            <canvas id="tagsChart"></canvas>
        </div>
        
        <!-- Gráfico de Tags por Estágio -->
        <div class="chart-container" style="grid-column: 1 / -1;">
            <div class="chart-title">🔍 Tags por Estágio de Desenvolvimento</div>
            <canvas id="tagsEstagioChart" height="80"></canvas>
        </div>
    </div>

    <!-- TABELA DETALHADA -->
    <div class="chart-container">
        <div class="chart-title">📋 Detalhamento por Estágio</div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--primary-color); color: white;">
                        <th style="padding: 12px; text-align: left;">Estágio</th>
                        <th style="padding: 12px; text-align: center;">Quantidade</th>
                        <th style="padding: 12px; text-align: center;">Porcentagem</th>
                        <th style="padding: 12px; text-align: left;">Tags Principais</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $coresEstagio = [
                        'inicial' => '#ff6b6b',
                        'desenvolvimento' => '#4ecdc4',
                        'conclusao' => '#45b7d1'
                    ];
                    
                    $nomesEstagio = [
                        'inicial' => 'Estágio Inicial',
                        'desenvolvimento' => 'Em Desenvolvimento',
                        'conclusao' => 'Concluído'
                    ];
                    
                    foreach ($distribuicaoEstagios as $estagio) {
                        $porcentagem = $totalIdeias > 0 ? round(($estagio['total'] / $totalIdeias) * 100, 1) : 0;
                        
                        // Buscar tags principais para este estágio
                        $tagsEstagio = array_filter($tagsPorEstagio, function($tag) use ($estagio) {
                            return $tag['ESTAGIO'] === $estagio['ESTAGIO'];
                        });
                        
                        $tagsPrincipais = array_slice($tagsEstagio, 0, 3);
                        ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px;">
                                <span class="estagio-badge estagio-<?php echo $estagio['ESTAGIO']; ?>">
                                    <?php echo $nomesEstagio[$estagio['ESTAGIO']]; ?>
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center; font-weight: bold;">
                                <?php echo $estagio['total']; ?>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <strong><?php echo $porcentagem; ?>%</strong>
                            </td>
                            <td style="padding: 12px;">
                                <?php
                                if (!empty($tagsPrincipais)) {
                                    foreach ($tagsPrincipais as $tag) {
                                        echo "<span class='tag' style='margin: 2px;'>" . htmlspecialchars($tag['tag']) . " ({$tag['total']})</span>";
                                    }
                                } else {
                                    echo "<em>Nenhuma tag</em>";
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Dados PHP para JavaScript
const distribuicaoEstagios = <?php echo json_encode($distribuicaoEstagios); ?>;
const tagsMaisUsadas = <?php echo json_encode($tagsMaisUsadas); ?>;
const tagsPorEstagio = <?php echo json_encode($tagsPorEstagio); ?>;

// Configuração de cores
const cores = {
    inicial: '#ff6b6b',
    desenvolvimento: '#4ecdc4',
    conclusao: '#45b7d1',
    tags: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384']
};

// Inicializar gráficos quando a página carregar
document.addEventListener('DOMContentLoaded', () => {
    // Gráfico de Distribuição por Estágio
    const ctxEstagio = document.getElementById('estagioChart').getContext('2d');
    new Chart(ctxEstagio, {
        type: 'doughnut',
        data: {
            labels: distribuicaoEstagios.map(e => {
                const nomes = {inicial: 'Inicial', desenvolvimento: 'Desenvolvimento', conclusao: 'Conclusão'};
                return `${nomes[e.ESTAGIO]} (${e.total})`;
            }),
            datasets: [{
                data: distribuicaoEstagios.map(e => e.total),
                backgroundColor: distribuicaoEstagios.map(e => cores[e.ESTAGIO]),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.raw / total) * 100);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Tags Mais Usadas
    const ctxTags = document.getElementById('tagsChart').getContext('2d');
    new Chart(ctxTags, {
        type: 'bar',
        data: {
            labels: tagsMaisUsadas.map(t => t.tag),
            datasets: [{
                label: 'Número de Ideias',
                data: tagsMaisUsadas.map(t => t.total),
                backgroundColor: cores.tags.slice(0, tagsMaisUsadas.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Número de Ideias'
                    }
                }
            }
        }
    });

    // Gráfico de Tags por Estágio
    const ctxTagsEstagio = document.getElementById('tagsEstagioChart').getContext('2d');

    // Agrupar tags por estágio
    const tagsAgrupadas = {};
    tagsPorEstagio.forEach(item => {
        if (!tagsAgrupadas[item.ESTAGIO]) {
            tagsAgrupadas[item.ESTAGIO] = [];
        }
        tagsAgrupadas[item.ESTAGIO].push(item);
    });

    // Pegar top 5 tags de cada estágio
    const datasets = [];
    Object.keys(tagsAgrupadas).forEach((estagio, index) => {
        const topTags = tagsAgrupadas[estagio].slice(0, 5);
        datasets.push({
            label: estagio === 'inicial' ? 'Estágio Inicial' : 
                   estagio === 'desenvolvimento' ? 'Em Desenvolvimento' : 'Concluído',
            data: topTags.map(t => t.total),
            backgroundColor: cores[estagio],
            borderColor: cores[estagio],
            borderWidth: 1
        });
    });

    new Chart(ctxTagsEstagio, {
        type: 'bar',
        data: {
            labels: ['Top 1', 'Top 2', 'Top 3', 'Top 4', 'Top 5'],
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Número de Ideias'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Ranking de Tags'
                    }
                }
            }
        }
    });
});
</script>

<script src="theme_site/theme.js"></script>

</body>
</html>