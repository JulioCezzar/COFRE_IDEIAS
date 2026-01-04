
function aplicarTema() {
    const temaSalvo = localStorage.getItem('theme');
    if (temaSalvo) {
        document.documentElement.setAttribute('data-theme', temaSalvo);
    }
}

function inicializarTema() {
    aplicarTema();
    
    const botaoTema = document.getElementById('trocar_tema');
    if (botaoTema) {
        botaoTema.addEventListener('click', function() {
            const temaAtual = document.documentElement.getAttribute('data-theme');
            const novoTema = temaAtual === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', novoTema);
            localStorage.setItem('theme', novoTema);
        });
    }
}

// Aplicar tema quando a página carregar
document.addEventListener('DOMContentLoaded', inicializarTema);