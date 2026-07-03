<?php

declare(strict_types=1);

namespace src\notas\application;

use src\shared\domain\helpers\FuncTablasSupport;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\value_objects\CursoStgr;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Tabla de `asig_faltan_personas_select`.
 */
final class AsigFaltanPersonasSelectTablaData
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PersonaFinderService $personaFinderService,
        private readonly TelecoPersonaService $telecoPersonaService,
        private readonly CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }
    /**
     * @param array{id_asignatura:int, personas_n:string, personas_agd:string, b_c:string, c1:string, c2:string} $in
     * @return array{titulo:string, obj_pau:string, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, telfs:string, mails:string}>}
     */

    public function execute(array $in): array
    {
        $Qid_asignatura = FuncTablasSupport::inputInt($in, 'id_asignatura', 0);
        $Qpersonas_n = FuncTablasSupport::inputString($in, 'personas_n');
        $Qpersonas_agd = FuncTablasSupport::inputString($in, 'personas_agd');
        $Qb_c = FuncTablasSupport::inputString($in, 'b_c');
        $Qc1 = FuncTablasSupport::inputString($in, 'c1');
        $Qc2 = FuncTablasSupport::inputString($in, 'c2');

        $isTrue = static function (string $v): bool {
            return $v === '1' || $v === 'true' || $v === 'on' || $v === 't';
        };

        if (!$isTrue($Qpersonas_n) && !$isTrue($Qpersonas_agd)) {
            throw new \RuntimeException(_('Debe marcar un grupo de personas (n o agd)'));
        }

        if ($Qb_c === 'b') {
            $curso = CursoStgr::BIENIO;
            $curso_txt = 'bienio';
        } else {
            $c1 = $isTrue($Qc1);
            $c2 = $isTrue($Qc2);
            if ($Qc1 === '' && $Qc2 === '') {
                $c1 = true;
                $c2 = true;
            }
            if ($c1 && $c2) {
                $curso = CursoStgr::CUADRIENIO;
                $curso_txt = 'cuadrienio';
            } elseif ($c2) {
                $curso = CursoStgr::C2;
                $curso_txt = 'cuadrienio años II-IV';
            } else {
                $curso = CursoStgr::C1;
                $curso_txt = 'cuadrienio año I';
            }
        }

        $personas = '';
        $gente = '';
        $obj_pau = '';
        if ($isTrue($Qpersonas_n)) {
            $personas = 'p_numerarios';
            $gente = 'numerarios';
            $obj_pau = 'PersonaN';
        }
        if ($isTrue($Qpersonas_agd)) {
            $personas = 'p_agregados';
            $gente = 'agregados';
            $obj_pau = 'PersonaAgd';
        }
        if ($isTrue($Qpersonas_n) && $isTrue($Qpersonas_agd)) {
            $personas = 'personas_dl';
            $gente = 'numerarios y agregados';
            $obj_pau = 'PersonaDl';
        }

        $AsignaturaRepository = $this->asignaturaRepository;
        $oAsignatura = $AsignaturaRepository->findById($Qid_asignatura);
        if ($oAsignatura === null) {
            throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), $Qid_asignatura));
        }
        $nom_asignatura = $oAsignatura->getNombre_corto();
        $id_tipo_asignatura = (int) ($oAsignatura->getId_tipo() ?? 0);

        $Pendientes = new AsignaturasPendientes($this->asignaturaRepository, $personas);
        $aId_nom = $Pendientes->personasQueLesFaltaAsignatura($Qid_asignatura, $curso, $id_tipo_asignatura);

        $PersonaFinderService = $this->personaFinderService;
        $telecoService = $this->telecoPersonaService;
        $CentroDlRepository = $this->centroDlRepository;
        $ambito = ConfigGlobal::mi_ambito();

        $rows = [];
        $i = 0;
        foreach ($aId_nom as $id_nom => $aAsignaturas) {
            $id_nom = (int)$id_nom;
            $oPersona = $PersonaFinderService->findPersonaEnDl($id_nom);
            if ($oPersona === null) {
                continue;
            }
            $i++;
            $id_tabla = $oPersona->getId_tabla();
            $stgr = (string) ($oPersona->getNivel_stgr() ?? '');
            $nom = $oPersona->getPrefApellidosNombre();
            if ($ambito === 'rstgr') {
                $nombre_ubi = (string) ($oPersona->getDl() ?? '');
            } else {
                $id_ctr = $oPersona->getId_ctr();
                if ($id_ctr === null) {
                    continue;
                }
                $oCentroDl = $CentroDlRepository->findById($id_ctr);
                $nombre_ubi = $oCentroDl?->getNombre_ubi() ?? '';
            }

            $telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, 'telf', ' / ', '*', false);
            $telfs_movil = $telecoService->getTelecosPorTipo($id_nom, 'móvil', ' / ', '*', false);
            if ($telfs_fijo !== '' && $telfs_movil !== '') {
                $telfs = $telfs_fijo . ' / ' . $telfs_movil;
            } else {
                $telfs = $telfs_fijo . $telfs_movil;
            }
            $mails = $telecoService->getTelecosPorTipo($id_nom, 'e-mail', ' / ', '*', false);

            $rows[] = [
                'id_nom' => $id_nom,
                'id_tabla' => $id_tabla,
                'nom' => $nom,
                'nombre_ubi' => $nombre_ubi,
                'stgr' => $stgr,
                'telfs' => $telfs,
                'mails' => $mails,
            ];
        }

        $titulo = sprintf(_('lista de %s de %s a los que falta la asignatura %s (%s)'), $gente, $curso_txt, $nom_asignatura, $i);

        return [
            'titulo' => $titulo,
            'obj_pau' => $obj_pau,
            'rows' => $rows,
        ];
    }
}
