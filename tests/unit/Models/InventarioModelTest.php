<?php

namespace Tests\Unit\Models;

use App\Models\InventarioModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class InventarioModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new InventarioModel();
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
        $this->assertInstanceOf(InventarioModel::class, $this->model);
        $this->assertEquals('inventarios', $this->model->getTable());
        $this->assertEquals('id', $this->model->primaryKey);
    }

    /**
     * Test campos permitidos
     */
    public function testAllowedFields(): void
    {
        $expectedFields = ['anio', 'mes', 'usuario_id', 'jefe_id', 'observacion'];
        
        $allowedFields = $this->getPrivateProperty($this->model, 'allowedFields');
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $allowedFields);
        }
    }

    /**
     * Test creación de inventario
     */
    public function testCrearInventario(): void
    {
        $data = [
            'anio' => 2025,
            'mes' => 1,
            'usuario_id' => 1,
            'jefe_id' => 2,
            'observacion' => 'Inventario de prueba enero 2025'
        ];

        $result = $this->model->insert($data);
        
        $this->assertIsNumeric($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * Test inserción mínima
     */
    public function testInsertMinimo(): void
    {
        $data = [
            'anio' => 2025,
            'mes' => 2,
            'usuario_id' => 1
        ];

        $result = $this->model->insert($data);
        
        $this->assertIsNumeric($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * Test actualización de inventario
     */
    public function testUpdateInventario(): void
    {
        $data = [
            'anio' => 2025,
            'mes' => 3,
            'usuario_id' => 1,
            'observacion' => 'Observación inicial'
        ];

        $id = $this->model->insert($data);

        $updateData = [
            'observacion' => 'Observación actualizada',
            'jefe_id' => 3
        ];

        $result = $this->model->update($id, $updateData);
        $this->assertTrue($result);

        $updated = $this->model->find($id);
        $this->assertEquals('Observación actualizada', $updated['observacion']);
        $this->assertEquals(3, $updated['jefe_id']);
    }

    /**
     * Test búsqueda por año
     */
    public function testBuscarPorAnio(): void
    {
        $anio = 2025;
        
        // Insertar varios inventarios del mismo año
        for ($mes = 1; $mes <= 3; $mes++) {
            $data = [
                'anio' => $anio,
                'mes' => $mes,
                'usuario_id' => 1
            ];
            $this->model->insert($data);
        }

        $inventarios = $this->model->where('anio', $anio)->findAll();
        
        $this->assertIsArray($inventarios);
        $this->assertGreaterThanOrEqual(3, count($inventarios));
    }

    /**
     * Test búsqueda por año y mes
     */
    public function testBuscarPorAnioYMes(): void
    {
        $data = [
            'anio' => 2025,
            'mes' => 6,
            'usuario_id' => 1,
            'observacion' => 'Inventario junio 2025'
        ];

        $this->model->insert($data);

        $inventario = $this->model->where([
            'anio' => 2025,
            'mes' => 6
        ])->first();

        $this->assertNotNull($inventario);
        $this->assertEquals(2025, $inventario['anio']);
        $this->assertEquals(6, $inventario['mes']);
    }

    /**
     * Test validación de meses válidos
     */
    public function testMesesValidos(): void
    {
        $mesesValidos = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

        foreach ($mesesValidos as $mes) {
            $data = [
                'anio' => 2025,
                'mes' => $mes,
                'usuario_id' => 1
            ];

            $result = $this->model->insert($data);
            $this->assertIsNumeric($result);
        }
    }

    /**
     * Test timestamps
     */
    public function testTimestamps(): void
    {
        $data = [
            'anio' => 2025,
            'mes' => 8,
            'usuario_id' => 1
        ];

        $id = $this->model->insert($data);
        $inventario = $this->model->find($id);

        $this->assertArrayHasKey('created_at', $inventario);
        $this->assertArrayHasKey('updated_at', $inventario);
        $this->assertNotNull($inventario['created_at']);
    }

    /**
     * Test eliminar inventario
     */
    public function testDeleteInventario(): void
    {
        $data = [
            'anio' => 2025,
            'mes' => 12,
            'usuario_id' => 1,
            'observacion' => 'Inventario para eliminar'
        ];

        $id = $this->model->insert($data);
        
        $result = $this->model->delete($id);
        $this->assertTrue($result);

        $deleted = $this->model->find($id);
        $this->assertNull($deleted);
    }

    /**
     * Test asignación de jefe
     */
    public function testAsignacionJefe(): void
    {
        $data = [
            'anio' => 2025,
            'mes' => 9,
            'usuario_id' => 5,
            'jefe_id' => 10,
            'observacion' => 'Inventario con jefe asignado'
        ];

        $id = $this->model->insert($data);
        $inventario = $this->model->find($id);

        $this->assertEquals(10, $inventario['jefe_id']);
    }

    /**
     * Test múltiples inventarios por usuario
     */
    public function testMultiplesInventariosPorUsuario(): void
    {
        $usuarioId = 99;
        
        for ($i = 1; $i <= 5; $i++) {
            $data = [
                'anio' => 2025,
                'mes' => $i,
                'usuario_id' => $usuarioId
            ];
            $this->model->insert($data);
        }

        $inventarios = $this->model->where('usuario_id', $usuarioId)->findAll();
        
        $this->assertGreaterThanOrEqual(5, count($inventarios));
    }

    /**
     * Test ordenamiento por fecha (año y mes)
     */
    public function testOrdenamientoPorFecha(): void
    {
        $inventarios = [
            ['anio' => 2024, 'mes' => 12, 'usuario_id' => 1],
            ['anio' => 2025, 'mes' => 1, 'usuario_id' => 1],
            ['anio' => 2025, 'mes' => 2, 'usuario_id' => 1]
        ];

        foreach ($inventarios as $inv) {
            $this->model->insert($inv);
        }

        $resultado = $this->model
            ->orderBy('anio', 'DESC')
            ->orderBy('mes', 'DESC')
            ->findAll();

        $this->assertIsArray($resultado);
        $this->assertGreaterThan(0, count($resultado));
    }

    /**
     * Test contar inventarios por año
     */
    public function testContarInventariosPorAnio(): void
    {
        $anio = 2026;
        
        // Insertar 3 inventarios
        for ($i = 1; $i <= 3; $i++) {
            $data = [
                'anio' => $anio,
                'mes' => $i,
                'usuario_id' => 1
            ];
            $this->model->insert($data);
        }

        $count = $this->model->where('anio', $anio)->countAllResults();
        
        $this->assertGreaterThanOrEqual(3, $count);
    }
}
