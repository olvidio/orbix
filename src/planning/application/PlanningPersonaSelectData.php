<?php

namespace src\planning\application;

use src\personas\domain\criteria\PersonaField;
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

        $criterioPersona = Criteria::create()->where(Filter::eq(PersonaField::Situacion, 'A'));
        $criterioCtr = null;

        if ($Qapellido1 !== '') {
            $criterioPersona = $criterioPersona->andWhere(Filter::matchesUnaccented(PersonaField::Apellido1, '^' . $Qapellido1));
        }
        if ($Qapellido2 !== '') {
            $criterioPersona = $criterioPersona->andWhere(Filter::matchesUnaccented(PersonaField::Apellido2, '^' . $Qapellido2));
        }
        if ($Qnombre !== '') {
            $criterioPersona = $criterioPersona->andWhere(Filter::matchesUnaccented(PersonaField::Nom, '^' . $Qnombre));
        }
        if ($Qcentro !== '') {
            $nom_ubi = str_replace('+', '\\+', $Qcentro);
            $criterioCtr = Criteria::create()->where(Filter::matchesUnaccented(CentroDlField::NombreUbi, $nom_ubi));
        }
        if ($Qna !== '') {
            $criterioPersona = $criterioPersona->andWhere(Filter::eq(PersonaField::IdTabla, 'p' . $Qna));
        }
        $criterioPersona = $criterioPersona
            ->orderBy(PersonaField::Apellido1)
            ->addOrderBy(PersonaField::Apellido2)
            ->addOrderBy(PersonaField::Nom);

        $PersonaRepository = $this->personaRepositoryPicker->getSafe($Qobj_pau);

        $cPersonas = [];
        if ($criterioCtr !== null) {
            $cCentros = $this->centroDlRepository->matching($criterioCtr);
            foreach ($cCentros as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $cPersonas2 = $PersonaRepository->matching(
                    $criterioPersona->andWhere(Filter::eq(PersonaField::IdCtr, $id_ubi))
                );
                if (count($cPersonas2) >= 1) {
                    $cPersonas = array_merge($cPersonas, $cPersonas2);
                }
            }
        } else {
            $cPersonas = $PersonaRepository->matching($criterioPersona);
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
