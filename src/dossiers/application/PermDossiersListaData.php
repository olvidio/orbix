<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\criteria\TipoDossierField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Listado de tipos de dossier para pantalla de permisos.
 * `pagina_link_spec` se firma en `perm_dossiers_data.php`.
 */
class PermDossiersListaData
{
    public function __construct(
        private TipoDossierRepositoryInterface $tipoDossierRepository,
    ) {
    }

    /**
     * @return array{a_filas: list<array<string, mixed>>}
     */
    public function build(string $tipo): array
    {
        $cTipoDossiers = $this->tipoDossierRepository->matching(
            Criteria::create()
                ->where(Filter::eq(TipoDossierField::TablaFrom, $tipo))
                ->orderBy(TipoDossierField::IdTipoDossier)
        );
        $a_filas = [];
        foreach ($cTipoDossiers as $oTipoDossier) {
            $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
            $depende_modificar = $oTipoDossier->isDepende_modificar();
            $descripcion = $oTipoDossier->getDescripcion() ?? '';
            $a_filas[] = [
                'descripcion' => $descripcion,
                'pagina_link_spec' => [
                    'path' => 'frontend/dossiers/controller/perm_dossier_ver.php',
                    'query' => [
                        'id_tipo_dossier' => $id_tipo_dossier,
                        'depende_modificar' => $depende_modificar,
                        'tipo' => $tipo,
                    ],
                ],
            ];
        }
        return ['a_filas' => $a_filas];
    }
}
