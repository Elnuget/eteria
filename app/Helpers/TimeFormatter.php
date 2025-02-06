<?php

namespace App\Helpers;

class TimeFormatter
{
    public static function formatSeconds($seconds)
    {
        if ($seconds < 0) {
            $seconds = abs($seconds);
        }

        if ($seconds < 60) {
            return $seconds . ' segundos';
        }

        if ($seconds < 3600) {
            $minutos = floor($seconds / 60);
            $segundos = $seconds % 60;
            return ($segundos > 0) ? 
                   "{$minutos} minutos y {$segundos} segundos" : 
                   "{$minutos} minutos";
        }

        if ($seconds < 86400) {
            $horas = floor($seconds / 3600);
            $minutos = floor(($seconds % 3600) / 60);
            $segundos = $seconds % 60;
            
            $resultado = "{$horas} horas";
            if ($minutos > 0) $resultado .= ", {$minutos} minutos";
            if ($segundos > 0) $resultado .= " y {$segundos} segundos";
            return $resultado;
        }

        $dias = floor($seconds / 86400);
        $horas = floor(($seconds % 86400) / 3600);
        $minutos = floor(($seconds % 3600) / 60);
        $segundos = $seconds % 60;

        $resultado = "{$dias} días";
        if ($horas > 0) $resultado .= ", {$horas} horas";
        if ($minutos > 0) $resultado .= ", {$minutos} minutos";
        if ($segundos > 0) $resultado .= " y {$segundos} segundos";
        return $resultado;
    }
} 