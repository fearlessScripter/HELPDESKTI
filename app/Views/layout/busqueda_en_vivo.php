<?php
/**
 * Búsqueda en tiempo real para los listados.
 *
 * Uso: un <input data-busqueda-en-vivo> y uno o más contenedores con data-region="nombre".
 * Al escribir, se pide la misma página al servidor con ?q=... (respetando los filtros ya
 * aplicados) y se reemplazan solo esas regiones. La búsqueda sigue siendo del lado del
 * servidor, por lo que funciona igual con miles de registros.
 */
?>
<style>
    [data-region].buscando { opacity: .55; transition: opacity .15s; pointer-events: none; }
    mark.resaltado { background-color: #fff3a3; padding: 0 1px; border-radius: 2px; }
</style>
<script>
(function () {
    var input = document.querySelector('[data-busqueda-en-vivo]');
    if (!input) return;

    var ESPERA_MS = 350;   // espera tras la última tecla antes de buscar
    var MIN_CARACTERES = 2; // con 1 letra no se busca (casi todo coincidiría)
    var icono = document.querySelector('[data-busqueda-icono]');
    var temporizador = null;
    var peticion = null;
    var ultimoTexto = normalizarEspacios(input.value);

    function normalizarEspacios(texto) {
        return texto.trim().replace(/\s+/g, ' ');
    }

    function cargando(activo) {
        document.querySelectorAll('[data-region]').forEach(function (r) { r.classList.toggle('buscando', activo); });
        if (icono) icono.className = activo ? 'spinner-border spinner-border-sm text-primary' : 'bi bi-search';
        input.setAttribute('aria-busy', activo ? 'true' : 'false');
    }

    function buscar() {
        var texto = normalizarEspacios(input.value);
        if (texto === ultimoTexto) return;
        if (texto.length > 0 && texto.length < MIN_CARACTERES) return;
        ultimoTexto = texto;

        var url = new URL(window.location.href);
        if (texto) { url.searchParams.set('q', texto); } else { url.searchParams.delete('q'); }
        url.searchParams.delete('page'); // una búsqueda nueva siempre parte en la página 1

        if (peticion) peticion.abort(); // descarta la búsqueda anterior si aún no respondía
        peticion = new AbortController();
        cargando(true);

        fetch(url.toString(), {
            signal: peticion.signal,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (resp) {
                // Sesión vencida u otro redirect: se navega normalmente
                if (resp.redirected || !resp.ok) { window.location.href = url.toString(); return null; }
                return resp.text();
            })
            .then(function (html) {
                if (html === null) return;
                var doc = new DOMParser().parseFromString(html, 'text/html');
                document.querySelectorAll('[data-region]').forEach(function (actual) {
                    var nuevo = doc.querySelector('[data-region="' + actual.dataset.region + '"]');
                    if (nuevo) actual.replaceWith(document.importNode(nuevo, true));
                });
                history.replaceState(null, '', url.toString());
                resaltar(texto);
            })
            .catch(function (err) {
                if (err.name !== 'AbortError') { window.location.href = url.toString(); }
            })
            .finally(function () { cargando(false); });
    }

    // Resalta las palabras buscadas en los resultados (sin distinguir tildes ni mayúsculas)
    function resaltar(texto) {
        var palabras = normalizarEspacios(texto).split(' ')
            .map(function (p) { return p.replace(/^#/, ''); })
            .filter(function (p) { return p.length >= MIN_CARACTERES; });
        if (!palabras.length) return;

        var variantes = { a: 'aáàäâ', e: 'eéèëê', i: 'iíìïî', o: 'oóòöô', u: 'uúùüû', n: 'nñ' };
        var patron = palabras.map(function (p) {
            return p.normalize('NFD').replace(/[̀-ͯ]/g, '').split('').map(function (c) {
                var minus = c.toLowerCase();
                if (variantes[minus]) return '[' + variantes[minus] + ']';
                return c.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }).join('');
        }).join('|');
        var regex = new RegExp('(' + patron + ')', 'gi');

        document.querySelectorAll('[data-region][data-resaltable], [data-region] [data-resaltable]').forEach(function (zona) {
            var walker = document.createTreeWalker(zona, NodeFilter.SHOW_TEXT, {
                acceptNode: function (nodo) {
                    if (!nodo.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                    if (nodo.parentElement.closest('.modal, .dropdown-menu, .btn, .badge, script, style, mark')) return NodeFilter.FILTER_REJECT;
                    return NodeFilter.FILTER_ACCEPT;
                }
            });
            var nodos = [];
            while (walker.nextNode()) nodos.push(walker.currentNode);
            nodos.forEach(function (nodo) {
                regex.lastIndex = 0;
                if (!regex.test(nodo.nodeValue)) return;
                // Se envuelve en UN <span> inline: si el padre es flex (ej. .accordion-button),
                // varios nodos sueltos serían ítems flex separados y se perderían los espacios.
                var fragmento = document.createElement('span');
                nodo.nodeValue.split(regex).forEach(function (parte, i) {
                    if (!parte) return;
                    if (i % 2 === 1) {
                        var mark = document.createElement('mark');
                        mark.className = 'resaltado';
                        mark.textContent = parte;
                        fragmento.appendChild(mark);
                    } else {
                        fragmento.appendChild(document.createTextNode(parte));
                    }
                });
                nodo.parentNode.replaceChild(fragmento, nodo);
            });
        });
    }

    input.addEventListener('input', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(buscar, ESPERA_MS);
    });

    // Enter busca al instante en vez de enviar el formulario
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(temporizador);
            buscar();
        }
    });

    // Resaltado inicial (p. ej. al recargar con ?q=...), cuando la tabla ya existe
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { resaltar(input.value); });
    } else {
        resaltar(input.value);
    }
})();
</script>
