<?php

namespace src\pasarela\application;

use src\actividades\domain\entity\TiposActividades;
use src\pasarela\domain\Activacion;

/**
 * Devuelve el listado del parámetro `fecha_activacion` listo para serializar:
 *  - `default`: valor por defecto.
 *  - `excepciones`: array de filas `{id_tipo_activ, etiqueta, valor}`.
 *
 * El frontend renderiza la tabla a partir de estos datos; este caso de uso no
 * genera HTML.
 */
final class ActivacionLista
{
    public function __construct(
        private readonly Activacion $activacion,
    ) {
    }

    /**
     * @return array{default: string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>}
     */
    public function execute(): array
    {
        

        $a_excepciones_raw = $this->activacion->getExcepciones();
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
            'default' => (string)$this->activacion->getDefault(),
            'excepciones' => $a_excepciones,
        ];
    }
}
