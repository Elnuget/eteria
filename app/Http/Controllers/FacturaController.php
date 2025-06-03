<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facturas = Factura::orderBy('created_at', 'desc')->get();
        return view('facturas.index', compact('facturas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'numero_factura' => 'required|string|max:50',
            'clave_acceso' => 'required|string|max:49|unique:facturas',
            'estado' => 'required|in:PENDIENTE,FIRMADO,RECIBIDA,DEVUELTA,AUTORIZADA,NO_AUTORIZADA',
            'ambiente' => 'required|in:1,2',
            'fecha_emision' => 'required|date',
            'xml_ruta' => 'nullable|string',
            'xml_firmado_ruta' => 'nullable|string',
            'pdf_ruta' => 'nullable|string',
            'certificado_ruta' => 'nullable|string',
            'certificado_password' => 'nullable|string',
            'certificado_serial' => 'nullable|string|max:100',
            'certificado_propietario' => 'nullable|string',
            'certificado_vigencia_hasta' => 'nullable|date',
            'fecha_firmado' => 'nullable|date',
            'fecha_recepcion' => 'nullable|date',
            'fecha_autorizacion' => 'nullable|date',
            'numero_autorizacion' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string'
        ]);

        $factura = Factura::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Factura creada exitosamente',
            'factura' => $factura
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Factura $factura): JsonResponse
    {
        return response()->json($factura);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura): JsonResponse
    {
        $validated = $request->validate([
            'numero_factura' => 'required|string|max:50',
            'clave_acceso' => 'required|string|max:49|unique:facturas,clave_acceso,' . $factura->id,
            'estado' => 'required|in:PENDIENTE,FIRMADO,RECIBIDA,DEVUELTA,AUTORIZADA,NO_AUTORIZADA',
            'ambiente' => 'required|in:1,2',
            'fecha_emision' => 'required|date',
            'xml_ruta' => 'nullable|string',
            'xml_firmado_ruta' => 'nullable|string',
            'pdf_ruta' => 'nullable|string',
            'certificado_ruta' => 'nullable|string',
            'certificado_password' => 'nullable|string',
            'certificado_serial' => 'nullable|string|max:100',
            'certificado_propietario' => 'nullable|string',
            'certificado_vigencia_hasta' => 'nullable|date',
            'fecha_firmado' => 'nullable|date',
            'fecha_recepcion' => 'nullable|date',
            'fecha_autorizacion' => 'nullable|date',
            'numero_autorizacion' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string'
        ]);

        $factura->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Factura actualizada exitosamente',
            'factura' => $factura
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factura $factura): JsonResponse
    {
        try {
            // Eliminar archivos asociados si existen
            if ($factura->xml_ruta) {
                $xmlPath = str_replace('/storage/', '', $factura->xml_ruta);
                $fullPath = storage_path('app/public/' . $xmlPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            if ($factura->xml_firmado_ruta) {
                $xmlFirmadoPath = str_replace('/storage/', '', $factura->xml_firmado_ruta);
                $fullPath = storage_path('app/public/' . $xmlFirmadoPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            if ($factura->pdf_ruta) {
                $pdfPath = str_replace('/storage/', '', $factura->pdf_ruta);
                $fullPath = storage_path('app/public/' . $pdfPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            // Eliminar la factura de la base de datos
            $factura->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Factura y archivos asociados eliminados exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for generating XML.
     */
    public function generarxml()
    {
        return view('facturas.generarxml');
    }

    /**
     * Save the XML data to database.
     */
    public function guardarxml(Request $request)
    {
        try {
            $validated = $request->validate([
                'numero_factura' => 'required|string|max:50',
                'clave_acceso' => 'required|string|max:49',
                'ambiente' => 'required|in:1,2',
                'fecha_emision' => 'required|date',
                'xml_ruta' => 'nullable|string',
                // Campos adicionales del formulario
                'razon_social_comprador' => 'nullable|string',
                'identificacion_comprador' => 'nullable|string',
                'total_sin_impuestos' => 'nullable|numeric',
                'total_descuento' => 'nullable|numeric',
                'importe_total' => 'nullable|numeric',
                'descripcion' => 'nullable|string',
                'cantidad' => 'nullable|numeric',
                'precio_unitario' => 'nullable|numeric',
                'telefono' => 'nullable|string',
                'email' => 'nullable|string'
            ]);

            // Generar nombre único para el archivo XML
            $nombreArchivo = 'factura_' . $validated['numero_factura'] . '_' . time() . '.xml';
            $rutaXML = 'facturas/xml/' . $nombreArchivo;
            $rutaCompleta = storage_path('app/public/' . $rutaXML);

            // Generar contenido XML
            $xmlContent = $this->generarContenidoXML($validated);

            // Guardar el archivo XML
            file_put_contents($rutaCompleta, $xmlContent);

            // Preparar los datos según las especificaciones
            $facturaData = [
                'numero_factura' => $validated['numero_factura'], // el secuencial
                'clave_acceso' => $validated['clave_acceso'],
                'estado' => 'PENDIENTE',
                'xml_ruta' => '/storage/' . $rutaXML, // Ruta accesible desde web
                'xml_firmado_ruta' => null,
                'pdf_ruta' => null,
                'ambiente' => $validated['ambiente'],
                'certificado_ruta' => null,
                'certificado_password' => null,
                'certificado_serial' => null,
                'certificado_propietario' => null,
                'certificado_vigencia_hasta' => null,
                'fecha_firmado' => null,
                'fecha_emision' => $validated['fecha_emision'],
                'fecha_recepcion' => null,
                'fecha_autorizacion' => null,
                'numero_autorizacion' => null,
                'observaciones' => null
            ];

            $factura = Factura::create($facturaData);

            return response()->json([
                'success' => true,
                'message' => 'Factura guardada exitosamente con XML generado',
                'factura' => $factura,
                'xml_url' => asset('storage/' . $rutaXML)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar contenido XML de la factura
     */
    private function generarContenidoXML($datos)
    {
        $fecha = date('d/m/Y', strtotime($datos['fecha_emision']));
        $ambiente = $datos['ambiente'] == '2' ? 'PRODUCCIÓN' : 'PRUEBAS';
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<factura id="comprobante" version="2.1.0">' . "\n";
        $xml .= '  <infoTributaria>' . "\n";
        $xml .= '    <ambiente>' . $datos['ambiente'] . '</ambiente>' . "\n";
        $xml .= '    <tipoEmision>1</tipoEmision>' . "\n";
        $xml .= '    <razonSocial>' . env('EMISOR_RAZON_SOCIAL') . '</razonSocial>' . "\n";
        $xml .= '    <nombreComercial>' . env('EMISOR_NOMBRE_COMERCIAL') . '</nombreComercial>' . "\n";
        $xml .= '    <ruc>' . env('EMISOR_RUC') . '</ruc>' . "\n";
        $xml .= '    <claveAcceso>' . $datos['clave_acceso'] . '</claveAcceso>' . "\n";
        $xml .= '    <codDoc>01</codDoc>' . "\n";
        $xml .= '    <estab>' . env('EMISOR_ESTABLECIMIENTO') . '</estab>' . "\n";
        $xml .= '    <ptoEmi>' . env('EMISOR_PUNTO_EMISION') . '</ptoEmi>' . "\n";
        $xml .= '    <secuencial>' . str_pad($datos['numero_factura'], 9, '0', STR_PAD_LEFT) . '</secuencial>' . "\n";
        $xml .= '    <dirMatriz>' . env('EMISOR_DIRECCION') . '</dirMatriz>' . "\n";
        $xml .= '    <regimenGeneral>' . env('EMISOR_REGIMEN_GENERAL', 'RÉGIMEN GENERAL') . '</regimenGeneral>' . "\n";
        $xml .= '    <contribuyenteRimpe>' . env('EMISOR_CONTRIBUYENTE_RIMPE') . '</contribuyenteRimpe>' . "\n";
        $xml .= '  </infoTributaria>' . "\n";
        
        $xml .= '  <infoFactura>' . "\n";
        $xml .= '    <fechaEmision>' . $fecha . '</fechaEmision>' . "\n";
        $xml .= '    <dirEstablecimiento>Calle: E3J Numero: S56-65 Interseccion: S57 P INOCENCIO JACOME</dirEstablecimiento>' . "\n";
        $xml .= '    <obligadoContabilidad>NO</obligadoContabilidad>' . "\n";
        $xml .= '    <tipoIdentificacionComprador>07</tipoIdentificacionComprador>' . "\n";
        $xml .= '    <razonSocialComprador>' . ($datos['razon_social_comprador'] ?? 'CONSUMIDOR FINAL') . '</razonSocialComprador>' . "\n";
        $xml .= '    <identificacionComprador>' . ($datos['identificacion_comprador'] ?? '9999999999') . '</identificacionComprador>' . "\n";
        $xml .= '    <totalSinImpuestos>' . number_format($datos['total_sin_impuestos'] ?? 100.00, 2, '.', '') . '</totalSinImpuestos>' . "\n";
        $xml .= '    <totalDescuento>' . number_format($datos['total_descuento'] ?? 0.00, 2, '.', '') . '</totalDescuento>' . "\n";
        
        // Impuestos
        $baseImponible = ($datos['total_sin_impuestos'] ?? 100.00) - ($datos['total_descuento'] ?? 0.00);
        $valorIva = $baseImponible * 0.15;
        
        $xml .= '    <totalConImpuestos>' . "\n";
        $xml .= '      <totalImpuesto>' . "\n";
        $xml .= '        <codigo>2</codigo>' . "\n";
        $xml .= '        <codigoPorcentaje>4</codigoPorcentaje>' . "\n";
        $xml .= '        <baseImponible>' . number_format($baseImponible, 2, '.', '') . '</baseImponible>' . "\n";
        $xml .= '        <valor>' . number_format($valorIva, 2, '.', '') . '</valor>' . "\n";
        $xml .= '      </totalImpuesto>' . "\n";
        $xml .= '    </totalConImpuestos>' . "\n";
        
        $xml .= '    <propina>0.00</propina>' . "\n";
        $xml .= '    <importeTotal>' . number_format($datos['importe_total'] ?? ($baseImponible + $valorIva), 2, '.', '') . '</importeTotal>' . "\n";
        $xml .= '    <moneda>DOLAR</moneda>' . "\n";
        
        // Pagos
        $xml .= '    <pagos>' . "\n";
        $xml .= '      <pago>' . "\n";
        $xml .= '        <formaPago>01</formaPago>' . "\n";
        $xml .= '        <total>' . number_format($datos['importe_total'] ?? ($baseImponible + $valorIva), 2, '.', '') . '</total>' . "\n";
        $xml .= '      </pago>' . "\n";
        $xml .= '    </pagos>' . "\n";
        $xml .= '  </infoFactura>' . "\n";
        
        // Detalles
        $xml .= '  <detalles>' . "\n";
        $xml .= '    <detalle>' . "\n";
        $xml .= '      <codigoPrincipal>PRODGENERICO</codigoPrincipal>' . "\n";
        $xml .= '      <descripcion>' . ($datos['descripcion'] ?? 'SERVICIO GENERAL') . '</descripcion>' . "\n";
        $xml .= '      <cantidad>' . number_format($datos['cantidad'] ?? 1.00, 2, '.', '') . '</cantidad>' . "\n";
        $xml .= '      <precioUnitario>' . number_format($datos['precio_unitario'] ?? 100.00, 2, '.', '') . '</precioUnitario>' . "\n";
        $xml .= '      <descuento>0.00</descuento>' . "\n";
        $xml .= '      <precioTotalSinImpuesto>' . number_format($datos['total_sin_impuestos'] ?? 100.00, 2, '.', '') . '</precioTotalSinImpuesto>' . "\n";
        
        $xml .= '      <impuestos>' . "\n";
        $xml .= '        <impuesto>' . "\n";
        $xml .= '          <codigo>2</codigo>' . "\n";
        $xml .= '          <codigoPorcentaje>4</codigoPorcentaje>' . "\n";
        $xml .= '          <tarifa>15.00</tarifa>' . "\n";
        $xml .= '          <baseImponible>' . number_format($datos['total_sin_impuestos'] ?? 100.00, 2, '.', '') . '</baseImponible>' . "\n";
        $xml .= '          <valor>' . number_format($valorIva, 2, '.', '') . '</valor>' . "\n";
        $xml .= '        </impuesto>' . "\n";
        $xml .= '      </impuestos>' . "\n";
        $xml .= '    </detalle>' . "\n";
        $xml .= '  </detalles>' . "\n";
        
        // Información adicional
        if (!empty($datos['telefono']) || !empty($datos['email'])) {
            $xml .= '  <infoAdicional>' . "\n";
            if (!empty($datos['telefono'])) {
                $xml .= '    <campoAdicional nombre="Telefono">' . $datos['telefono'] . '</campoAdicional>' . "\n";
            }
            if (!empty($datos['email'])) {
                $xml .= '    <campoAdicional nombre="Email">' . $datos['email'] . '</campoAdicional>' . "\n";
            }
            $xml .= '  </infoAdicional>' . "\n";
        }
        
        $xml .= '</factura>';
        
        return $xml;
    }
}
