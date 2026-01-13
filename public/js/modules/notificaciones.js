/**
 * Sistema de notificaciones (licencias y préstamos por vencer)
 */

function cargarNotificaciones() {
    const licenciasReq = $.get(APP.url('licencias/proximas-vencer'));
    const prestamosReq = $.get(APP.url('movimientos/prestamos-por-vencer'));

    $.when(licenciasReq, prestamosReq).done(function (licenciasResp, prestamosResp) {
        const licencias = licenciasResp[0] || { cantidad: 0, licencias: [] };
        const prestamos = prestamosResp[0] || { cantidad: 0, prestamos: [] };

        const total = (licencias.cantidad || 0) + (prestamos.cantidad || 0);

        if (total > 0) {
            $('#contadorNotificaciones').text(total).show();
        } else {
            $('#contadorNotificaciones').hide();
        }

        let html = "";

        if (total === 0) {
            html = "<small>No hay alertas</small>";
        } else {
            if ((licencias.cantidad || 0) > 0) {
                licencias.licencias.forEach(item => {
                    html += `<div class="alert alert-warning p-2 mb-2"><b>${item.nombre_software}</b><br>Expira: ${item.fecha_expiracion}</div>`;
                });
            }
            if ((prestamos.cantidad || 0) > 0) {
                prestamos.prestamos.forEach(p => {
                    html += `<div class="alert alert-info p-2 mb-2"><b>Préstamo - Lote ${p.lote}</b><br>Usuario: ${p.usuario}<br>Vence: ${p.fecha_limite}</div>`;
                });
            }
            html += `<div class="dropdown-divider"></div><a class="dropdown-item text-center" href="${APP.url('movimientos')}">Ver movimientos</a>`;
        }

        $("#listaNotificaciones").html(html);
    }).fail(function () {
        $("#listaNotificaciones").html("<small>No se pudieron cargar las notificaciones</small>");
        $('#contadorNotificaciones').hide();
    });
}

// Ejecutar al iniciar y periódicamente
$(function () {
    if ($('#contadorNotificaciones').length) {
        cargarNotificaciones();
        setInterval(cargarNotificaciones, 60000); // Cada minuto
    }
});
