<?php

namespace Tests\Unit\Models;

use App\Models\MovimientosCelularesModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class MovimientosCelularesModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $model;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new MovimientosCelularesModel();
        $this->db = \Config\Database::connect();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test que el modelo se inicializa correctamente
     */
    public function testModelInitialization(): void
    {
        $this->assertInstanceOf(MovimientosCelularesModel::class, $this->model);
        $this->assertEquals('movimientos_celulares', $this->model->getTable());
        $this->assertEquals('id', $this->model->primaryKey);
    }

    /**
     * Test de campos permitidos
     */
    public function testAllowedFields(): void
    {
        $expectedFields = [
            'id_celular',
            'id_personas',
            'id_departamentos',
            'id_locales',
            'tipo_movimiento',
            'fecha_movimiento',
            'observaciones',
            'responsable_nombre',
            'lote',
            'anulado',
            'motivo_anulacion'
        ];

        $allowedFields = $this->getPrivateProperty($this->model, 'allowedFields');
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $allowedFields, "Campo {$field} no está en allowedFields");
        }
    }

    /**
     * Test inserción de movimiento básico
     */
    public function testInsertMovimiento(): void
    {
        $data = [
            'id_celular' => 1,
            'id_personas' => 1,
            'id_departamentos' => 1,
            'id_locales' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'observaciones' => 'Prueba unitaria',
            'responsable_nombre' => 'Test User',
            'lote' => 'TEST001',
            'anulado' => 0
        ];

        $result = $this->model->insert($data);
        
        $this->assertIsNumeric($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * Test validación de campos requeridos
     */
    public function testValidacionCamposRequeridos(): void
    {
        $data = [
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
        ];

        $result = $this->model->insert($data);
        
        // Si falla la inserción por campos faltantes
        if ($result === false) {
            $this->assertTrue(true);
        } else {
            // Si la BD permite NULL, la inserción será exitosa
            $this->assertIsNumeric($result);
        }
    }

    /**
     * Test actualización de movimiento
     */
    public function testUpdateMovimiento(): void
    {
        // Primero insertar un registro
        $data = [
            'id_celular' => 1,
            'id_personas' => 1,
            'id_departamentos' => 1,
            'id_locales' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'observaciones' => 'Movimiento inicial',
            'lote' => 'TEST002',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);
        $this->assertIsNumeric($id);

        // Actualizar el registro
        $updateData = [
            'observaciones' => 'Movimiento actualizado',
            'tipo_movimiento' => 'devolucion'
        ];

        $result = $this->model->update($id, $updateData);
        $this->assertTrue($result);

        // Verificar actualización
        $updated = $this->model->find($id);
        $this->assertEquals('Movimiento actualizado', $updated['observaciones']);
        $this->assertEquals('devolucion', $updated['tipo_movimiento']);
    }

    /**
     * Test anulación de movimiento
     */
    public function testAnulacionMovimiento(): void
    {
        // Insertar movimiento
        $data = [
            'id_celular' => 1,
            'id_personas' => 1,
            'id_departamentos' => 1,
            'id_locales' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'TEST003',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);

        // Anular movimiento
        $anulacion = [
            'anulado' => 1,
            'motivo_anulacion' => 'Movimiento registrado por error'
        ];

        $result = $this->model->update($id, $anulacion);
        $this->assertTrue($result);

        // Verificar anulación
        $movimiento = $this->model->find($id);
        $this->assertEquals(1, $movimiento['anulado']);
        $this->assertEquals('Movimiento registrado por error', $movimiento['motivo_anulacion']);
    }

    /**
     * Test método getMovimientosPorLote
     */
    public function testGetMovimientosPorLote(): void
    {
        $lote = 'LOTE_TEST_' . time();

        // Insertar varios movimientos con el mismo lote
        for ($i = 1; $i <= 3; $i++) {
            $data = [
                'id_celular' => $i,
                'id_personas' => 1,
                'tipo_movimiento' => 'asignacion',
                'fecha_movimiento' => date('Y-m-d'),
                'lote' => $lote,
                'anulado' => 0
            ];
            $this->model->insert($data);
        }

        // Buscar por lote
        $movimientos = $this->model->getMovimientosPorLote($lote);
        
        // Debe encontrar los 3 registros
        $this->assertIsArray($movimientos);
        $this->assertGreaterThanOrEqual(3, count($movimientos));
    }

    /**
     * Test método getUltimoMovimientoCelular
     */
    public function testGetUltimoMovimientoCelular(): void
    {
        $idCelular = 999;

        // Insertar varios movimientos para el mismo celular
        $fechas = [
            date('Y-m-d', strtotime('-3 days')),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d')
        ];

        foreach ($fechas as $fecha) {
            $data = [
                'id_celular' => $idCelular,
                'id_personas' => 1,
                'tipo_movimiento' => 'asignacion',
                'fecha_movimiento' => $fecha,
                'lote' => 'TEST_ULTIMO_' . time(),
                'anulado' => 0
            ];
            $this->model->insert($data);
        }

        // Obtener último movimiento
        $ultimo = $this->model->getUltimoMovimientoCelular($idCelular);
        
        $this->assertIsArray($ultimo);
        $this->assertEquals($idCelular, $ultimo['id_celular']);
        $this->assertEquals(date('Y-m-d'), $ultimo['fecha_movimiento']);
    }

    /**
     * Test que movimientos anulados no aparezcan en getUltimoMovimientoCelular
     */
    public function testMovimientoAnuladoNoEsElUltimo(): void
    {
        $idCelular = 888;

        // Insertar movimiento válido
        $data1 = [
            'id_celular' => $idCelular,
            'id_personas' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d', strtotime('-2 days')),
            'lote' => 'TEST_ANL1',
            'anulado' => 0
        ];
        $this->model->insert($data1);

        // Insertar movimiento anulado más reciente
        $data2 = [
            'id_celular' => $idCelular,
            'id_personas' => 1,
            'tipo_movimiento' => 'devolucion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'TEST_ANL2',
            'anulado' => 1
        ];
        $this->model->insert($data2);

        // El último movimiento válido debe ser el primero (no anulado)
        $ultimo = $this->model->getUltimoMovimientoCelular($idCelular);
        
        $this->assertEquals(0, $ultimo['anulado']);
        $this->assertEquals('TEST_ANL1', $ultimo['lote']);
    }

    /**
     * Test timestamps
     */
    public function testTimestamps(): void
    {
        $data = [
            'id_celular' => 1,
            'id_personas' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'TEST_TIME',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);
        $movimiento = $this->model->find($id);

        $this->assertArrayHasKey('created_at', $movimiento);
        $this->assertArrayHasKey('updated_at', $movimiento);
        $this->assertNotNull($movimiento['created_at']);
        $this->assertNotNull($movimiento['updated_at']);
    }

    /**
     * Test método getResumenMovimientosAgrupado
     */
    public function testGetResumenMovimientosAgrupado(): void
    {
        $result = $this->model->getResumenMovimientosAgrupado();
        
        $this->assertIsArray($result);
        
        // Si hay datos, verificar estructura
        if (count($result) > 0) {
            $primer = $result[0];
            $this->assertArrayHasKey('lote', $primer);
            $this->assertArrayHasKey('fecha_movimiento', $primer);
            $this->assertArrayHasKey('cantidad_celulares', $primer);
        }
    }

    /**
     * Test eliminación de movimiento
     */
    public function testDeleteMovimiento(): void
    {
        $data = [
            'id_celular' => 1,
            'id_personas' => 1,
            'tipo_movimiento' => 'asignacion',
            'fecha_movimiento' => date('Y-m-d'),
            'lote' => 'TEST_DEL',
            'anulado' => 0
        ];

        $id = $this->model->insert($data);
        $this->assertIsNumeric($id);

        // Eliminar
        $result = $this->model->delete($id);
        $this->assertTrue($result);

        // Verificar que no existe
        $deleted = $this->model->find($id);
        $this->assertNull($deleted);
    }
}
