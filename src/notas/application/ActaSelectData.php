<?php

declare(strict_types=1);

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\support\ActaPrefijosDeEsquema;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\criteria\ActaField as F;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use src\shared\security\HashB;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

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
        private readonly ActaPrefijosDeEsquema $actaPrefijosDeEsquema,
    ) {
    }
    /**
     * @param array{titulo?:string, acta?:string, mes_fin_stgr:int} $in
     * @return array{titulo:string, a_asignaturas: array<int|string, string|null>, actas: list<array{acta:string, f_acta:?string, id_asignatura:int, has_pdf:bool}>}
     */

    public function execute(array $in): array
    {
        $Qtitulo = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'titulo');
        $Qacta = \src\shared\domain\helpers\FuncTablasSupport::inputString($in, 'acta');
        $mes_fin_stgr = \src\shared\domain\helpers\FuncTablasSupport::inputInt($in, 'mes_fin_stgr', 6);

        $mi_dele = ConfigGlobal::mi_delef();
        $ambito = ConfigGlobal::mi_ambito();

        $titulo = '';

        if ($Qacta !== '') {
            $criteria = Criteria::create()
                ->where(Filter::matches(F::Acta, $Qacta))
                ->orderBy(F::FActa, OrderDirection::Desc)
                ->addOrderBy(F::Acta, OrderDirection::Desc);

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
                    $criteria = $criteria->where(Filter::matches(F::Acta, $Qacta_dl));
                    $repoActas = $this->actaRepository;
                } else {
                    $soloNumero = empty($matches[3]);
                    $patron = $this->actaPrefijosDeEsquema->patronBusquedaPorNumero($Qacta, $soloNumero);
                    $criteria = $criteria->where(Filter::matches(
                        F::Acta,
                        $patron !== ''
                            ? $patron
                            : (empty($matches[3]) ? "$mi_dele " . $matches[1] . '/' . date('y') : "$mi_dele $Qacta")
                    ));
                    $repoActas = $this->actaDlRepository;
                }
                $cActas = $repoActas->matching($criteria);
            } else {
                if ($ambito === 'rstgr') {
                    $repoActas = $this->actaRepository;
                    $cActas = $repoActas->matching($criteria);
                } else {
                    $repoActas = $this->actaDlRepository;
                    $cActas = $repoActas->matching($criteria);
                    if (empty($cActas)) {
                        $repoActas = $this->actaExRepository;
                        $cActas = $repoActas->matching($criteria);
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
            $inicurs_ca = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('inicio', $any)->format('Y-m-d');
            $fincurs_ca = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('fin', $any)->format('Y-m-d');
            $txt_curso = "$inicurs_ca - $fincurs_ca";

            $limite = 20;
            $criteria = Criteria::create()
                ->where(Filter::between(F::FActa, $inicurs_ca, $fincurs_ca))
                ->orderBy(F::FActa, OrderDirection::Desc)
                ->addOrderBy(F::Acta, OrderDirection::Desc)
                ->limit($limite);

            $titulo = ucfirst(sprintf(_("lista de actas del curso %s. Máximo %s"), $txt_curso, $limite));
            if ($ambito === 'rstgr') {
                $repoDelegacion = $this->delegacionRepository;
                $aDlMap = $repoDelegacion->getArrayDlRegionStgr([$mi_dele]);
                $aDl = array_values($aDlMap);
                $sReg = implode(' |', $aDl);
                $Qacta_pat = "^($sReg )";
                $criteria = $criteria->andWhere(Filter::matches(F::Acta, $Qacta_pat));
                $repoActas = $this->actaRepository;
            } else {
                $repoActas = $this->actaDlRepository;
            }
            $cActas = $repoActas->matching($criteria);
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
                'ctx_eliminar' => HashB::sign('acta_eliminar', ['acta' => (string) $oActa->getActa()]),
            ];
        }

        return [
            'titulo' => $titulo,
            'a_asignaturas' => $a_asignaturas,
            'actas' => $actas,
        ];
    }
}
