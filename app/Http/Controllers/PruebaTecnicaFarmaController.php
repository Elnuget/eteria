<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebaTecnicaFarmaController extends Controller
{
    /**
     * Mostrar la vista principal de la prueba técnica
     */
    public function index()
    {
        return view('prueba-tecnica-farma.index');
    }
}
