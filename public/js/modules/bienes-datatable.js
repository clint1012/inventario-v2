/**
 * DataTable de Bienes con filtros avanzados
 */

var table; // variable global accesible desde otros scripts

$(document).ready(function () {
    // Solo inicializar si existe la tabla
    if (!$('#bienesTable').length) return;

    // ==============================
    // Inicializar DataTable
    // ==============================
    table = $('#bienesTable').DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        order: [[0, "asc"]],
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        columnDefs: [
            { targets: [5, 9], visible: false, searchable: true }
            // 🔹 5 = "Serie", 9 = "Fecha de compra"
        ],

        initComplete: function () {
            var api = this.api();

            // ==============================
            // Llenar selects dinámicos (marca, modelo, local, departamento)
            // ==============================
            api.columns([3, 4, 5, 6]).every(function () {
                var column = this;
                var select = $('select', column.header());
                var dataSet = [];

                column.data().each(function (d) {
                    if (d && !dataSet.includes(d)) dataSet.push(d);
                });

                dataSet.sort();
                dataSet.forEach(function (d) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });

                // 🔹 Evento al cambiar un filtro de columna
                select.on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                    actualizarFiltrosDependientes();
                });
            });
        }
    });

    // ==============================
    //  Filtros por columna (segunda fila del thead)
    // ==============================
    $('#bienesTable thead tr:eq(1) th').each(function (i) {
        var input = $('input, select', this);
        if (input.length) {
            $(input).on('keyup change', function () {
                var val = this.value;
                table.column(i).search(val).draw();
                actualizarFiltrosDependientes();
            });
        }
    });

    // ==============================
    // Filtro por rango de fechas (externo)
    // ==============================
    $.fn.dataTable.ext.search.push(function (settings, data) {
        var fechaDesde = $('#filterFechaDesde').val();
        var fechaHasta = $('#filterFechaHasta').val();
        var fechaCompra = data[8] || ''; // Columna 8 = Fecha de compra

        if (fechaCompra === '') return true;
        var fecha = new Date(fechaCompra);
        var desde = fechaDesde ? new Date(fechaDesde) : null;
        var hasta = fechaHasta ? new Date(fechaHasta) : null;

        return (desde === null || fecha >= desde) && (hasta === null || fecha <= hasta);
    });

    $('#filterFechaDesde, #filterFechaHasta').on('change', function () {
        table.draw();
    });

    // ==============================
    // Filtros externos adicionales
    // ==============================
    $('#filterDescripcion').on('keyup', function () {
        table.column(2).search(this.value).draw();
        actualizarFiltrosDependientes();
    });

    $('#filterEstado').on('change', function () {
        table.column(7).search(this.value).draw();
        actualizarFiltrosDependientes();
    });

    // ==============================
    // Limpiar filtros
    // ==============================
    $(document).on('click', '#clearFilters', function () {
        console.log('🧹 Limpiando filtros...');

        $('#filterFechaDesde, #filterFechaHasta, #filterDescripcion').val('');
        $('#filterEstado').val('');

        $('#bienesTable thead tr:eq(1) th').each(function () {
            var $el = $('input, select', this);
            if ($el.length) {
                if ($el.is('select')) {
                    $el.prop('selectedIndex', 0).trigger('change');
                } else {
                    $el.val('').trigger('keyup').trigger('change');
                }
            }
        });

        if (typeof table !== 'undefined' && table) {
            table.search('').columns().search('').draw();
        }

        setTimeout(actualizarFiltrosDependientes, 300);
    });

    // ==============================
    // Mostrar / Ocultar filtros externos
    // ==============================
    $('#toggleFilters').on('click', function () {
        $('#filterContainer').toggle();
        $(this).text($('#filterContainer').is(':visible') ? 'Ocultar Filtros' : 'Mostrar Filtros');
    });

    // ==============================
    // Autocompletado descripcion
    // ==============================
    $('#bienesTable thead tr:eq(1) th:eq(2) input').autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: APP.url('bienes/buscarDescripcion'),
                dataType: "json",
                data: { term: request.term },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            table.column(2).search(ui.item.value).draw();
            actualizarFiltrosDependientes();
        }
    });

    // ==============================
    // Cargar lista de filtros desde backend
    // ==============================
    cargarFiltrosDinamicos();

    // 🔁 Actualiza dependientes tras cada renderizado del DataTable
    table.on('draw', function () {
        actualizarFiltrosDependientes();
    });
});

// =========================================================
// Cargar filtros dinámicos por AJAX
// =========================================================
function cargarFiltrosDinamicos() {
    $.getJSON(APP.url('bienes/marcas'), function (data) {
        let select = $('#bienesTable thead tr:eq(1) th:eq(3) select');
        select.empty().append('<option value="">Todos</option>');
        data.forEach(item => {
            if (item.marca) select.append(`<option value="${item.marca}">${item.marca}</option>`);
        });
    });

    $.getJSON(APP.url('bienes/modelos'), function (data) {
        let select = $('#bienesTable thead tr:eq(1) th:eq(4) select');
        select.empty().append('<option value="">Todos</option>');
        data.forEach(item => {
            if (item.modelo) select.append(`<option value="${item.modelo}">${item.modelo}</option>`);
        });
    });

    $.getJSON(APP.url('bienes/locales'), function (data) {
        let select = $('#bienesTable thead tr:eq(1) th:eq(5) select');
        select.empty().append('<option value="">Todos</option>');
        data.forEach(local => {
            select.append(`<option value="${local.nombre}">${local.nombre}</option>`);
        });
    });

    $.getJSON(APP.url('bienes/departamentos'), function (data) {
        let select = $('#bienesTable thead tr:eq(1) th:eq(6) select');
        select.empty().append('<option value="">Todos</option>');
        data.forEach(dep => {
            select.append(`<option value="${dep.nombre}">${dep.nombre}</option>`);
        });
    });
}

// ==================================================
// 🔁 Filtros dependientes (discriminantes)
// ==================================================
function actualizarFiltrosDependientes() {
    if (typeof table === 'undefined' || !table.columns) return;

    const selectsCols = [3, 4, 6, 7]; // Marca, Modelo, Local, Departamento

    selectsCols.forEach(function (colIdx) {
        const column = table.column(colIdx);
        const visibleIndex = column.index('visible');
        let select = $('#bienesTable thead tr:eq(1) th:visible').eq(visibleIndex).find('select');
        if (!select.length) {
            select = $('#bienesTable thead tr:eq(1) th').eq(colIdx).find('select');
        }
        if (!select.length) return;

        const valActual = select.val();
        const colData = column.data({ search: 'applied' }).toArray();
        const valores = Array.from(new Set(
            colData.map(v => (v ? v.trim() : '')).filter(v => v !== '')
        )).sort();

        select.empty().append('<option value="">Todos</option>');
        valores.forEach(v => select.append(`<option value="${v}">${v}</option>`));

        if (valActual && select.find(`option[value="${valActual}"]`).length) {
            select.val(valActual);
        } else {
            select.val('');
        }
    });
}
