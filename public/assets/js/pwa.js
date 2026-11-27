/*
 * Registro do PWA e controle do convite de instalação.
 * O service worker também é registrado nas telas de login/cadastro.
 */
(() => {
    const modoInstalado =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;

    const secao = document.getElementById('pwa-instalacao');
    const botao = document.getElementById('pwa-instalar');
    const status = document.getElementById('pwa-status');

    let eventoInstalacao = null;

    const mostrarStatus = (texto) => {
        if (!status) {
            return;
        }

        status.textContent = texto;
        status.classList.remove('hidden');
    };

    if (modoInstalado && secao) {
        secao.classList.add('hidden');
    }

    window.addEventListener('beforeinstallprompt', (evento) => {
        evento.preventDefault();
        eventoInstalacao = evento;

        if (botao) {
            botao.style.display = 'inline-flex';
            botao.disabled = false;
        }
    });

    if (botao) {
        botao.addEventListener('click', async () => {
            if (!eventoInstalacao) {
                mostrarStatus('Use o menu do navegador para instalar o Jornada.');
                return;
            }

            botao.disabled = true;
            eventoInstalacao.prompt();

            try {
                const escolha = await eventoInstalacao.userChoice;

                if (escolha.outcome === 'accepted') {
                    mostrarStatus('Instalação iniciada. O Jornada vai aparecer junto com seus aplicativos.');
                } else {
                    mostrarStatus('Instalação cancelada. Você pode instalar depois pelo menu do navegador.');
                }
            } catch (erro) {
                mostrarStatus('Não foi possível abrir a instalação. Use o menu do navegador.');
            } finally {
                eventoInstalacao = null;
                botao.style.display = 'none';
                botao.disabled = false;
            }
        });
    }

    window.addEventListener('appinstalled', () => {
        if (botao) {
            botao.style.display = 'none';
        }

        mostrarStatus('Jornada instalado com sucesso.');
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/', updateViaCache: 'none' }).catch((erro) => {
                console.warn('Service worker do Jornada não registrado:', erro);
            });
        }, { once: true });
    }
})();
