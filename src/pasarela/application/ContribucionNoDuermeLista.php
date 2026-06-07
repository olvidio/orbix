<?php

namespace src\pasarela\application;

use src\actividades\domain\entity\TiposActividades;
use src\pasarela\domain\ContribucionNoDuerme;

/**
 * Devuelve el listado del parámetro `contribucion_no_duerme` listo para serializar.
 *
 * Estructura: `{default, excepciones: [{id_tipo_activ, etiqueta, valor}]}`.
 */
final class ContribucionNoDuermeLista
{
    public function __construct(
        private readonly ContribucionNoDuerme $contribucionNoDuerme,
    ) {
    }

    /**
     * @return array{default: string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>}
     */
    public function execute(): array
    {
        

        $a_excepciones_raw = $this->contribucionNoDuerme->getExcepciones();
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
            'default' => (string)$this->contribucionNoDuerme->getDefault(),
            'excepciones' => $a_excepciones,
        ];
    }
}
