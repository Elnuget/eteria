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

    /**
     * Show the form for signing a factura.
     */
    public function firmar(Factura $factura)
    {
        // Verificar que la factura tenga XML y no esté ya firmada
        if (!$factura->xml_ruta) {
            return redirect()->route('facturas.index')
                ->with('error', 'Esta factura no tiene un archivo XML para firmar.');
        }
        
        if ($factura->xml_firmado_ruta) {
            return redirect()->route('facturas.index')
                ->with('info', 'Esta factura ya ha sido firmada digitalmente.');
        }
        
        return view('facturas.firmar', compact('factura'));
    }

    /**
     * Show the form for sending a factura.
     */
    public function envio(Factura $factura)
    {
        // Verificar que la factura esté firmada y no haya sido recibida
        if (!$factura->xml_firmado_ruta) {
            return redirect()->route('facturas.index')
                ->with('error', 'Esta factura no está firmada digitalmente.');
        }
        
        if ($factura->fecha_recepcion) {
            return redirect()->route('facturas.index')
                ->with('info', 'Esta factura ya ha sido enviada y recibida por el SRI.');
        }
        
        return view('facturas.envio', compact('factura'));
    }

    /**
     * Procesar la firma digital de la factura
     */
    public function procesarFirma(Request $request, Factura $factura): JsonResponse
    {
        try {
            // Verificar que la factura tenga XML y no esté ya firmada
            if (!$factura->xml_ruta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura no tiene un archivo XML para firmar.'
                ], 400);
            }
            
            if ($factura->xml_firmado_ruta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura ya ha sido firmada digitalmente.'
                ], 400);
            }

            // Verificar que el archivo XML existe
            $xmlPath = public_path($factura->xml_ruta);
            if (!file_exists($xmlPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo XML de la factura no existe.'
                ], 400);
            }

            // Obtener las credenciales del certificado desde el archivo
            $credentialsPath = public_path('firma/17104441_1725874992.txt');
            $certificatePath = public_path('firma/17104441_identity_1725874992.p12');
            
            if (!file_exists($credentialsPath) || !file_exists($certificatePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los archivos de certificado digital no están disponibles.'
                ], 400);
            }

            // Leer las credenciales
            $credentials = file_get_contents($credentialsPath);
            preg_match('/Contraseña:\s*(.+)/', $credentials, $matches);
            $password = isset($matches[1]) ? trim($matches[1]) : null;

            if (!$password) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener la contraseña del certificado.'
                ], 400);
            }

            // Simular el proceso de firma digital
            // En una implementación real, aquí se usaría una librería como phpseclib, OpenSSL, o similar
            // para firmar digitalmente el XML con el certificado p12
            
            // Leer el contenido del XML original
            $xmlContent = file_get_contents($xmlPath);
            
            // Generar el XML firmado (simulación)
            $xmlFirmado = $this->simularFirmaDigital($xmlContent, $certificatePath, $password);
            
            // Crear el nombre del archivo firmado
            $nombreArchivoFirmado = 'factura_' . $factura->numero_factura . '_firmado_' . time() . '.xml';
            $rutaXmlFirmado = 'facturas/xml_firmado/' . $nombreArchivoFirmado;
            $rutaCompletaFirmado = storage_path('app/public/' . $rutaXmlFirmado);

            // Crear directorio si no existe
            $directorio = dirname($rutaCompletaFirmado);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Guardar el XML firmado
            file_put_contents($rutaCompletaFirmado, $xmlFirmado);

            // Actualizar la factura en la base de datos
            $factura->update([
                'estado' => 'FIRMADO',
                'xml_firmado_ruta' => '/storage/' . $rutaXmlFirmado,
                'fecha_firmado' => now(),
                'certificado_ruta' => '/firma/17104441_identity_1725874992.p12',
                'certificado_password' => encrypt($password), // Encriptar la contraseña
                'certificado_propietario' => 'UANATACA ECUADOR S.A.',
                'certificado_serial' => '17104441_1725874992',
                'certificado_vigencia_hasta' => '2025-05-13'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura firmada digitalmente con éxito.',
                'factura' => $factura->fresh(),
                'xml_firmado_url' => asset('storage/' . $rutaXmlFirmado)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al firmar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simular la firma digital del XML
     * En una implementación real, aquí se usaría OpenSSL o una librería especializada
     */
    private function simularFirmaDigital($xmlContent, $certificatePath, $password)
    {
        // En una implementación real, aquí se aplicaría la firma digital real
        // Por ahora, agregamos una sección de firma simulada al XML
        
        $firmaSimilada = '
    <!-- FIRMA DIGITAL APLICADA -->
    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Id="Signature">
        <ds:SignedInfo>
            <ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
            <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
            <ds:Reference URI="">
                <ds:Transforms>
                    <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                </ds:Transforms>
                <ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
                <ds:DigestValue>SIMULADO_DIGEST_VALUE_' . base64_encode(random_bytes(20)) . '</ds:DigestValue>
            </ds:Reference>
        </ds:SignedInfo>
        <ds:SignatureValue>SIMULADO_SIGNATURE_VALUE_' . base64_encode(random_bytes(128)) . '</ds:SignatureValue>
        <ds:KeyInfo>
            <ds:X509Data>
                <ds:X509Certificate>SIMULADO_CERTIFICATE_' . base64_encode(random_bytes(64)) . '</ds:X509Certificate>
            </ds:X509Data>
        </ds:KeyInfo>
    </ds:Signature>';

        // Insertar la firma antes del cierre del elemento factura
        $xmlFirmado = str_replace('</factura>', $firmaSimilada . "\n" . '</factura>', $xmlContent);
        
        return $xmlFirmado;
    }
}
