$(document).ready(function () {

  /* ============================================================
     1. Convertir en mayúsculas mientras el usuario escribe
     ============================================================ */
  $("#modalProveedor input[type='text'], #modalProveedor input[type='email'], #modalProveedor textarea")
    .on("input", function () {
      this.value = this.value.toUpperCase();
    });

  /* ============================================================
     2. Inicialización de DataTables
     ============================================================ */
  const table = $("#tablaProveedores").DataTable({
    ajax: base_url + "proveedor",
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json",
    },
    order: [[0, "asc"]],
    responsive: true,
    fixedHeader: true,
    columns: [
      { data: "id" },
      { data: "nombre" },
      { data: "rep_legal" },
      { data: "ruc" },
      { data: "telefono" },
      { data: "tel_fijo" },
      { data: "correo" },
      { data: "direccion" },
      { data: "estado" },
      { data: "giro" },
      {
        data: null,
        render: function (data) {
          return `
            <button class="btn btn-info btn-sm btn-pdf" data-id="${data.id}">PDF</button>
            <button class="btn btn-warning btn-sm btn-editar" data-id="${data.id}">Editar</button>
            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${data.id}">Eliminar</button>`;
        },
      },
    ],
  });

  /* ============================================================
     3. Botón NUEVO
     ============================================================ */
  $("#btnNuevo").click(function () {
    $("#formProveedor")[0].reset();
    $("#id").val("");
    $("#modalProveedor").modal("show");
  });

  /* ============================================================
     4. Guardar / Actualizar proveedor
     ============================================================ */
  $("#formProveedor").submit(function (e) {
    e.preventDefault();

    let id = $("#id").val();
    let formData = new FormData(this);
    let url = id ? base_url + "proveedor/" + id : base_url + "proveedor";

    if (id) {
      formData.append("_method", "PUT"); // método PUT
    }

    // Convertir a mayúsculas ANTES de enviar
    $(this).find("input[type='text'], input[type='email'], textarea").each(function () {
      this.value = this.value.toUpperCase();
    });

    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $("#modalProveedor").modal("hide");
        table.ajax.reload();
        alert(response.message);
      },
    });
  });

  /* ============================================================
     5. Editar registro
     ============================================================ */
  $("#tablaProveedores").on("click", ".btn-editar", function () {
    let id = $(this).data("id");

    $.get(base_url + "proveedor/" + id, function (data) {
      $("#id").val(data.id);
      $("#nombre").val(data.nombre);
      $("#rep_legal").val(data.rep_legal);
      $("#ruc").val(data.ruc);
      $("#telefono").val(data.telefono);
      $("#tel_fijo").val(data.tel_fijo);
      $("#correo").val(data.correo);
      $("#direccion").val(data.direccion);
      $("#giro").val(data.giro);
      $("#estado").val(data.estado);

      $("#modalProveedor").modal("show");
    });
  });

  /* ============================================================
     6. Eliminar registro
     ============================================================ */
  $("#tablaProveedores").on("click", ".btn-eliminar", function () {
    if (!confirm("¿Desea eliminar este proveedor?")) return;

    let id = $(this).data("id");

    $.ajax({
      url: base_url + "proveedor/" + id,
      type: "DELETE",
      success: function (response) {
        table.ajax.reload();
        alert(response.message);
      },
    });
  });

  /* ============================================================
     7. Generar PDF
     ============================================================ */
  $("#tablaProveedores").on("click", ".btn-pdf", function () {
    window.open(base_url + "proveedor/pdf/" + $(this).data("id"), "_blank");
  });

});
