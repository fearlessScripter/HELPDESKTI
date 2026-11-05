<?php
/**
 * Envío único de formularios (todas las páginas).
 *
 * Al enviar un formulario POST, sus botones ahora quedan deshabilitados con "Enviando..." para que un
 * doble clic o varios Enter no manden la misma solicitud varias veces.
 * Es la primera barrera (mejora la experiencia); la garantía está en el servidor
 * (ej. token_envio en Tickets::crear), porque el navegador puede saltarse este script.
 */
?>
<script>
(function () {
    function botonesDeEnvio(form) {
        return Array.prototype.filter.call(form.elements, function (el) {
            return (el.tagName === 'BUTTON' && (el.type || 'submit') === 'submit')
                || (el.tagName === 'INPUT' && el.type === 'submit');
        });
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        // Si otro script canceló el envío (ej. validación propia), no se toca nada
        if (e.defaultPrevented || (form.method || '').toLowerCase() !== 'post') {
            return;
        }

        // Ya se está enviando: se descartan los clics siguientes
        if (form.dataset.enviando === '1') {
            e.preventDefault();
            return;
        }
        form.dataset.enviando = '1';

        // Se deshabilitan un instante después, cuando el navegador ya armó los datos del envío
        // (deshabilitarlos antes haría perder el name/value del botón presionado)
        setTimeout(function () {
            botonesDeEnvio(form).forEach(function (btn) {
                btn.disabled = true;
                if (btn.tagName === 'BUTTON' && btn.dataset.textoOriginal === undefined) {
                    btn.dataset.textoOriginal = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Enviando...';
                }
            });
        }, 0);
    });

    // Al volver con "Atrás", el navegador puede mostrar la página guardada con los botones bloqueados
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) {
            return;
        }
        document.querySelectorAll('form[data-enviando="1"]').forEach(function (form) {
            delete form.dataset.enviando;
            botonesDeEnvio(form).forEach(function (btn) {
                btn.disabled = false;
                if (btn.dataset.textoOriginal !== undefined) {
                    btn.innerHTML = btn.dataset.textoOriginal;
                    delete btn.dataset.textoOriginal;
                }
            });
        });
    });
})();
</script>
