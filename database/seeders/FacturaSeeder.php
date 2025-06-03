<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Factura;
use Carbon\Carbon;

class FacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facturas = [
            [
                'numero_factura' => '001-001-000000001',
                'clave_acceso' => '0306202501179214673900110010010000000011234567890',
                'estado' => 'AUTORIZADA',
                'ambiente' => '2',
                'fecha_emision' => Carbon::now()->subDays(10),
                'fecha_autorizacion' => Carbon::now()->subDays(10)->addHours(2),
                'numero_autorizacion' => '2025060312345678901234567890123456789012345678',
                'xml_ruta' => '/storage/facturas/xml/factura_001.xml',
                'xml_firmado_ruta' => '/storage/facturas/xml_firmado/factura_001_firmado.xml',
                'pdf_ruta' => '/storage/facturas/pdf/factura_001.pdf',
                'certificado_propietario' => 'EMPRESA DEMO S.A.',
                'certificado_vigencia_hasta' => Carbon::now()->addYear(),
                'observaciones' => 'Factura de prueba autorizada correctamente'
            ],
            [
                'numero_factura' => '001-001-000000002',
                'clave_acceso' => '0306202501179214673900110010010000000021234567891',
                'estado' => 'PENDIENTE',
                'ambiente' => '1',
                'fecha_emision' => Carbon::now()->subDays(5),
                'xml_ruta' => '/storage/facturas/xml/factura_002.xml',
                'certificado_propietario' => 'EMPRESA DEMO S.A.',
                'certificado_vigencia_hasta' => Carbon::now()->addYear(),
                'observaciones' => 'Factura en ambiente de pruebas'
            ],
            [
                'numero_factura' => '001-001-000000003',
                'clave_acceso' => '0306202501179214673900110010010000000031234567892',
                'estado' => 'FIRMADO',
                'ambiente' => '2',
                'fecha_emision' => Carbon::now()->subDays(3),
                'fecha_firmado' => Carbon::now()->subDays(3)->addMinutes(30),
                'xml_ruta' => '/storage/facturas/xml/factura_003.xml',
                'xml_firmado_ruta' => '/storage/facturas/xml_firmado/factura_003_firmado.xml',
                'certificado_propietario' => 'EMPRESA DEMO S.A.',
                'certificado_serial' => '123456789ABCDEF',
                'certificado_vigencia_hasta' => Carbon::now()->addYear(),
                'observaciones' => 'Factura firmada, pendiente de envío al SRI'
            ],
            [
                'numero_factura' => '001-001-000000004',
                'clave_acceso' => '0306202501179214673900110010010000000041234567893',
                'estado' => 'DEVUELTA',
                'ambiente' => '2',
                'fecha_emision' => Carbon::now()->subDays(7),
                'fecha_recepcion' => Carbon::now()->subDays(7)->addHours(1),
                'xml_ruta' => '/storage/facturas/xml/factura_004.xml',
                'xml_firmado_ruta' => '/storage/facturas/xml_firmado/factura_004_firmado.xml',
                'certificado_propietario' => 'EMPRESA DEMO S.A.',
                'certificado_vigencia_hasta' => Carbon::now()->addYear(),
                'observaciones' => 'Factura devuelta por errores en la información tributaria'
            ],
            [
                'numero_factura' => '001-001-000000005',
                'clave_acceso' => '0306202501179214673900110010010000000051234567894',
                'estado' => 'NO_AUTORIZADA',
                'ambiente' => '2',
                'fecha_emision' => Carbon::now()->subDays(15),
                'fecha_recepcion' => Carbon::now()->subDays(15)->addHours(3),
                'xml_ruta' => '/storage/facturas/xml/factura_005.xml',
                'xml_firmado_ruta' => '/storage/facturas/xml_firmado/factura_005_firmado.xml',
                'certificado_propietario' => 'EMPRESA DEMO S.A.',
                'certificado_vigencia_hasta' => Carbon::now()->addYear(),
                'observaciones' => 'Factura no autorizada - problemas con certificado digital'
            ]
        ];

        foreach ($facturas as $facturaData) {
            Factura::create($facturaData);
        }
    }
}
