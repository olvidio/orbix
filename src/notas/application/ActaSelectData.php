<?php

declare(strict_types=1);

namespace src\notas\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use function src\shared\domain\helpers\curso_est;

/**
 * Lista de actas y mapa de asignaturas para `acta_select` (frontend sin repositorios).
 */
final class ActaSelectData
{

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly ActaRepositoryInterface $actaRepository,
        private readonly ActaDlRepositoryInterface $actaDlRepository,
        private readonly ActaExRepositoryInterface $actaExRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }
    /**
     * @param array{titulo?:string, acta?:string, mes_fin_stgr:int} $in
     * @return array{titulo:string, a_asignaturas: array<int|string, string|null>, actas: list<array{acta:string, f_acta:?string, id_asignatura:int, has_pdf:bool}>}
     */

    public function execute(array $in): array
    {
        $Qtitulo = input_string($in, 'titulo');
        $Qacta = input_string($in, 'acta');
        $mes_fin_stgr = input_int($in, 'mes_fin_stgr', 6);

        $mi_dele = ConfigGlobal::mi_delef();
        $ambito = ConfigGlobal::mi_ambito();

        $aWhere = [];
        $aOperador = [];
        $titulo = '';

        if ($Qacta !== '') {
            $aWhere['acta'] = $Qacta;
            $aOperador['acta'] = '~';
            $aWhere['_ordre'] = 'f_acta DESC, acta DESC';

            $matches = [];
            preg_match("/^(\d*)(\/)?(\d*)/", $Qacta, $matches);
            $cActas = [];
            if (!empty($matches[1])) {
                if ($ambito === 'rstgr') {
                    $repoDelegacion = $this->delegacionRepository;
                    $aDlMap = $repoDelegacion->getArrayDlRegionStgr([$mi_dele]);
                    $aDl = array_values($aDlMap);
                    $Qacta_dl = '';
                    foreach ($aDl as $dl) {
                        $Qacta_dl .= empty($Qacta_dl) ? '' : '|';
                        $Qacta_dl .= empty($matches[3]) ? "$dl " . $matches[1] . '/' . date('y') : "$dl $Qacta";
                    }
                    $aWhere['acta'] = $Qacta_dl;
                    $repoActas = $this->actaRepository;
                } else {
                    $aWhere['acta'] = empty($matches[3]) ? "$mi_dele " . $matches[1] . '/' . date('y') : "$mi_dele $Qacta";
                    $repoActas = $this->actaDlRepository;
                }
                $cActas = $repoActas->getActas($aWhere, $aOperador);
            } else {
                if ($ambito === 'rstgr') {
                    $repoActas = $this->actaRepository;
                    $cActas = $repoActas->getActas($aWhere, $aOperador);
                } else {
                    $repoActas = $this->actaDlRepository;
                    $cActas = $repoActas->getActas($aWhere, $aOperador);
                    if (empty($cActas)) {
                        $repoActas = $this->actaExRepository;
                        $cActas = $repoActas->getActas($aWhere, $aOperador);
                    }
                }
            }
            $titulo = $Qtitulo;
        } else {
            $mes = date('m');
            if ((int)$mes > $mes_fin_stgr) {
                $any = (int)date('Y') + 1;
            } else {
                $any = (int)date('Y');
            }
            $inicurs_ca = curso_est('inicio', $any)->format('Y-m-d');
            $fincurs_ca = curso_est('fin', $any)->format('Y-m-d');
            $txt_curso = "$inicurs_ca - $fincurs_ca";

            $aWhere['f_acta'] = "'$inicurs_ca','$fincurs_ca'";
            $aOperador['f_acta'] = 'BETWEEN';
            $aWhere['_ordre'] = 'f_acta DESC, acta DESC';
            $aWhere['_limit'] = 20;

            $titulo = ucfirst(sprintf(_("lista de actas del curso %s. Máximo %s"), $txt_curso, $aWhere['_limit']));
            if ($ambito === 'rstgr') {
                $repoDelegacion = $this->delegacionRepository;
                $aDlMap = $repoDelegacion->getArrayDlRegionStgr([$mi_dele]);
                $aDl = array_values($aDlMap);
                $sReg = implode(' |', $aDl);
                $Qacta_pat = "^($sReg )";
                $aWhere['acta'] = $Qacta_pat;
                $aOperador['acta'] = '~';
                $repoActas = $this->actaRepository;
            } else {
                $repoActas = $this->actaDlRepository;
            }
            $cActas = $repoActas->getActas($aWhere, $aOperador);
        }

        $AsignaturaRepository = $this->asignaturaRepository;
        $a_asignaturas = $AsignaturaRepository->getArrayAsignaturas();

        $actas = [];
        foreach ($cActas as $oActa) {
            $pdf = $oActa->getPdf();
            $actas[] = [
                'acta' => $oActa->getActa(),
                'f_acta' => $oActa->getF_acta()?->getFromLocal(),
                'id_asignatura' => (int)$oActa->getId_asignatura(),
                'has_pdf' => $pdf !== null,
            ];
        }

        return [
            'titulo' => $titulo,
            'a_asignaturas' => $a_asignaturas,
            'actas' => $actas,
        ];
    }
}
