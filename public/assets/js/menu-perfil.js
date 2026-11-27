/*
 * Menu suspenso do perfil no cabeçalho.
 * Abre pelo avatar, fecha ao clicar fora, ao navegar ou com Esc.
 */
(() => {
    const menu = document.querySelector('[data-menu-perfil]');

    if (!menu) {
        return;
    }

    const botao = menu.querySelector('[data-menu-perfil-botao]');
    const painel = menu.querySelector('[data-menu-perfil-painel]');

    if (!botao || !painel) {
        return;
    }

    // Mantém o estado do painel e do botão em um único lugar; o CSS realça o
    // botão pelo atributo aria-expanded.
    const definirAberto = (aberto) => {
        painel.classList.toggle('hidden', !aberto);
        botao.setAttribute('aria-expanded', aberto ? 'true' : 'false');
    };

    botao.addEventListener('click', (evento) => {
        evento.stopPropagation();

        definirAberto(botao.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('click', (evento) => {
        if (!(evento.target instanceof Node) || !menu.contains(evento.target)) {
            definirAberto(false);
        }
    });

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && !painel.classList.contains('hidden')) {
            definirAberto(false);
            botao.focus();
        }
    });

    painel.addEventListener('click', (evento) => {
        if (evento.target instanceof Element && evento.target.closest('a, button')) {
            definirAberto(false);
        }
    });
})();
