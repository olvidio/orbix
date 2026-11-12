<?php

namespace src\profesores\domain\services;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\profesores\domain\criteria\ProfesorStgrField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;

class ProfesorStgrService
{
    public function __construct(
        private ProfesorStgrRepositoryInterface $profesorStgrRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private PersonaPubRepositoryInterface $personaPubRepository,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getArrayProfesoresPub(): array
    {
        $cPersonasPub = $this->personaPubRepository->matching(
            Criteria::create()->where(Filter::eq(PersonaField::ProfesorStgr, true))
        );

        $aProfesores = [];
        foreach ($cPersonasPub as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $situacion = $oPersona->getSituacionVo()->value();
            if ($situacion !== 'A') {
                continue;
            }
            $aProfesores[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersona->getPrefApellidosNombre(),
                'ap1' => $oPersona->getApellido1Vo()->value(),
                'ap2' => $oPersona->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersona->getNomVo()?->value() ?? '',
            ];
        }
        \src\shared\domain\helpers\FuncTablasSupport::usortProfesoresPorApellidos($aProfesores);

        return \src\shared\domain\helpers\FuncTablasSupport::profesoresOpcionesFromFilas($aProfesores);
    }

    /**
     * @return array<int, array{id_nom: int, ap_nom: string, dl: string}>
     */
    public function getArrayProfesoresConDl(): array
    {
        $gesProfesores = $this->profesorStgrRepository->matching(Criteria::create()->where(Filter::isNull(ProfesorStgrField::FCese)));
        /** @var list<array{id_nom: int, ap_nom: string, dl: string, ap1: string, ap2: string, nom: string}> $aFilas */
        $aFilas = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $this->personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            $situacion = $oPersonaDl->getSituacionVo()->value();
            if ($situacion !== 'A') {
                continue;
            }
            $aFilas[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersonaDl->getPrefApellidosNombre(),
                'dl' => $oPersonaDl->getDlVo()?->value() ?? '',
                'ap1' => $oPersonaDl->getApellido1Vo()->value(),
                'ap2' => $oPersonaDl->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersonaDl->getNomVo()?->value() ?? '',
            ];
        }
        // Mismo orden que los otros listados de profesores (sin acentos): con
        // array_multisort(SORT_STRING) «Álvarez» quedaba detrás de «Zapata».
        \src\shared\domain\helpers\FuncTablasSupport::usortProfesoresPorApellidos($aFilas);
        /** @var list<array{id_nom: int, ap_nom: string, dl: string, ap1: string, ap2: string, nom: string}> $aFilas */
        $aProfesores = array_map(static fn (array $f): array => ['id_nom' => $f['id_nom'], 'ap_nom' => $f['ap_nom'], 'dl' => $f['dl']], $aFilas);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $aOpciones[$aClave['id_nom']] = $aClave;
        }

        return $aOpciones;
    }

    /**
     * @return array<int, string>
     */
    public function getArrayProfesoresDl(): array
    {
        $gesProfesores = $this->profesorStgrRepository->matching(Criteria::create()->where(Filter::isNull(ProfesorStgrField::FCese)));
        $aFilas = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $this->personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            $situacion = $oPersonaDl->getSituacionVo()->value();
            if ($situacion !== 'A') {
                continue;
            }
            $aFilas[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersonaDl->getPrefApellidosNombre(),
                'ap1' => $oPersonaDl->getApellido1Vo()->value(),
                'ap2' => $oPersonaDl->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersonaDl->getNomVo()?->value() ?? '',
            ];
        }
        \src\shared\domain\helpers\FuncTablasSupport::usortProfesoresPorApellidos($aFilas);

        return \src\shared\domain\helpers\FuncTablasSupport::profesoresOpcionesFromFilas($aFilas);
    }
}
