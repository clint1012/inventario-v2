$(document).ready(function() {

    // Inicializa DataTable
    const table = $('#tablaProveedores').DataTable({
        ajax: base_url + 'proveedor', // ruta singular RESTful
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'ruc' },
            { data: 'telefono' },
            { data: 'correo' },
            { data: 'direccion' },
            { data: 'estado' },
            { data: 'giro' },
            { 
                data: null,
                render: function(data) {
                    return `
                        <button class="btn btn-info btn-sm btn-pdf" data-id="${data.id}">PDF</button>
                        <button class="btn btn-warning btn-sm btn-editar" data-id="${data.id}">Editar</button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${data.id}">Eliminar</button>`;
                }
            }
        ]
    });

    // Abrir modal para nuevo proveedor
    $('#btnNuevo').click(function() {
        $('#formProveedor')[0].reset();
        $('#id').val('');
        $('#modalProveedor').modal('show');
    });

    // Crear o actualizar proveedor
    $('#formProveedor').submit(function(e) {
        e.preventDefault();

        let id = $('#id').val();
        let formData = new FormData(this);
        let url = id ? base_url + 'proveedor/' + id : base_url + 'proveedor';
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modalProveedor').modal('hide');
                table.ajax.reload();
                alert(response.message); // muestra "Proveedor creado" o "Proveedor actualizado"
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al guardar proveedor');
            }
        });
    });

    // Editar proveedor
    $('#tablaProveedores').on('click', '.btn-editar', function() {
        let id = $(this).data('id');
        $.get(base_url + 'proveedor/' + id, function(data) {
            $('#id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#ruc').val(data.ruc);
            $('#telefono').val(data.telefono);
            $('#correo').val(data.correo);
            $('#direccion').val(data.direccion);
            $('#giro').val(data.giro);
            $('#estado').val(data.estado);
            $('#modalProveedor').modal('show');
        });
    });

    // Eliminar proveedor
    $('#tablaProveedores').on('click', '.btn-eliminar', function() {
        if (!confirm('¿Desea eliminar este proveedor?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: base_url + 'proveedor/' + id,
            type: 'DELETE',
            success: function(response) {
                table.ajax.reload();
                alert(response.message);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error al eliminar proveedor');
            }
        });
    });

    // Abrir PDF
    $('#tablaProveedores').on('click', '.btn-pdf', function() {
        let id = $(this).data('id');
        window.open(base_url + 'proveedor/pdf/' + id, '_blank');
    });

});
