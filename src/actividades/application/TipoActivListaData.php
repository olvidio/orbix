<?php

namespace src\actividades\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\criteria\TipoDeActividadField;
use src\actividades\domain\entity\TiposActividades;
use src\shared\domain\criteria\Criteria;

/**
 * Tipos de actividad existentes, por id, para la pantalla de gestión. La tabla la monta
 * `frontend/actividades/controller/tipo_activ_lista.php`.
 */
final class TipoActivListaData
{
    public function __construct(
        private TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
    ) {
    }

    /**
     * @return array{tipos: list<array{id_tipo_activ: int, nom: string}>}
     */
    public function execute(): array
    {
        $cTipos = $this->tipoDeActividadRepository->matching(
            Criteria::create()->orderBy(TipoDeActividadField::IdTipoActiv)
        );

        $tipos = [];
        foreach ($cTipos as $oTipo) {
            $id_tipo_activ = $oTipo->getId_tipo_activ();
            $tipos[] = [
                'id_tipo_activ' => $id_tipo_activ,
                'nom' => (new TiposActividades($id_tipo_activ))->getNom(),
            ];
        }

        return ['tipos' => $tipos];
    }
}
