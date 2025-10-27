<?php echo $this->extend('plantilla'); ?>

<?= $this->section('contenido'); ?>

<h2>Inventario y Asignación de Direcciones IP</h2>
<p>Listado de IPs con la asignación a Persona, Área y Activo Patrimonial.</p>

<?php if (session()->getFlashdata('mensaje')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('mensaje') ?></div>
<?php endif ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif ?>

<div class="table-responsive">
    <table id="ipTable" class="display table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>IP</th>
                <th>Nombre</th>
                <th>Área</th>
                <th>Piso</th>
                <th>Cód. Patrimonial</th>
                <th>Descripción Cód. Patrimonial</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts_datatable_ip') ?> 
<script>
$(document).ready(function() {
    $('#ipTable').DataTable({
        "processing": true,
        "ajax": {
            "url": "<?= base_url('ip/datatables') ?>",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": 0 }, // direccion_ip
            { "data": 1 }, // nombre_persona
            { "data": 2 }, // nombre_area
            { "data": 3 }, // piso
            { "data": 4 }, // cod_patrimonial
            { "data": 5 }, // descripcion_bien
            { "data": 6, "orderable": false, "searchable": false } // Acciones
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[ 0, "asc" ]] 
    });
});
</script>
<?= $this->endSection() ?>