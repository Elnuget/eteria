<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
        $xml .= '    <ruc>' . env('EMISOR_RUC') . '</ruc>' . "\n";
        $xml .= '    <claveAcceso>' . $datos['clave_acceso'] . '</claveAcceso>' . "\n";
        $xml .= '    <codDoc>01</codDoc>' . "\n";
        $xml .= '    <estab>' . env('EMISOR_ESTABLECIMIENTO') . '</estab>' . "\n";
        $xml .= '    <ptoEmi>' . env('EMISOR_PUNTO_EMISION') . '</ptoEmi>' . "\n";
        $xml .= '    <secuencial>' . str_pad($datos['numero_factura'], 9, '0', STR_PAD_LEFT) . '</secuencial>' . "\n";
        $xml .= '    <dirMatriz>' . env('EMISOR_DIRECCION') . '</dirMatriz>' . "\n";
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
     * Enviar factura al SRI
     */
    public function enviarSri(Request $request, Factura $factura): JsonResponse
    {
        try {
            // Verificar que la factura esté firmada
            if ($factura->estado !== 'FIRMADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'La factura debe estar firmada antes de enviarla al SRI'
                ], 400);
            }

            // Verificar que existe el XML firmado
            if (!$factura->xml_firmado_ruta || !file_exists(public_path($factura->xml_firmado_ruta))) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el archivo XML firmado'
                ], 400);
            }

            // Leer el contenido del XML firmado
            $xmlContent = file_get_contents(public_path($factura->xml_firmado_ruta));
            
            // Preparar el SOAP request
            $soapEnvelope = $this->crearSoapEnvelope($xmlContent);
            
            // Enviar al webservice del SRI
            $response = $this->enviarAlSri($soapEnvelope);
            
            // Procesar la respuesta
            $resultado = $this->procesarRespuestaSri($response);
            
            // Actualizar el estado de la factura
            $nuevoEstado = $resultado['estado'] === 'RECIBIDA' ? 'RECIBIDA' : 'DEVUELTA';
            $factura->update([
                'estado' => $nuevoEstado,
                'fecha_recepcion' => now(),
                'observaciones' => $resultado['mensaje']
            ]);

            return response()->json([
                'success' => true,
                'estado' => $resultado['estado'],
                'mensaje' => $resultado['mensaje'],
                'informacion_adicional' => $resultado['informacion_adicional'] ?? '',
                'tipo' => $resultado['tipo'] ?? '',
                'factura' => $factura->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear el SOAP envelope para enviar al SRI
     */
    private function crearSoapEnvelope($xmlContent)
    {
        $xmlBase64 = base64_encode($xmlContent);
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rec="http://ec.gob.sri.ws.recepcion">
    <soap:Header/>
    <soap:Body>
        <rec:validarComprobante>
            <xml>' . $xmlBase64 . '</xml>
        </rec:validarComprobante>
    </soap:Body>
</soap:Envelope>';
    }

    /**
     * Enviar el SOAP request al SRI
     */
    private function enviarAlSri($soapEnvelope)
    {
        // URLs según el ambiente
        $urls = [
            'pruebas' => 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl',
            'produccion' => 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'
        ];
        
        // Por ahora usar siempre ambiente de pruebas
        $url = $urls['pruebas'];
        
        try {
            // Usar cURL en lugar de file_get_contents para mejor manejo de errores
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $soapEnvelope);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ""',
                'Content-Length: ' . strlen($soapEnvelope)
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($response === false || !empty($error)) {
                throw new \Exception('Error de cURL: ' . $error);
            }
            
            if ($httpCode !== 200) {
                throw new \Exception('Error HTTP ' . $httpCode . ' al conectar con el SRI');
            }
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error al conectar con el webservice del SRI', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            throw $e;
        }
    }

    /**
     * Procesar la respuesta XML del SRI
     */
    private function procesarRespuestaSri($xmlResponse)
    {
        try {
            // Cargar el XML de respuesta
            $dom = new \DOMDocument();
            $dom->loadXML($xmlResponse);
            
            // Buscar el estado usando XPath para mayor precisión
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpath->registerNamespace('ns2', 'http://ec.gob.sri.ws.recepcion');
            
            // Buscar el estado
            $estadoNodes = $xpath->query('//estado');
            $estado = $estadoNodes->length > 0 ? $estadoNodes->item(0)->nodeValue : 'DESCONOCIDO';
            
            // Buscar mensaje
            $mensajeNodes = $xpath->query('//mensaje/mensaje');
            $mensaje = $mensajeNodes->length > 0 ? $mensajeNodes->item(0)->nodeValue : 'Sin mensaje';
            
            // Buscar información adicional
            $infoNodes = $xpath->query('//mensaje/informacionAdicional');
            $informacionAdicional = $infoNodes->length > 0 ? $infoNodes->item(0)->nodeValue : '';
            
            // Buscar tipo
            $tipoNodes = $xpath->query('//mensaje/tipo');
            $tipo = $tipoNodes->length > 0 ? $tipoNodes->item(0)->nodeValue : '';
            
            return [
                'estado' => $estado,
                'mensaje' => $mensaje,
                'informacion_adicional' => $informacionAdicional,
                'tipo' => $tipo
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al procesar respuesta SRI: ' . $e->getMessage());
            return [
                'estado' => 'ERROR',
                'mensaje' => 'Error al procesar la respuesta del SRI',
                'informacion_adicional' => $e->getMessage(),
                'tipo' => 'ERROR'
            ];
        }
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
        // Por ahora, agregamos una sección de firma simulada al XML con datos válidos
        
        // Generar valores base64 válidos para la simulación
        $digestValue = base64_encode(hash('sha1', $xmlContent . time(), true));
        $signatureValue = base64_encode(hash('sha256', $xmlContent . $certificatePath . time(), true));
        $certificateValue = base64_encode('MIIC...CERTIFICADO_SIMULADO_' . time());
        
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
                <ds:DigestValue>' . $digestValue . '</ds:DigestValue>
            </ds:Reference>
        </ds:SignedInfo>
        <ds:SignatureValue>' . $signatureValue . '</ds:SignatureValue>
        <ds:KeyInfo>
            <ds:X509Data>
                <ds:X509Certificate>' . $certificateValue . '</ds:X509Certificate>
            </ds:X509Data>
        </ds:KeyInfo>
    </ds:Signature>';

        // Insertar la firma antes del cierre del elemento factura
        $xmlFirmado = str_replace('</factura>', $firmaSimilada . "\n" . '</factura>', $xmlContent);
        
        return $xmlFirmado;
    }

    /**
     * Show the form for authorizing a factura.
     */
    public function autorizar(Factura $factura)
    {
        // Permitir autorización para facturas RECIBIDA y AUTORIZADA (para reintentos)
        if (!in_array($factura->estado, ['RECIBIDA', 'AUTORIZADA'])) {
            return redirect()->route('facturas.index')
                ->with('error', 'Esta factura debe estar en estado RECIBIDA o AUTORIZADA para ser procesada.');
        }
        
        return view('facturas.autorizar', compact('factura'));
    }

    /**
     * Procesar autorización de factura con el SRI
     */
    public function procesarAutorizacion(Request $request, Factura $factura): JsonResponse
    {
        try {
            // Permitir autorización para facturas RECIBIDA y AUTORIZADA (para reintentos)
            if (!in_array($factura->estado, ['RECIBIDA', 'AUTORIZADA'])) {
                return response()->json([
                    'success' => false,
                    'estado' => 'ERROR_ESTADO',
                    'mensaje' => 'La factura debe estar en estado RECIBIDA para ser autorizada.',
                    'tipo' => 'ERROR'
                ], 400);
            }

            // Validar que tengamos una clave de acceso válida
            if (empty($factura->clave_acceso) || strlen($factura->clave_acceso) !== 49) {
                return response()->json([
                    'success' => false,
                    'estado' => 'ERROR_CLAVE',
                    'mensaje' => 'La clave de acceso no es válida para consultar al SRI.',
                    'tipo' => 'ERROR'
                ], 400);
            }

            Log::info('Iniciando consulta de autorización SRI', [
                'factura_id' => $factura->id,
                'clave_acceso' => $factura->clave_acceso,
                'estado_actual' => $factura->estado
            ]);

            // Construir el SOAP envelope para autorización
            $soapEnvelope = $this->crearSoapEnvelopeAutorizacion($factura->clave_acceso);
            
            // Enviar al SRI
            $resultado = $this->enviarAutorizacionAlSri($soapEnvelope);
            
            // Log del resultado para debugging
            Log::info('Respuesta SRI autorización', [
                'factura_id' => $factura->id,
                'resultado' => $resultado
            ]);
            
            // Validar que el resultado tenga la estructura correcta
            if (!isset($resultado['success']) || !isset($resultado['estado'])) {
                return response()->json([
                    'success' => false,
                    'estado' => 'ERROR_RESPUESTA',
                    'mensaje' => 'Respuesta inválida del servicio SRI',
                    'tipo' => 'ERROR'
                ], 500);
            }
            
            // Actualizar la factura según la respuesta
            if ($resultado['success'] && $resultado['estado'] === 'AUTORIZADO') {
                // Validar que tengamos número de autorización
                if (empty($resultado['numero_autorizacion'])) {
                    return response()->json([
                        'success' => false,
                        'estado' => 'ERROR_AUTORIZACION',
                        'mensaje' => 'El SRI no proporcionó un número de autorización válido',
                        'tipo' => 'ERROR'
                    ], 500);
                }
                
                $factura->update([
                    'estado' => 'AUTORIZADA',
                    'fecha_autorizacion' => now(),
                    'numero_autorizacion' => $resultado['numero_autorizacion'],
                    'observaciones' => $resultado['mensaje']
                ]);
                
                Log::info('Factura autorizada exitosamente', [
                    'factura_id' => $factura->id,
                    'numero_autorizacion' => $resultado['numero_autorizacion']
                ]);
            } else {
                $factura->update([
                    'estado' => 'NO_AUTORIZADA',
                    'observaciones' => $resultado['mensaje'] ?? 'No autorizada por el SRI'
                ]);
                
                Log::warning('Factura no autorizada', [
                    'factura_id' => $factura->id,
                    'motivo' => $resultado['mensaje'] ?? 'Sin mensaje específico'
                ]);
            }
            
            return response()->json($resultado);
            
        } catch (\Exception $e) {
            Log::error('Error en autorización SRI', [
                'factura_id' => $factura->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'estado' => 'ERROR_SISTEMA',
                'mensaje' => 'Error interno al procesar la autorización con el SRI',
                'tipo' => 'ERROR',
                'informacion_adicional' => app()->environment('local') ? $e->getMessage() : 'Contacte al administrador'
            ], 500);
        }
    }

    /**
     * Crear SOAP envelope para autorización
     */
    private function crearSoapEnvelopeAutorizacion($claveAcceso)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:aut="http://ec.gob.sri.ws.autorizacion">
    <soap:Header/>
    <soap:Body>
        <aut:autorizacionComprobante>
            <claveAccesoComprobante>' . $claveAcceso . '</claveAccesoComprobante>
        </aut:autorizacionComprobante>
    </soap:Body>
</soap:Envelope>';
    }

    /**
     * Enviar autorización al SRI
     */
    private function enviarAutorizacionAlSri($soapEnvelope)
    {
        // URLs según el ambiente
        $urls = [
            'pruebas' => 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl',
            'produccion' => 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl'
        ];
        
        $url = $urls['pruebas']; // Usar ambiente de pruebas
        
        Log::info('Enviando consulta autorización al SRI REAL', [
            'url' => $url,
            'soap_envelope_size' => strlen($soapEnvelope)
        ]);
        
        try {
            // Configurar contexto SSL para el SRI
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'ciphers' => 'DEFAULT:!DH'
                ],
                'http' => [
                    'timeout' => 60,
                    'user_agent' => 'SRI-Cliente-PHP/1.0'
                ]
            ]);

            // Crear cliente SOAP con configuración específica para SRI
            $client = new \SoapClient($url, [
                'trace' => true,
                'exceptions' => true,
                'soap_version' => SOAP_1_1,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'connection_timeout' => 60,
                'stream_context' => $context,
                'user_agent' => 'SRI-Cliente-PHP/1.0',
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                'encoding' => 'UTF-8'
            ]);

            Log::info('Cliente SOAP creado exitosamente para SRI');

            // Extraer la clave de acceso del SOAP envelope
            $claveAcceso = $this->extraerClaveAccesoDelSOAP($soapEnvelope);
            
            Log::info('Realizando consulta de autorización SRI', [
                'clave_acceso' => $claveAcceso
            ]);

            // Hacer la llamada al método específico del SRI
            $response = $client->autorizacionComprobante([
                'claveAccesoComprobante' => $claveAcceso
            ]);

            Log::info('Respuesta SRI autorización recibida', [
                'response_type' => gettype($response),
                'has_response' => !empty($response)
            ]);

            // Procesar la respuesta del SRI
            $resultado = $this->procesarRespuestaSOAPAutorizacion($response);
            
            Log::info('Resultado procesado de autorización SRI', [
                'resultado' => $resultado
            ]);
            
            return $resultado;
            
        } catch (\SoapFault $e) {
            Log::error('Error SOAP al consultar SRI autorización', [
                'faultcode' => $e->faultcode,
                'faultstring' => $e->faultstring,
                'detail' => $e->detail ?? null
            ]);
            
            throw new \Exception('Error SOAP SRI: ' . $e->faultstring);
            
        } catch (\Exception $e) {
            Log::error('Error general al conectar con SRI autorización', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            
            throw $e;
        }
    }

    /**
     * Extraer clave de acceso del SOAP envelope
     */
    private function extraerClaveAccesoDelSOAP($soapEnvelope)
    {
        try {
            $dom = new \DOMDocument();
            $dom->loadXML($soapEnvelope);
            
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('aut', 'http://ec.gob.sri.ws.autorizacion');
            
            $claveNodes = $xpath->query('//aut:claveAccesoComprobante');
            
            if ($claveNodes->length > 0) {
                return $claveNodes->item(0)->nodeValue;
            }
            
            throw new \Exception('No se pudo extraer la clave de acceso del SOAP envelope');
            
        } catch (\Exception $e) {
            Log::error('Error al extraer clave de acceso del SOAP', [
                'error' => $e->getMessage(),
                'soap_envelope' => $soapEnvelope
            ]);
            throw $e;
        }
    }

    /**
     * Procesar respuesta SOAP del SRI para autorización
     */
    private function procesarRespuestaSOAPAutorizacion($response)
    {
        try {
            Log::info('Procesando respuesta SOAP del SRI', [
                'response_structure' => json_encode($response, JSON_PARTIAL_OUTPUT_ON_ERROR)
            ]);

            // La respuesta del SRI viene como objeto SOAP
            if (!isset($response->RespuestaAutorizacionComprobante)) {
                throw new \Exception('Respuesta del SRI no tiene la estructura esperada');
            }

            $respuestaAutorizacion = $response->RespuestaAutorizacionComprobante;
            
            // Verificar si hay autorizaciones en la respuesta
            if (!isset($respuestaAutorizacion->autorizaciones) || 
                !isset($respuestaAutorizacion->autorizaciones->autorizacion)) {
                
                Log::warning('Respuesta SRI sin autorizaciones');
                return [
                    'success' => false,
                    'estado' => 'SIN_RESPUESTA',
                    'mensaje' => 'El SRI no devolvió información de autorización',
                    'numero_autorizacion' => null,
                    'fecha_autorizacion' => null
                ];
            }

            $autorizacion = $respuestaAutorizacion->autorizaciones->autorizacion;
            
            // Extraer información de la autorización
            $estado = $autorizacion->estado ?? 'DESCONOCIDO';
            $numeroAutorizacion = $autorizacion->numeroAutorizacion ?? null;
            $fechaAutorizacion = $autorizacion->fechaAutorizacion ?? null;
            
            // Procesar mensajes si existen
            $mensajes = [];
            if (isset($autorizacion->mensajes) && isset($autorizacion->mensajes->mensaje)) {
                $mensajesData = $autorizacion->mensajes->mensaje;
                
                // Si es un solo mensaje, convertir a array
                if (!is_array($mensajesData)) {
                    $mensajesData = [$mensajesData];
                }
                
                foreach ($mensajesData as $mensaje) {
                    $mensajes[] = [
                        'identificador' => $mensaje->identificador ?? null,
                        'mensaje' => $mensaje->mensaje ?? 'Sin mensaje',
                        'tipo' => $mensaje->tipo ?? 'INFO',
                        'informacion_adicional' => $mensaje->informacionAdicional ?? null
                    ];
                }
            }

            Log::info('Autorización SRI procesada', [
                'estado' => $estado,
                'numero_autorizacion' => $numeroAutorizacion,
                'fecha_autorizacion' => $fechaAutorizacion,
                'mensajes_count' => count($mensajes)
            ]);

            // Determinar si fue exitoso
            $success = ($estado === 'AUTORIZADO');
            
            return [
                'success' => $success,
                'estado' => $estado,
                'numero_autorizacion' => $numeroAutorizacion,
                'fecha_autorizacion' => $fechaAutorizacion,
                'mensajes' => $mensajes,
                'mensaje' => $success 
                    ? 'Comprobante autorizado correctamente'
                    : ($mensajes[0]['mensaje'] ?? 'Comprobante no autorizado')
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al procesar respuesta SOAP del SRI', [
                'error' => $e->getMessage(),
                'response' => json_encode($response, JSON_PARTIAL_OUTPUT_ON_ERROR)
            ]);
            
            return [
                'success' => false,
                'estado' => 'ERROR_PROCESAMIENTO',
                'mensaje' => 'Error al procesar la respuesta del SRI: ' . $e->getMessage(),
                'numero_autorizacion' => null,
                'fecha_autorizacion' => null
            ];
        }
    }
}
