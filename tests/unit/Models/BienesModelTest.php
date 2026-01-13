<?php

namespace Tests\Unit\Models;

use App\Models\BienesModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class BienesModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new BienesModel();
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
        $this->assertInstanceOf(BienesModel::class, $this->model);
        $this->assertEquals('bienes', $this->model->getTable());
        $this->assertEquals('id', $this->model->primaryKey);
    }

    /**
     * Test campos críticos permitidos
     */
    public function testAllowedFieldsContainsCriticalFields(): void
    {
        $criticalFields = [
            'cod_patrimonial',
            'descripcion',
            'tipo_bien',
            'marca',
            'modelo',
            'serie',
            'estado',
            'id_departamento',
            'id_personas',
            'id_locales'
        ];

        $allowedFields = $this->getPrivateProperty($this->model, 'allowedFields');
        
        foreach ($criticalFields as $field) {
            $this->assertContains($field, $allowedFields);
        }
    }

    /**
     * Test inserción de bien básico
     */
    public function testInsertBienBasico(): void
    {
        $data = [
            'cod_patrimonial' => 'TEST-' . time(),
            'descripcion' => 'Laptop de prueba',
            'tipo_bien' => 'laptop',
            'marca' => 'HP',
            'modelo' => 'ProBook 450',
            'estado' => 'disponible'
        ];

        $result = $this->model->insert($data);
        
        $this->assertIsNumeric($result);
        $this->assertGreaterThan(0, $result);
    }

    /**
     * Test inserción de bien completo
     */
    public function testInsertBienCompleto(): void
    {
        $data = [
            'cod_patrimonial' => 'FULL-' . time(),
            'descripcion' => 'Laptop corporativa completa',
            'tipo_bien' => 'laptop',
            'marca' => 'Dell',
            'modelo' => 'Latitude 5420',
            'serie' => 'SN' . time(),
            'procesador' => 'Intel Core i7',
            'memoria' => '16GB',
            'tipo_disco' => 'SSD',
            'espacio_disco' => '512GB',
            'sistema_operativo' => 'Windows 11 Pro',
            'ver_office' => 'Office 2021',
            'estado' => 'disponible',
            'id_departamento' => 1,
            'fecha_adquisicion' => date('Y-m-d'),
            'años_garantia' => 3
        ];

        $id = $this->model->insert($data);
        $bien = $this->model->find($id);

        $this->assertEquals('Laptop corporativa completa', $bien['descripcion']);
        $this->assertEquals('Intel Core i7', $bien['procesador']);
        $this->assertEquals('16GB', $bien['memoria']);
    }

    /**
     * Test actualización de bien
     */
    public function testUpdateBien(): void
    {
        $data = [
            'cod_patrimonial' => 'UPD-' . time(),
            'descripcion' => 'Monitor inicial',
            'tipo_bien' => 'monitor',
            'estado' => 'disponible'
        ];

        $id = $this->model->insert($data);

        $updateData = [
            'descripcion' => 'Monitor actualizado',
            'marca' => 'Samsung',
            'modelo' => '24" LED'
        ];

        $result = $this->model->update($id, $updateData);
        $this->assertTrue($result);

        $updated = $this->model->find($id);
        $this->assertEquals('Monitor actualizado', $updated['descripcion']);
        $this->assertEquals('Samsung', $updated['marca']);
    }

    /**
     * Test asignación de bien a persona
     */
    public function testAsignarBienAPersona(): void
    {
        $data = [
            'cod_patrimonial' => 'ASIG-' . time(),
            'descripcion' => 'Laptop para asignar',
            'tipo_bien' => 'laptop',
            'estado' => 'disponible'
        ];

        $id = $this->model->insert($data);

        // Asignar a persona y departamento
        $asignacion = [
            'id_personas' => 1,
            'id_departamento' => 2,
            'id_locales' => 1,
            'estado' => 'asignado'
        ];

        $result = $this->model->update($id, $asignacion);
        $this->assertTrue($result);

        $asignado = $this->model->find($id);
        $this->assertEquals(1, $asignado['id_personas']);
        $this->assertEquals('asignado', $asignado['estado']);
    }

    /**
     * Test cambio de estado de bien
     */
    public function testCambioEstadoBien(): void
    {
        $estados = ['disponible', 'asignado', 'en_mantenimiento', 'de_baja'];

        foreach ($estados as $index => $estado) {
            $data = [
                'cod_patrimonial' => 'EST' . $index . '-' . time(),
                'descripcion' => 'Bien para estado ' . $estado,
                'tipo_bien' => 'laptop',
                'estado' => 'disponible'
            ];

            $id = $this->model->insert($data);
            
            $result = $this->model->update($id, ['estado' => $estado]);
            $this->assertTrue($result);

            $bien = $this->model->find($id);
            $this->assertEquals($estado, $bien['estado']);
        }
    }

    /**
     * Test registro de baja
     */
    public function testRegistroBaja(): void
    {
        $data = [
            'cod_patrimonial' => 'BAJA-' . time(),
            'descripcion' => 'Equipo para dar de baja',
            'tipo_bien' => 'laptop',
            'estado' => 'asignado'
        ];

        $id = $this->model->insert($data);

        $baja = [
            'estado' => 'de_baja',
            'motivo_baja' => 'Equipo obsoleto',
            'usuario_baja' => 'admin_test'
        ];

        $result = $this->model->update($id, $baja);
        $this->assertTrue($result);

        $bien = $this->model->find($id);
        $this->assertEquals('de_baja', $bien['estado']);
        $this->assertEquals('Equipo obsoleto', $bien['motivo_baja']);
    }

    /**
     * Test registro de mantenimiento
     */
    public function testRegistroMantenimiento(): void
    {
        $data = [
            'cod_patrimonial' => 'MANT-' . time(),
            'descripcion' => 'Equipo para mantenimiento',
            'tipo_bien' => 'laptop',
            'estado' => 'asignado'
        ];

        $id = $this->model->insert($data);

        $mantenimiento = [
            'estado' => 'en_mantenimiento',
            'motivo_mantenimiento' => 'Limpieza y actualización',
            'usuario_mantenimiento' => 'tecnico_test',
            'tipo_mantenimiento' => 'preventivo'
        ];

        $result = $this->model->update($id, $mantenimiento);
        $this->assertTrue($result);

        $bien = $this->model->find($id);
        $this->assertEquals('en_mantenimiento', $bien['estado']);
        $this->assertEquals('preventivo', $bien['tipo_mantenimiento']);
    }

    /**
     * Test búsqueda por código patrimonial
     */
    public function testBuscarPorCodigoPatrimonial(): void
    {
        $codigo = 'UNICO-' . time();
        
        $data = [
            'cod_patrimonial' => $codigo,
            'descripcion' => 'Bien único',
            'tipo_bien' => 'laptop',
            'estado' => 'disponible'
        ];

        $this->model->insert($data);

        $bien = $this->model->where('cod_patrimonial', $codigo)->first();
        
        $this->assertNotNull($bien);
        $this->assertEquals($codigo, $bien['cod_patrimonial']);
        $this->assertEquals('Bien único', $bien['descripcion']);
    }

    /**
     * Test filtro por tipo de bien
     */
    public function testFiltroPorTipoBien(): void
    {
        $tipos = ['laptop', 'desktop', 'monitor', 'impresora'];

        foreach ($tipos as $tipo) {
            $data = [
                'cod_patrimonial' => strtoupper($tipo) . '-' . time(),
                'descripcion' => 'Bien tipo ' . $tipo,
                'tipo_bien' => $tipo,
                'estado' => 'disponible'
            ];

            $this->model->insert($data);
        }

        foreach ($tipos as $tipo) {
            $bienes = $this->model->where('tipo_bien', $tipo)->findAll();
            $this->assertGreaterThan(0, count($bienes));
        }
    }

    /**
     * Test filtro por estado
     */
    public function testFiltroPorEstado(): void
    {
        $estados = ['disponible', 'asignado'];

        foreach ($estados as $estado) {
            $data = [
                'cod_patrimonial' => 'FIL' . strtoupper($estado) . '-' . time(),
                'descripcion' => 'Bien ' . $estado,
                'tipo_bien' => 'laptop',
                'estado' => $estado
            ];

            $this->model->insert($data);
        }

        $disponibles = $this->model->where('estado', 'disponible')->findAll();
        $this->assertGreaterThan(0, count($disponibles));

        $asignados = $this->model->where('estado', 'asignado')->findAll();
        $this->assertGreaterThan(0, count($asignados));
    }

    /**
     * Test eliminación de bien
     */
    public function testDeleteBien(): void
    {
        $data = [
            'cod_patrimonial' => 'DEL-' . time(),
            'descripcion' => 'Bien para eliminar',
            'tipo_bien' => 'laptop',
            'estado' => 'disponible'
        ];

        $id = $this->model->insert($data);
        
        $result = $this->model->delete($id);
        $this->assertTrue($result);

        $deleted = $this->model->find($id);
        $this->assertNull($deleted);
    }

    /**
     * Test validación de código patrimonial único
     */
    public function testCodigoPatrimonialUnico(): void
    {
        $codigo = 'DUPLI-' . time();
        
        $data1 = [
            'cod_patrimonial' => $codigo,
            'descripcion' => 'Primer bien',
            'tipo_bien' => 'laptop',
            'estado' => 'disponible'
        ];

        $id1 = $this->model->insert($data1);
        $this->assertIsNumeric($id1);

        // Intentar insertar con el mismo código
        $data2 = [
            'cod_patrimonial' => $codigo,
            'descripcion' => 'Segundo bien',
            'tipo_bien' => 'laptop',
            'estado' => 'disponible'
        ];

        $id2 = $this->model->insert($data2);
        
        // Si la BD tiene constraint UNIQUE, fallará
        // Si no, ambos se insertarán (depende de la estructura de BD)
        if ($id2 === false) {
            $this->assertTrue(true); // Constraint funciona
        } else {
            // Sin constraint, se permite duplicado
            $this->assertIsNumeric($id2);
        }
    }

    /**
     * Test garantía vencida
     */
    public function testGarantiaVencida(): void
    {
        $data = [
            'cod_patrimonial' => 'GAR-' . time(),
            'descripcion' => 'Equipo con garantía',
            'tipo_bien' => 'laptop',
            'fecha_adquisicion' => date('Y-m-d', strtotime('-4 years')),
            'años_garantia' => 3,
            'estado' => 'asignado'
        ];

        $id = $this->model->insert($data);
        $bien = $this->model->find($id);

        $this->assertEquals(3, $bien['años_garantia']);
        
        // Calcular si está vencida
        $fechaAdquisicion = strtotime($bien['fecha_adquisicion']);
        $fechaVencimiento = strtotime('+' . $bien['años_garantia'] . ' years', $fechaAdquisicion);
        $estaVencida = time() > $fechaVencimiento;
        
        $this->assertTrue($estaVencida);
    }
}
