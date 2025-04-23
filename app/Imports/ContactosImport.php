<?php

namespace App\Imports;

use App\Models\Contacto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\DB;

class ContactosImport implements ToModel, WithHeadingRow, WithValidation, WithMapping, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected $errores;
    protected $importados;
    protected $total;
    protected $duplicados;

    public function __construct()
    {
        $this->errores = collect();
        $this->importados = 0;
        $this->total = 0;
        $this->duplicados = 0;
    }

    /**
     * Mapea las columnas del Excel a los campos del modelo
     */
    public function map($row): array
    {
        $this->total++;
        
        // Buscar la columna que contenga la palabra "numero" o "teléfono" o "telefono"
        $numeroColumn = collect($row)->filter(function($value, $key) {
            return preg_match('/(numero|telefono|teléfono|tel|celular|cel)/i', $key);
        })->keys()->first();

        // Buscar la columna que contenga la palabra "nombre"
        $nombreColumn = collect($row)->filter(function($value, $key) {
            return preg_match('/(nombre|name|contacto)/i', $key);
        })->keys()->first();

        if (!$numeroColumn || empty($row[$numeroColumn])) {
            $this->errores->push("Fila {$this->total}: No se encontró un número válido");
            return [];
        }

        // Limpiar el número de teléfono
        $numero = preg_replace('/[^0-9]/', '', $row[$numeroColumn]);
        
        // Verificar si el número ya existe
        if (Contacto::where('numero', $numero)->exists()) {
            $this->duplicados++;
            $this->errores->push("Fila {$this->total}: El número {$numero} ya existe en la base de datos");
            return [];
        }

        return [
            'numero' => $numero,
            'nombre' => $nombreColumn ? ($row[$nombreColumn] ?? null) : null,
            'estado' => 'por iniciar'
        ];
    }

    /**
     * @param array $row
     */
    public function model(array $row)
    {
        if (empty($row)) return null;

        try {
            $this->importados++;
            return new Contacto($row);
        } catch (\Exception $e) {
            $this->errores->push("Error al importar fila {$this->total}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errores->push("Error en la fila {$failure->row()}: " . implode(', ', $failure->errors()));
        }
    }

    public function getResultados(): array
    {
        return [
            'total' => max(0, $this->total - 1), // Restamos 1 para no contar el encabezado
            'importados' => $this->importados,
            'duplicados' => $this->duplicados,
            'errores' => $this->errores->toArray(),
        ];
    }
} 