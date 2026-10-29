<?php

namespace src\planning\application;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;

/**
 * Listado de personas para `planning_persona_select`.
 */
final class PlanningPersonaSelectData
{
    public function __construct(
        private PlanningPersonaRepositoryPicker $personaRepositoryPicker,
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<int, array{id_nom: int|string, id_tabla: string, pref_apellidos_nombre: string, centro_o_dl: string}>
     */
    public function execute(array $input): array
    {
        $Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'obj_pau');
        $Qapellido1 = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'apellido1');
        $Qapellido2 = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'apellido2');
        $Qnombre = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nombre');
        $Qcentro = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'centro');
        $Qna = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'na');

        $aWhere = [
            'situacion' => 'A',
            '_ordre' => 'apellido1,apellido2,nom',
        ];
        $aOperador = [];
        $criterioCtr = null;

        if ($Qapellido1 !== '') {
            $aWhere['apellido1'] = '^' . $Qapellido1;
            $aOperador['apellido1'] = 'sin_acentos';
        }
        if ($Qapellido2 !== '') {
            $aWhere['apellido2'] = '^' . $Qapellido2;
            $aOperador['apellido2'] = 'sin_acentos';
        }
        if ($Qnombre !== '') {
            $aWhere['nom'] = '^' . $Qnombre;
            $aOperador['nom'] = 'sin_acentos';
        }
        if ($Qcentro !== '') {
            $nom_ubi = str_replace('+', '\\+', $Qcentro);
            $criterioCtr = Criteria::create()->where(Filter::matchesUnaccented(CentroDlField::NombreUbi, $nom_ubi));
        }
        if ($Qna !== '') {
            $aWhere['id_tabla'] = 'p' . $Qna;
        }

        $PersonaRepository = $this->personaRepositoryPicker->getSafe($Qobj_pau);

        $cPersonas = [];
        if ($criterioCtr !== null) {
            $cCentros = $this->centroDlRepository->matching($criterioCtr);
            foreach ($cCentros as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $aWhere['id_ctr'] = $id_ubi;
                $cPersonas2 = $PersonaRepository->getPersonas($aWhere, $aOperador);
                if (count($cPersonas2) >= 1) {
                    $cPersonas = array_merge($cPersonas, $cPersonas2);
                }
            }
        } else {
            $cPersonas = $PersonaRepository->getPersonas($aWhere, $aOperador);
        }

        $out = [];
        foreach ($cPersonas as $oPersona) {
            $out[] = [
                'id_nom' => $oPersona->getId_nom(),
                'id_tabla' => $oPersona->getId_tabla(),
                'pref_apellidos_nombre' => $oPersona->getPrefApellidosNombre(),
                'centro_o_dl' => $oPersona->getCentro_o_dl(),
            ];
        }

        return $out;
    }
}
