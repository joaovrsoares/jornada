/* Service worker do Jornada (PWA). */
const VERSAO = 'jornada-pwa-v1';
const CACHE_ESTATICO = `${VERSAO}-estatico`;
const PAGINA_OFFLINE = '/offline.html';

const PRECACHE = [
    PAGINA_OFFLINE,
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/assets/js/pwa.js',
    '/assets/brand/logo-jornada-mark.svg',
    '/assets/brand/app-icon-192.png',
    '/assets/brand/app-icon-512.png',
    '/assets/brand/favicon.ico',
    '/assets/brand/favicon.svg',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_ESTATICO)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((chaves) => Promise.all(
                chaves
                    .filter((chave) => chave !== CACHE_ESTATICO)
                    .map((chave) => caches.delete(chave))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const requisicao = event.request;

    if (requisicao.method !== 'GET') {
        return;
    }

    const url = new URL(requisicao.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    // Navegação: tenta a rede e, sem conexão, entrega a página offline.
    if (requisicao.mode === 'navigate') {
        event.respondWith(
            fetch(requisicao).catch(async () => {
                const offline = await caches.match(PAGINA_OFFLINE);

                return offline || new Response(
                    'Você está offline. Verifique a conexão e tente novamente.',
                    {
                        status: 503,
                        headers: { 'Content-Type': 'text/plain; charset=utf-8' },
                    }
                );
            })
        );
        return;
    }

    // Arquivos estáticos: cache primeiro, atualizando em segundo plano.
    if (
        url.pathname.startsWith('/assets/')
        || url.pathname === PAGINA_OFFLINE
        || url.pathname === '/favicon.ico'
    ) {
        event.respondWith(cachePrimeiro(requisicao));
    }
});

async function cachePrimeiro(requisicao) {
    const cache = await caches.open(CACHE_ESTATICO);
    const emCache = await cache.match(requisicao);

    const atualizacao = fetch(requisicao)
        .then((resposta) => {
            if (resposta && resposta.ok) {
                cache.put(requisicao, resposta.clone());
            }
            return resposta;
        })
        .catch(() => null);

    if (emCache) {
        atualizacao.catch(() => {});
        return emCache;
    }

    return (await atualizacao) || Response.error();
}
