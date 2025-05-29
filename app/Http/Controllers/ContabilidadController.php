<?php

namespace App\Http\Controllers;

use App\Models\Contabilidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContabilidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contabilidad::with('usuario');
        
        // Filtros por mes y año
        $mes = $request->get('mes', now()->month);
        $anio = $request->get('anio', now()->year);
        
        // Aplicar filtros de fecha al mes y año seleccionados
        if ($mes && $anio) {
            $query->whereMonth('fecha', $mes)
                  ->whereYear('fecha', $anio);
        }
        
        $contabilidad = $query->orderBy('fecha', 'desc')->paginate(15);

        // Leer archivos JSON de compras y ventas
        $compras = $this->readJsonFile(resource_path('views/contabilidad/compra/compras.txt'));
        $ventas = $this->readJsonFile(resource_path('views/contabilidad/venta/ventas.txt'));
        
        // Filtrar compras y ventas por mes y año seleccionados
        $compras = $this->filterDataByDate($compras, $mes, $anio);
        $ventas = $this->filterDataByDate($ventas, $mes, $anio);

        return view('contabilidad.index', compact('contabilidad', 'compras', 'ventas', 'mes', 'anio'));
    }

    /**
     * Leer y decodificar archivo JSON
     */
    private function readJsonFile($filePath)
    {
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $data = json_decode($content, true);
            return $data ?? [];
        }
        return [];
    }

    /**
     * Filtrar datos de compras/ventas por mes y año
     */
    private function filterDataByDate($data, $mes, $anio)
    {
        if (!isset($data['compras']) && !isset($data['ventas'])) {
            return $data;
        }

        // Determinar la clave principal (compras o ventas)
        $key = isset($data['compras']) ? 'compras' : 'ventas';
        
        if (!isset($data[$key])) {
            return $data;
        }

        $filteredData = [];
        foreach ($data[$key] as $item) {
            if (isset($item['invoice_date'])) {
                try {
                    // Parsear la fecha en formato d/m/Y
                    $itemDate = \Carbon\Carbon::createFromFormat('d/m/Y', $item['invoice_date']);
                    
                    // Verificar si coincide con el mes y año
                    if ($itemDate->month == $mes && $itemDate->year == $anio) {
                        $filteredData[] = $item;
                    }
                } catch (\Exception $e) {
                    // Si hay error en el formato de fecha, incluir el item
                    continue;
                }
            }
        }

        return [$key => $filteredData];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        return view('contabilidad.create', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'usuario_id' => 'required|exists:users,id'
        ]);

        Contabilidad::create($request->all());

        return redirect()->route('contabilidad.index')
            ->with('success', 'Registro de contabilidad creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contabilidad $contabilidad)
    {
        $contabilidad->load('usuario');
        return view('contabilidad.show', compact('contabilidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contabilidad $contabilidad)
    {
        $usuarios = User::orderBy('name')->get();
        return view('contabilidad.edit', compact('contabilidad', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contabilidad $contabilidad)
    {
        $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'usuario_id' => 'required|exists:users,id'
        ]);

        $contabilidad->update($request->all());

        return redirect()->route('contabilidad.index')
            ->with('success', 'Registro de contabilidad actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contabilidad $contabilidad)
    {
        $contabilidad->delete();

        return redirect()->route('contabilidad.index')
            ->with('success', 'Registro de contabilidad eliminado exitosamente.');
    }
}
