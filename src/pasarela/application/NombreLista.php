<?php

namespace src\pasarela\application;

use src\actividades\domain\entity\TiposActividades;
use src\pasarela\domain\Nombre;

/**
 * Devuelve el listado del parámetro `nombre` listo para serializar.
 *
 * Estructura: `{excepciones: [{id_tipo_activ, etiqueta, valor}]}`.
 * (El parámetro `nombre` no tiene valor por defecto.)
 */
final class NombreLista
{
    public function __construct(
        private readonly Nombre $nombre,
    ) {
    }

    /**
     * @return array{excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>}
     */
    public function execute(): array
    {
        

        $a_excepciones_raw = $this->nombre->getExcepciones();
        $a_excepciones = [];
        foreach ($a_excepciones_raw as $id_tipo_activ => $valor) {
            $oActividadTipo = new TiposActividades((string)$id_tipo_activ);
            $a_excepciones[] = [
                'id_tipo_activ' => (string)$id_tipo_activ,
                'etiqueta' => $oActividadTipo->getNom(),
                'valor' => (string)$valor,
            ];
        }

        return [
            'excepciones' => $a_excepciones,
        ];
    }
}
