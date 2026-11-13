'use strict';

const btnAgora = document.getElementById('btn-agora');
const inputHora = document.getElementById('input-hora');

if (btnAgora && inputHora) {
    btnAgora.addEventListener('click', () => {
        const agora = new Date();
        const h = String(agora.getHours()).padStart(2, '0');
        const m = String(agora.getMinutes()).padStart(2, '0');
        inputHora.value = `${h}:${m}`;
    });
}

/* Widget de Calendário do Sistema (presente em /, /jornada, /registrar e /relatorios) */
const widgetCalendario = document.getElementById('widget-calendario');
if (widgetCalendario) {
    const btnToggle = document.getElementById('btn-calendario');
    const painel = document.getElementById('painel-calendario');
    const seta = document.getElementById('icone-calendario-seta');
    const tituloMes = document.getElementById('cal-titulo');
    const gradeMeses = document.getElementById('cal-grade-meses');
    const grade = document.getElementById('cal-grade');
    const dica = document.getElementById('cal-dica');
    const btnMesAnt = document.getElementById('cal-mes-anterior');
    const btnProxMes = document.getElementById('cal-proximo-mes');
    const btnIrHoje = document.getElementById('cal-ir-hoje');

    const escopo = widgetCalendario.dataset.escopo || 'dia';
    const rotaBase = widgetCalendario.dataset.rotaBase || '/registrar';
    const dataSelecionada = widgetCalendario.dataset.dataSelecionada || '';
    const mesSelecionado = widgetCalendario.dataset.mesSelecionado || dataSelecionada.slice(0, 7);
    const hojeIso = widgetCalendario.dataset.hoje || '';
    const modo = widgetCalendario.dataset.modo || 'individual';

    let mapaRegistros = {};
    try {
        mapaRegistros = JSON.parse(widgetCalendario.dataset.registros || '{}');
    } catch {
        mapaRegistros = {};
    }

    const nomesMeses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];
    const nomesMesesCurtos = [
        'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
        'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez',
    ];

    const classesPontoStatus = {
        concluido: 'size-1.5 rounded-full bg-emerald-400',
        andamento: 'size-1.5 rounded-full bg-amber-400',
        ocorrencia: 'size-1.5 rounded-full bg-sky-400',
        falta: 'size-1.5 rounded-full bg-red-400',
    };

    const refInicial = (escopo === 'dia' && dataSelecionada) ? dataSelecionada : `${mesSelecionado}-01`;
    const partesIniciais = refInicial.split('-').map(Number);
    let anoVisivel = partesIniciais[0] || new Date().getFullYear();
    let mesVisivel = (partesIniciais[1] || (new Date().getMonth() + 1)) - 1;

    if (btnIrHoje) {
        btnIrHoje.textContent = escopo === 'mes' ? 'Mês atual' : 'Ir para hoje';
    }
    if (dica) {
        dica.textContent = escopo === 'mes'
            ? 'Selecione um mês acima para filtrar ou toque em um dia para abrir o registro'
            : 'Toque em qualquer dia para carregar as batidas';
    }

    const irParaData = (iso) => {
        window.location.href = `/registrar?modo=${encodeURIComponent(modo)}&data=${encodeURIComponent(iso)}`;
    };

    const irParaMes = (anoMes) => {
        window.location.href = `${rotaBase}?mes=${encodeURIComponent(anoMes)}`;
    };

    const renderizarCalendario = () => {
        if (!grade || !tituloMes) return;
        tituloMes.textContent = `${nomesMeses[mesVisivel]} de ${anoVisivel}`;

        if (gradeMeses) {
            gradeMeses.innerHTML = '';
            nomesMesesCurtos.forEach((rotuloCurto, idx) => {
                const mStr = String(idx + 1).padStart(2, '0');
                const anoMesCand = `${anoVisivel}-${mStr}`;
                const ativo = idx === mesVisivel;
                const ehMesSelecionado = anoMesCand === mesSelecionado;

                const btnMes = document.createElement('button');
                btnMes.type = 'button';

                const baseMes = 'cursor-pointer rounded-lg py-1.5 font-mono text-xs transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500';
                let varMes = 'text-zinc-400 hover:bg-zinc-800 hover:text-white';
                if (ativo) {
                    varMes = 'bg-teal-600 font-bold text-white shadow-2xs';
                } else if (ehMesSelecionado) {
                    varMes = 'border border-teal-500/50 bg-zinc-900 font-semibold text-teal-300';
                }

                btnMes.className = `${baseMes} ${varMes}`;
                btnMes.textContent = rotuloCurto;
                btnMes.addEventListener('click', () => {
                    if (escopo === 'mes') {
                        irParaMes(anoMesCand);
                    } else {
                        mesVisivel = idx;
                        renderizarCalendario();
                    }
                });
                gradeMeses.appendChild(btnMes);
            });
        }

        grade.innerHTML = '';

        const primeiroDiaSemana = new Date(anoVisivel, mesVisivel, 1).getDay();
        const totalDiasMes = new Date(anoVisivel, mesVisivel + 1, 0).getDate();

        for (let i = 0; i < primeiroDiaSemana; i++) {
            const vazio = document.createElement('span');
            vazio.className = 'h-10';
            grade.appendChild(vazio);
        }

        const mesStr = String(mesVisivel + 1).padStart(2, '0');

        for (let dia = 1; dia <= totalDiasMes; dia++) {
            const diaStr = String(dia).padStart(2, '0');
            const iso = `${anoVisivel}-${mesStr}-${diaStr}`;
            const diaSemana = new Date(anoVisivel, mesVisivel, dia).getDay();
            const fimDeSemana = diaSemana === 0 || diaSemana === 6;
            const selecionado = escopo === 'dia' && iso === dataSelecionada;
            const ehHoje = iso === hojeIso;
            const status = mapaRegistros[iso] || null;

            const btnDia = document.createElement('button');
            btnDia.type = 'button';

            const baseBtn = 'flex h-10 cursor-pointer flex-col items-center justify-center gap-0.5 rounded-xl font-mono text-xs tabular-nums transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500';
            let varianteBtn = 'text-zinc-200 hover:bg-zinc-800 hover:text-white';

            if (selecionado) {
                varianteBtn = 'bg-teal-600 font-bold text-white ring-2 ring-teal-400/50';
            } else if (ehHoje) {
                varianteBtn = 'border border-teal-500/60 bg-zinc-800/90 font-bold text-teal-300 hover:bg-zinc-800';
            } else if (fimDeSemana) {
                varianteBtn = 'bg-zinc-900/50 text-zinc-500 hover:bg-zinc-800 hover:text-zinc-200';
            }

            btnDia.className = `${baseBtn} ${varianteBtn}`;
            btnDia.setAttribute('aria-label', `${diaStr}/${mesStr}/${anoVisivel}`);
            if (selecionado) {
                btnDia.setAttribute('aria-current', 'date');
            }

            const numEl = document.createElement('span');
            numEl.textContent = String(dia);
            btnDia.appendChild(numEl);

            const dotEl = document.createElement('span');
            if (status && classesPontoStatus[status]) {
                dotEl.className = selecionado ? 'size-1.5 rounded-full bg-white' : classesPontoStatus[status];
            } else {
                dotEl.className = 'size-1.5 rounded-full bg-transparent';
            }
            btnDia.appendChild(dotEl);

            btnDia.addEventListener('click', () => irParaData(iso));
            grade.appendChild(btnDia);
        }
    };

    const alternarPainel = (forcarEstado) => {
        if (!painel || !btnToggle) return;
        const abrir = typeof forcarEstado === 'boolean' ? forcarEstado : painel.classList.contains('hidden');
        painel.classList.toggle('hidden', !abrir);
        btnToggle.setAttribute('aria-expanded', abrir ? 'true' : 'false');
        if (seta) {
            seta.classList.toggle('rotate-180', abrir);
        }
        if (abrir) {
            renderizarCalendario();
        }
    };

    if (btnToggle) {
        btnToggle.addEventListener('click', () => alternarPainel());
    }

    if (btnMesAnt) {
        btnMesAnt.addEventListener('click', () => {
            mesVisivel--;
            if (mesVisivel < 0) {
                mesVisivel = 11;
                anoVisivel--;
            }
            renderizarCalendario();
        });
    }

    if (btnProxMes) {
        btnProxMes.addEventListener('click', () => {
            mesVisivel++;
            if (mesVisivel > 11) {
                mesVisivel = 0;
                anoVisivel++;
            }
            renderizarCalendario();
        });
    }

    if (btnIrHoje && hojeIso) {
        btnIrHoje.addEventListener('click', () => {
            if (escopo === 'mes') {
                irParaMes(hojeIso.slice(0, 7));
            } else {
                irParaData(hojeIso);
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && painel && !painel.classList.contains('hidden')) {
            alternarPainel(false);
        }
    });

    if (new URLSearchParams(window.location.search).get('calendario') === '1') {
        alternarPainel(true);
    }
}

/* Padronização nativa da foto do comprovante (Canvas -> WebP máx 1200px, qualidade 0.78) */
const decodificarImagem = async (arquivo) => {
    try {
        return await createImageBitmap(arquivo);
    } catch {
        // Fallback via <img>: navegadores como o Safari decodificam HEIC pelo sistema
        // mesmo quando createImageBitmap não aceita o formato.
        return await new Promise((resolve, reject) => {
            const url = URL.createObjectURL(arquivo);
            const imagem = new Image();

            imagem.onload = () => {
                URL.revokeObjectURL(url);
                resolve(imagem);
            };
            imagem.onerror = () => {
                URL.revokeObjectURL(url);
                reject(new Error('Formato de imagem não suportado pelo navegador.'));
            };
            imagem.src = url;
        });
    }
};

const comprimirImagem = async (arquivo) => {
    const bitmap = await decodificarImagem(arquivo);
    const maxLado = 1200;
    let width = bitmap.naturalWidth || bitmap.width;
    let height = bitmap.naturalHeight || bitmap.height;

    if (width > maxLado || height > maxLado) {
        if (width >= height) {
            height = Math.round((height * maxLado) / width);
            width = maxLado;
        } else {
            width = Math.round((width * maxLado) / height);
            height = maxLado;
        }
    }

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(bitmap, 0, 0, width, height);
    if (typeof bitmap.close === 'function') {
        bitmap.close();
    }

    let blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', 0.78));
    let ext = 'webp';

    if (!blob || blob.type !== 'image/webp') {
        blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.8));
        ext = 'jpg';
    }

    const nomeBase = arquivo.name.replace(/\.[^.]+$/, '') || 'ticket';
    return new File([blob], `${nomeBase}.${ext}`, {
        type: blob.type,
        lastModified: Date.now(),
    });
};

const ehArquivoHeic = (arquivo) => /\.(heic|heif)$/i.test(arquivo.name || '')
    || /^image\/hei[cf]$/i.test(arquivo.type || '');

document.querySelectorAll('input[type="file"][data-comprimir]').forEach((input) => {
    input.addEventListener('change', async () => {
        const arquivo = input.files && input.files[0];
        if (!arquivo) return;

        const statusEl = input.parentElement.querySelector('[data-status-foto]');
        const form = input.closest('form');
        const btnSubmit = form ? form.querySelector('button[type="submit"]') : null;

        const definirStatus = (mensagem, erro = false) => {
            if (!statusEl) return;
            statusEl.textContent = mensagem;
            statusEl.classList.toggle('text-red-600', erro);
            statusEl.classList.toggle('text-zinc-500', !erro);
        };

        try {
            definirStatus('Otimizando imagem...');
            if (btnSubmit) btnSubmit.disabled = true;

            const otimizado = await comprimirImagem(arquivo);
            const dt = new DataTransfer();
            dt.items.add(otimizado);
            input.files = dt.files;

            const kb = Math.max(1, Math.round(otimizado.size / 1024));
            definirStatus(`✓ Imagem padronizada (${kb} KB · ${otimizado.type.replace('image/', '').toUpperCase()})`);
        } catch {
            // Enviar o arquivo original só produziria erro no servidor (HEIC não é
            // decodificado pelo PHP): limpa a seleção e orienta o usuário.
            input.value = '';
            definirStatus(
                ehArquivoHeic(arquivo)
                    ? 'Esta foto é HEIC e não pôde ser convertida neste navegador. Tire a foto pela câmera do sistema ou exporte como JPG antes de anexar.'
                    : 'Não foi possível processar esta imagem. Use uma foto JPG, PNG ou WebP.',
                true
            );
        } finally {
            if (btnSubmit) btnSubmit.disabled = false;
        }
    });
});

/* Modal nativo <dialog> para visualizar comprovante */
const modal = document.getElementById('modal-comprovante');
const modalImg = document.getElementById('modal-imagem');
const modalTitulo = document.getElementById('modal-titulo');
const modalDownload = document.getElementById('modal-download');
const modalFechar = document.getElementById('modal-fechar');

if (modal && modalImg) {
    document.addEventListener('click', (e) => {
        const gatilho = e.target.closest('[data-comprovante-url]');
        if (!gatilho) return;

        e.preventDefault();
        const url = gatilho.getAttribute('data-comprovante-url');
        const titulo = gatilho.getAttribute('data-comprovante-titulo') || 'Comprovante de ponto';

        modalTitulo.textContent = titulo;
        modalImg.removeAttribute('src');
        modalImg.src = url;
        if (modalDownload) {
            modalDownload.href = `${url}&download=1`;
        }
        modal.showModal();
    });

    modal.addEventListener('close', () => {
        modalImg.removeAttribute('src');
    });

    if (modalFechar) {
        modalFechar.addEventListener('click', () => modal.close());
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.close();
    });
}

/* Simulador de confronto em /relatorios */
const painelConfronto = document.getElementById('painel-confronto');
if (painelConfronto) {
    const lerMinutos = (texto) => {
        const limpo = texto.trim();
        const m = limpo.match(/^([+-])?\s*(\d{1,4})(?::(\d{1,2}))?$/);
        if (!m) return null;
        const sinal = m[1] === '-' ? -1 : 1;
        const horas = parseInt(m[2], 10);
        const mins = m[3] ? parseInt(m[3], 10) : 0;
        if (mins >= 60) return null;
        return sinal * ((horas * 60) + mins);
    };

    const formatarMin = (min) => {
        const abs = Math.abs(min);
        const h = String(Math.floor(abs / 60)).padStart(2, '0');
        const m = String(abs % 60).padStart(2, '0');
        return `${h}h ${m}min`;
    };

    const classeBaseResultado = 'mt-2 block min-h-5 font-mono text-xs font-semibold tabular-nums';

    const atualizarComparacao = (inputEl, saidaEl, valorMeu) => {
        const valorEmpresa = lerMinutos(inputEl.value);
        if (inputEl.value.trim() === '') {
            saidaEl.textContent = '';
            saidaEl.className = `${classeBaseResultado} text-zinc-500`;
            return;
        }
        if (valorEmpresa === null) {
            saidaEl.textContent = 'Formato inválido (use HH:MM, ex.: 160:00 ou -01:15)';
            saidaEl.className = `${classeBaseResultado} text-red-700`;
            return;
        }

        const dif = valorEmpresa - valorMeu;
        if (dif === 0) {
            saidaEl.textContent = '✓ Confere exatamente com o seu controle';
            saidaEl.className = `${classeBaseResultado} text-emerald-700`;
        } else if (dif < 0) {
            saidaEl.textContent = `Divergência: empresa contou ${formatarMin(dif)} a menos que o seu registro`;
            saidaEl.className = `${classeBaseResultado} text-red-700`;
        } else {
            saidaEl.textContent = `Divergência: empresa contou ${formatarMin(dif)} a mais que o seu registro`;
            saidaEl.className = `${classeBaseResultado} text-emerald-700`;
        }
    };

    const meuTrabalhado = parseInt(painelConfronto.dataset.trabalhado || '0', 10);
    const meuSaldo = parseInt(painelConfronto.dataset.saldo || '0', 10);

    const inTrab = document.getElementById('empresa-trabalhado');
    const resTrab = document.getElementById('res-trabalhado');
    const inSaldo = document.getElementById('empresa-saldo');
    const resSaldo = document.getElementById('res-saldo');

    if (inTrab && resTrab) {
        inTrab.addEventListener('input', () => atualizarComparacao(inTrab, resTrab, meuTrabalhado));
    }
    if (inSaldo && resSaldo) {
        inSaldo.addEventListener('input', () => atualizarComparacao(inSaldo, resSaldo, meuSaldo));
    }
}
