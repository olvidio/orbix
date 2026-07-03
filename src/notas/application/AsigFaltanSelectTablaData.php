<?php

declare(strict_types=1);

namespace src\notas\application;


use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\value_objects\CursoStgr;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\services\TelecoPersonaService;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;

/**
 * Tabla de `asig_faltan_select` (asignaturas pendientes por persona).
 */
final class AsigFaltanSelectTablaData
{

    public function __construct(
        private readonly TelecoPersonaService $telecoPersonaService,
        private readonly CentroDlRepositoryInterface $centroDlRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
        private readonly PersonaNRepositoryInterface $personaNRepository,
        private readonly PersonaAgdRepositoryInterface $personaAgdRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }
    /**
     * @param array<string, mixed> $in
     * @return array{titulo:string, obj_pau:string, lista:bool, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, asig_txt:string|int, telfs:string, mails:string}>}
     */
    public function execute(array $in): array
    {
        $Qnumero = \src\shared\domain\helpers\FuncTablasSupport::inputInt($in, 'numero');
        $Qb_c = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'b_c');
        $Qc1 = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'c1');
        $Qc2 = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'c2');
        $Qpersonas_n = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'personas_n');
        $Qpersonas_agd = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'personas_agd');
        $Qlista = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'lista');

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

        $lista = $isTrue($Qlista);
        $Pendientes = new AsignaturasPendientes($this->asignaturaRepository, $personas);
        $aId_nom = $lista
            ? $Pendientes->listarFaltantesPorPersona($Qnumero, $curso)
            : $Pendientes->contarFaltantesPorPersona($Qnumero, $curso);

        $titulo = sprintf(
            _('lista de %s a los que faltan %d o menos asignaturas para finalizar el %s'),
            $gente,
            $Qnumero,
            $curso_txt
        );

        $repoPersona = $this->personaRepository($obj_pau);
        $a_NivelStgr = NivelStgrId::getArrayNivelStgr();
        $telecoService = $this->telecoPersonaService;
        $CentroDlRepository = $this->centroDlRepository;
        $ambito = ConfigGlobal::mi_ambito();

        $rows = [];
        foreach ($aId_nom as $id_nom => $aAsignaturas) {
            $id_nom = (int)$id_nom;
            $oPersona = $repoPersona->findById($id_nom);
            if ($oPersona === null) {
                continue;
            }
            $id_tabla = $oPersona->getId_tabla();
            $nom = $oPersona->getPrefApellidosNombre();
            $nivelStgrVo = $oPersona->getNivelStgrVo();
            $stgr = $nivelStgrVo !== null ? ($a_NivelStgr[$nivelStgrVo->value()] ?? '') : '';

            if ($ambito === 'rstgr') {
                $nombre_ubi = (string) ($oPersona->getDl() ?? '');
            } else {
                $id_ctr = $oPersona->getId_ctr();
                if ($id_ctr === null) {
                    continue;
                }
                $oCentroDl = $CentroDlRepository->findById($id_ctr);
                $nombre_ubi = $oCentroDl instanceof CentroDl ? $oCentroDl->getNombre_ubi() : '';
            }

            $telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, 'telf', ' / ', '*', false);
            $telfs_movil = $telecoService->getTelecosPorTipo($id_nom, 'móvil', ' / ', '*', false);
            if ($telfs_fijo !== '' && $telfs_movil !== '') {
                $telfs = $telfs_fijo . ' / ' . $telfs_movil;
            } else {
                $telfs = $telfs_fijo . $telfs_movil;
            }
            $mails = $telecoService->getTelecosPorTipo($id_nom, 'e-mail', ' / ', '*', false);

            if ($lista && is_array($aAsignaturas)) {
                $as = '';
                foreach ($aAsignaturas as $asig) {
                    $as .= $as === '' ? '' : ' / ';
                    $as .= (string) $asig;
                }
            } else {
                $as = is_int($aAsignaturas) ? $aAsignaturas : (int) $aAsignaturas;
            }

            $rows[] = [
                'id_nom' => $id_nom,
                'id_tabla' => $id_tabla,
                'nom' => $nom,
                'nombre_ubi' => $nombre_ubi,
                'stgr' => $stgr,
                'asig_txt' => $as,
                'telfs' => $telfs,
                'mails' => $mails,
            ];
        }

        return [
            'titulo' => $titulo,
            'obj_pau' => $obj_pau,
            'lista' => $lista,
            'rows' => $rows,
        ];
    }

    private function personaRepository(string $obj_pau): PersonaDlRepositoryInterface|PersonaNRepositoryInterface|PersonaAgdRepositoryInterface
    {
        return match ($obj_pau) {
            'PersonaDl' => $this->personaDlRepository,
            'PersonaN' => $this->personaNRepository,
            'PersonaAgd' => $this->personaAgdRepository,
            default => throw new \InvalidArgumentException('obj_pau'),
        };
    }
}

