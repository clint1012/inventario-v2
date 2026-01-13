<?php

namespace Tests\Unit\Models;

use App\Models\AsignacionModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class AsignacionModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new AsignacionModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test inicialización del modelo
     */
    public function testModelInitialization(): void
    {
        $this->assertInstanceOf(AsignacionModel::class, $this->model);
        $this->assertEquals('movimientos', $this->model->getTable());
        $this->assertEquals('id', $this->model->primaryKey);
    }

    /**
     * Test campos permitidos
     */
    public function testAllowedFields(): void
    {
        $expectedFields = [
            'id_bienes',
            'id_personas',
            'id_departamentos',
            'id_locales',
            'id_persona_anterior',
            'id_departamento_anterior',
            'id_local_anterior',
            'estado_anterior',
            'tipo_movimiento',
            'fecha_movimiento',
            'observaciones',
            'lote',
            'anulado',
            'motivo_anulacion',
            'fecha_limite_prestamo'
        ];

        $allowedFields = $this->getPrivateProperty($this->model, 'allowedFields');
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $allowedFields);
        }
    }

    /**
     * Test inserción de asignación
     */
    public function testInsertAsignacion(): void
    {
        $data = [
            'id_bienes' => 1,
            'id_personas' => 1,
            'id_departamentos' => 1,
            'id_locales' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'observaciones' => 'Asignación de prueba',
            'lote' => 'LOTE_TEST_' . time(),
            'anulado' => 0
        ];

        $result = $this->model->insert($data);
        
        $this->assertIsNumeric($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * Test registro de datos anteriores en movimiento
     */
    public function testGuardarDatosAnteriores(): void
    {
        $data = [
            'id_bienes' => 1,
            'id_personas' => 2,
            'id_departamentos' => 2,
            'id_locales' => 2,
            'id_persona_anterior' => 1,
            'id_departamento_anterior' => 1,
            'id_local_anterior' => 1,
            'estado_anterior' => 'asignado',
            'tipo_movimiento' => 'reasignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'REASIG_TEST',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);
        $movimiento = $this->model->find($id);

        $this->assertEquals(1, $movimiento['id_persona_anterior']);
        $this->assertEquals(1, $movimiento['id_departamento_anterior']);
        $this->assertEquals('asignado', $movimiento['estado_anterior']);
    }

    /**
     * Test actualización de movimiento
     */
    public function testUpdateMovimiento(): void
    {
        $data = [
            'id_bienes' => 1,
            'id_personas' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'observaciones' => 'Observación inicial',
            'lote' => 'UPDATE_TEST',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);

        $updateData = [
            'observaciones' => 'Observación actualizada',
            'id_departamentos' => 2
        ];

        $result = $this->model->update($id, $updateData);
        $this->assertTrue($result);

        $updated = $this->model->find($id);
        $this->assertEquals('Observación actualizada', $updated['observaciones']);
        $this->assertEquals(2, $updated['id_departamentos']);
    }

    /**
     * Test anulación de movimiento
     */
    public function testAnularMovimiento(): void
    {
        $data = [
            'id_bienes' => 1,
            'id_personas' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'ANULAR_TEST',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);

        $anulacion = [
            'anulado' => 1,
            'motivo_anulacion' => 'Error en el registro'
        ];

        $result = $this->model->update($id, $anulacion);
        $this->assertTrue($result);

        $anulado = $this->model->find($id);
        $this->assertEquals(1, $anulado['anulado']);
        $this->assertEquals('Error en el registro', $anulado['motivo_anulacion']);
    }

    /**
     * Test registro de préstamo con fecha límite
     */
    public function testRegistroPrestamo(): void
    {
        $fechaLimite = date('Y-m-d', strtotime('+30 days'));
        
        $data = [
            'id_bienes' => 1,
            'id_personas' => 1,
            'tipo_movimiento' => 'prestamo',
            'fecha_movimiento' => date('Y-m-d'),
            'fecha_limite_prestamo' => $fechaLimite,
            'observaciones' => 'Préstamo temporal',
            'lote' => 'PRESTAMO_TEST',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);
        $prestamo = $this->model->find($id);

        $this->assertEquals('prestamo', $prestamo['tipo_movimiento']);
        $this->assertEquals($fechaLimite, $prestamo['fecha_limite_prestamo']);
    }

    /**
     * Test timestamps automáticos
     */
    public function testTimestamps(): void
    {
        $data = [
            'id_bienes' => 1,
            'id_personas' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'TIMESTAMP_TEST',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);
        $movimiento = $this->model->find($id);

        $this->assertArrayHasKey('created_at', $movimiento);
        $this->assertArrayHasKey('updated_at', $movimiento);
        $this->assertNotNull($movimiento['created_at']);
    }

    /**
     * Test diferentes tipos de movimiento
     */
    public function testTiposMovimiento(): void
    {
        $tipos = ['asignacion', 'reasignacion', 'prestamo', 'devolucion', 'baja'];

        foreach ($tipos as $tipo) {
            $data = [
                'id_bienes' => 1,
                'id_personas' => 1,
                'tipo_movimiento' => $tipo,
                'fecha_movimiento' => date('Y-m-d'),
                'lote' => 'TIPO_' . strtoupper($tipo),
                'anulado' => 0
            ];

            $id = $this->model->insert($data);
            $movimiento = $this->model->find($id);

            $this->assertEquals($tipo, $movimiento['tipo_movimiento']);
        }
    }

    /**
     * Test eliminar movimiento
     */
    public function testDeleteMovimiento(): void
    {
        $data = [
            'id_bienes' => 1,
            'id_personas' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'DELETE_TEST',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);
        
        $result = $this->model->delete($id);
        $this->assertTrue($result);

        $deleted = $this->model->find($id);
        $this->assertNull($deleted);
    }

    /**
     * Test método getMovimientosConDetalles retorna array
     */
    public function testGetMovimientosConDetalles(): void
    {
        $result = $this->model->getMovimientosConDetalles();
        
        $this->assertIsArray($result);
        
        // Si hay datos, verificar que tiene las columnas esperadas
        if (count($result) > 0) {
            $primer = $result[0];
            $this->assertArrayHasKey('tipo_movimiento', $primer);
            $this->assertArrayHasKey('fecha_movimiento', $primer);
            $this->assertArrayHasKey('lote', $primer);
        }
    }
}
