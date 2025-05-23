<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ContactosImport;

class ContactoController extends Controller
{
    /**
     * Mostrar una lista de los contactos.
     */
    public function index()
    {
        $contactos = Contacto::orderBy('created_at', 'desc')->get();
        return view('contactos.index', compact('contactos'));
    }

    /**
     * Almacenar un nuevo contacto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string',
            'nombre' => 'nullable|string|max:255',
            'estado' => 'required|in:iniciado,por iniciar'
        ]);

        $contacto = Contacto::create($request->all());

        return redirect()->route('contactos.index')->with('success', 'Contacto registrado exitosamente');
    }

    /**
     * Actualizar un contacto específico.
     */
    public function update(Request $request, Contacto $contacto)
    {
        $request->validate([
            'numero' => 'required|string',
            'nombre' => 'nullable|string|max:255',
            'estado' => 'required|in:iniciado,por iniciar'
        ]);

        $contacto->update($request->all());

        return redirect()->route('contactos.index')->with('success', 'Contacto actualizado exitosamente');
    }

    /**
     * Eliminar un contacto específico.
     */
    public function destroy(Contacto $contacto)
    {
        $contacto->delete();
        return redirect()->route('contactos.index')->with('success', 'Contacto eliminado exitosamente');
    }

    /**
     * Importar contactos desde archivo Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $import = new ContactosImport;
            $import->import($request->file('excel_file'));
            
            $resultados = $import->getResultados();
            
            $mensaje = "Total de registros procesados: {$resultados['total']}\n";
            $mensaje .= "Contactos importados exitosamente: {$resultados['importados']}\n";
            if ($resultados['duplicados'] > 0) {
                $mensaje .= "Contactos omitidos por duplicados: {$resultados['duplicados']}\n";
            }
            
            if (!empty($resultados['errores'])) {
                $mensaje .= "\nDetalles:\n";
                foreach ($resultados['errores'] as $error) {
                    $mensaje .= "- {$error}\n";
                }
                return redirect()->route('contactos.index')
                    ->with('warning', nl2br($mensaje));
            }
            
            return redirect()->route('contactos.index')
                ->with('success', nl2br($mensaje));
        } catch (\Exception $e) {
            return redirect()->route('contactos.index')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
} 