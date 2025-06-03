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
        return view('facturas.index');
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
        $factura->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Factura eliminada exitosamente'
        ]);
    }
}
