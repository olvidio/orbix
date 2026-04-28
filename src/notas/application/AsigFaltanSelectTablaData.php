<?php

declare(strict_types=1);

namespace src\notas\application;

use src\actividades\domain\value_objects\NivelStgrId;
use src\notas\domain\value_objects\CursoStgr;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Tabla de `asig_faltan_select` (asignaturas pendientes por persona).
 */
final class AsigFaltanSelectTablaData
{
    /**
     * @param array{
     *   numero:int,
     *   b_c:string,
     *   c1:string,
     *   c2:string,
     *   personas_n:string,
     *   personas_agd:string,
     *   lista:string
     * } $in
     * @return array{titulo:string, obj_pau:string, lista:bool, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, asig_txt:string|int, telfs:string, mails:string}>}
     */
    public static function execute(array $in): array
    {
        $Qnumero = (int)($in['numero'] ?? 0);
        $Qb_c = (string)($in['b_c'] ?? '');
        $Qc1 = (string)($in['c1'] ?? '');
        $Qc2 = (string)($in['c2'] ?? '');
        $Qpersonas_n = (string)($in['personas_n'] ?? '');
        $Qpersonas_agd = (string)($in['personas_agd'] ?? '');
        $Qlista = (string)($in['lista'] ?? '');

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
        $Pendientes = new AsignaturasPendientes($personas);
        $aId_nom = $lista
            ? $Pendientes->listarFaltantesPorPersona($Qnumero, $curso)
            : $Pendientes->contarFaltantesPorPersona($Qnumero, $curso);

        $titulo = sprintf(
            _('lista de %s a los que faltan %d o menos asignaturas para finalizar el %s'),
            $gente,
            $Qnumero,
            $curso_txt
        );

        $repoPersona = self::personaRepository($obj_pau);
        $a_NivelStgr = NivelStgrId::getArrayNivelStgr();
        $telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
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
            $nivel_stgr = $oPersona->getNivelStgrVo()->value();
            $stgr = $a_NivelStgr[$nivel_stgr] ?? '';

            if ($ambito === 'rstgr') {
                $nombre_ubi = $oPersona->getDl();
            } else {
                $id_ctr = $oPersona->getId_ctr();
                $oCentroDl = $CentroDlRepository->findById($id_ctr);
                $nombre_ubi = $oCentroDl->getNombre_ubi();
            }

            $telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, 'telf', ' / ', '*', false);
            $telfs_movil = $telecoService->getTelecosPorTipo($id_nom, 'móvil', ' / ', '*', false);
            if (!empty($telfs_fijo) && !empty($telfs_movil)) {
                $telfs = $telfs_fijo . ' / ' . $telfs_movil;
            } else {
                $telfs = ($telfs_fijo ?? '') . ($telfs_movil ?? '');
            }
            $mails = $telecoService->getTelecosPorTipo($id_nom, 'e-mail', ' / ', '*', false);

            if ($lista) {
                $as = '';
                foreach ($aAsignaturas as $asig) {
                    $as .= empty($as) ? '' : ' / ';
                    $as .= $asig;
                }
            } else {
                $as = $aAsignaturas;
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

    private static function personaRepository(string $obj_pau): object
    {
        return match ($obj_pau) {
            'PersonaDl' => $GLOBALS['container']->get(PersonaDlRepositoryInterface::class),
            'PersonaN' => $GLOBALS['container']->get(PersonaNRepositoryInterface::class),
            'PersonaAgd' => $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class),
            default => throw new \InvalidArgumentException('obj_pau'),
        };
    }
}

