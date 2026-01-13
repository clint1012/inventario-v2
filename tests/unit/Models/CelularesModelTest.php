<?php

namespace Tests\Unit\Models;

use App\Models\CelularesModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class CelularesModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new CelularesModel();
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
        $this->assertInstanceOf(CelularesModel::class, $this->model);
        $this->assertEquals('celulares', $this->model->getTable());
        $this->assertEquals('id', $this->model->primaryKey);
    }

    /**
     * Test campos permitidos
     */
    public function testAllowedFields(): void
    {
        $expectedFields = [
            'numero_serie',
            'imei',
            'modelo',
            'descripcion',
            'estado'
        ];

        $allowedFields = $this->getPrivateProperty($this->model, 'allowedFields');
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $allowedFields);
        }
    }

    /**
     * Test inserción de celular básico
     */
    public function testInsertCelular(): void
    {
        $data = [
            'numero_serie' => 'SN' . time(),
            'imei' => 'IMEI' . time(),
            'modelo' => 'iPhone 13',
            'descripcion' => 'iPhone de prueba',
            'estado' => 'disponible'
        ];

        $result = $this->model->insert($data);
        
        $this->assertIsNumeric($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * Test validación de IMEI requerido
     */
    public function testValidacionImeiRequerido(): void
    {
        $data = [
            'modelo' => 'Samsung Galaxy',
            'estado' => 'disponible'
        ];

        $result = $this->model->insert($data);
        
        // La validación debe fallar
        $this->assertFalse($result);
        
        $errors = $this->model->errors();
        $this->assertArrayHasKey('imei', $errors);
    }

    /**
     * Test validación de modelo requerido
     */
    public function testValidacionModeloRequerido(): void
    {
        $data = [
            'imei' => 'IMEI_SIN_MODELO_' . time(),
            'estado' => 'disponible'
        ];

        $result = $this->model->insert($data);
        
        $this->assertFalse($result);
        
        $errors = $this->model->errors();
        $this->assertArrayHasKey('modelo', $errors);
    }

    /**
     * Test validación de IMEI único
     */
    public function testValidacionImeiUnico(): void
    {
        $imei = 'IMEI_UNICO_' . time();
        
        $data1 = [
            'imei' => $imei,
            'modelo' => 'Modelo A',
            'estado' => 'disponible'
        ];

        $id1 = $this->model->insert($data1);
        $this->assertIsNumeric($id1);

        // Intentar insertar con el mismo IMEI
        $data2 = [
            'imei' => $imei,
            'modelo' => 'Modelo B',
            'estado' => 'disponible'
        ];

        $id2 = $this->model->insert($data2);
        
        // Debe fallar por IMEI duplicado
        $this->assertFalse($id2);
        
        $errors = $this->model->errors();
        $this->assertArrayHasKey('imei', $errors);
    }

    /**
     * Test actualización de celular
     */
    public function testUpdateCelular(): void
    {
        $data = [
            'imei' => 'IMEI_UPDATE_' . time(),
            'modelo' => 'Modelo inicial',
            'descripcion' => 'Descripción inicial',
            'estado' => 'disponible'
        ];

        $id = $this->model->insert($data);

        $updateData = [
            'descripcion' => 'Descripción actualizada',
            'estado' => 'asignado'
        ];

        $result = $this->model->update($id, $updateData);
        $this->assertTrue($result);

        $updated = $this->model->find($id);
        $this->assertEquals('Descripción actualizada', $updated['descripcion']);
        $this->assertEquals('asignado', $updated['estado']);
    }

    /**
     * Test método getCelularesDisponibles
     */
    public function testGetCelularesDisponibles(): void
    {
        // Insertar celulares con diferentes estados
        $celulares = [
            [
                'imei' => 'IMEI_DISP1_' . time(),
                'modelo' => 'iPhone 12',
                'estado' => 'disponible'
            ],
            [
                'imei' => 'IMEI_DISP2_' . time(),
                'modelo' => 'Samsung S21',
                'estado' => 'disponible'
            ],
            [
                'imei' => 'IMEI_ASIG_' . time(),
                'modelo' => 'Xiaomi Mi 11',
                'estado' => 'asignado'
            ]
        ];

        foreach ($celulares as $celular) {
            $this->model->insert($celular);
        }

        $disponibles = $this->model->getCelularesDisponibles();
        
        $this->assertIsArray($disponibles);
        $this->assertGreaterThanOrEqual(2, count($disponibles));
        
        // Verificar que todos los retornados estén disponibles
        foreach ($disponibles as $celular) {
            $this->assertEquals('disponible', $celular['estado']);
        }
    }

    /**
     * Test cambio de estado de celular
     */
    public function testCambioEstadoCelular(): void
    {
        $estados = ['disponible', 'asignado', 'en_mantenimiento', 'de_baja'];

        foreach ($estados as $index => $estado) {
            $data = [
                'imei' => 'IMEI_EST' . $index . '_' . time(),
                'modelo' => 'Modelo ' . $estado,
                'estado' => 'disponible'
            ];

            $id = $this->model->insert($data);
            
            $result = $this->model->update($id, ['estado' => $estado]);
            $this->assertTrue($result);

            $celular = $this->model->find($id);
            $this->assertEquals($estado, $celular['estado']);
        }
    }

    /**
     * Test búsqueda por IMEI
     */
    public function testBuscarPorImei(): void
    {
        $imei = 'IMEI_BUSCAR_' . time();
        
        $data = [
            'imei' => $imei,
            'modelo' => 'iPhone 14',
            'descripcion' => 'iPhone para buscar',
            'estado' => 'disponible'
        ];

        $this->model->insert($data);

        $celular = $this->model->where('imei', $imei)->first();
        
        $this->assertNotNull($celular);
        $this->assertEquals($imei, $celular['imei']);
        $this->assertEquals('iPhone 14', $celular['modelo']);
    }

    /**
     * Test filtro por modelo
     */
    public function testFiltroPorModelo(): void
    {
        $modelos = ['iPhone 13', 'Samsung S22', 'Xiaomi Mi 12'];

        foreach ($modelos as $modelo) {
            $data = [
                'imei' => 'IMEI_' . str_replace(' ', '_', $modelo) . '_' . time(),
                'modelo' => $modelo,
                'estado' => 'disponible'
            ];

            $this->model->insert($data);
        }

        foreach ($modelos as $modelo) {
            $celulares = $this->model->where('modelo', $modelo)->findAll();
            $this->assertGreaterThan(0, count($celulares));
        }
    }

    /**
     * Test timestamps
     */
    public function testTimestamps(): void
    {
        $data = [
            'imei' => 'IMEI_TIME_' . time(),
            'modelo' => 'iPhone Test',
            'estado' => 'disponible'
        ];

        $id = $this->model->insert($data);
        $celular = $this->model->find($id);

        $this->assertArrayHasKey('created_at', $celular);
        $this->assertArrayHasKey('updated_at', $celular);
        $this->assertNotNull($celular['created_at']);
    }

    /**
     * Test eliminar celular
     */
    public function testDeleteCelular(): void
    {
        $data = [
            'imei' => 'IMEI_DEL_' . time(),
            'modelo' => 'Celular para eliminar',
            'estado' => 'disponible'
        ];

        $id = $this->model->insert($data);
        
        $result = $this->model->delete($id);
        $this->assertTrue($result);

        $deleted = $this->model->find($id);
        $this->assertNull($deleted);
    }

    /**
     * Test celulares asignados no aparecen como disponibles
     */
    public function testCelularesAsignadosNoDisponibles(): void
    {
        $data = [
            'imei' => 'IMEI_ASIGNADO_' . time(),
            'modelo' => 'iPhone asignado',
            'estado' => 'asignado'
        ];

        $id = $this->model->insert($data);
        
        $disponibles = $this->model->getCelularesDisponibles();
        
        // Verificar que el celular asignado no esté en disponibles
        $found = false;
        foreach ($disponibles as $celular) {
            if ($celular['id'] == $id) {
                $found = true;
                break;
            }
        }
        
        $this->assertFalse($found);
    }

    /**
     * Test datos completos del celular
     */
    public function testDatosCompletosCelular(): void
    {
        $data = [
            'numero_serie' => 'SN123456789',
            'imei' => 'IMEI_COMPLETO_' . time(),
            'modelo' => 'Samsung Galaxy S22 Ultra',
            'descripcion' => 'Celular corporativo con todos los datos completos',
            'estado' => 'disponible'
        ];

        $id = $this->model->insert($data);
        $celular = $this->model->find($id);

        $this->assertEquals('SN123456789', $celular['numero_serie']);
        $this->assertEquals('Samsung Galaxy S22 Ultra', $celular['modelo']);
        $this->assertEquals('Celular corporativo con todos los datos completos', $celular['descripcion']);
    }
}
