<?php

namespace src\actividades\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use frontend\shared\web\Periodo;

/**
 * Lista centros encargados de actividades en un periodo dado y, para cada
 * actividad, enumera los otros centros encargados.
 *
 * Concentra todos los accesos a repositorios; el controlador frontend
 * solo parsea el POST y renderiza el HTML devuelto.
 *
 * Devuelve:
 *   - html (string)  Bloque HTML con los <h3> por centro y sus <table> de actividades.
 */
final class ListaCentrosActivDatos
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function ejecutar(array $input): array
    {
        $Qid_ctr_num = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ctr_num');
        $Qa_id_ctr = is_array($input['id_ctr'] ?? null) ? $input['id_ctr'] : [];
        $Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
        $Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'year');
        $Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');
        $Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');

        if (empty($Qperiodo)) {
            $Qperiodo = 'actual';
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();
        if ($Qperiodo === 'desdeHoy') {
            $condicion_periodo = "f_fin BETWEEN '$inicioIso' AND '$finIso'";
        } else {
            $condicion_periodo = "f_ini BETWEEN '$inicioIso' AND '$finIso'";
        }

        $CentroRepository = $this->centroDlRepository;
        $aWhere = [];
        $aOperador = [];
        if (empty($Qid_ctr_num)) {
            $aWhere = ['tipo_ctr' => '^s[^s]', '_ordre' => 'nombre_ubi'];
            $aOperador = ['tipo_ctr' => '~'];
            $cCentros = $CentroRepository->getCentros($aWhere, $aOperador);
        } else {
            $Qa_id_ctr = array_filter($Qa_id_ctr);
            if (empty($Qa_id_ctr)) {
                $aWhere = ['tipo_ctr' => '^s[^s]', '_ordre' => 'nombre_ubi'];
                $aOperador = ['tipo_ctr' => '~'];
            } else {
                $idCtrList = [];
                foreach ($Qa_id_ctr as $idCtr) {
                    if (is_scalar($idCtr)) {
                        $idCtrList[] = (string) $idCtr;
                    }
                }
                $aWhere['id_ubi'] = implode(',', $idCtrList);
                $aOperador['id_ubi'] = 'IN';
            }
            $cCentros = $CentroRepository->getCentros($aWhere, $aOperador);
        }

        $c = 0;
        $a_centros = [];
        $a_actividades = [];
        $CentroEncargadoRepository = $this->centroEncargadoRepository;
        foreach ($cCentros as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $a_centros[$c] = $oCentro->getNombre_ubi();
            $cActividades = $CentroEncargadoRepository->getActividadesDeCentros($id_ubi, $condicion_periodo);
            $a = 0;
            foreach ($cActividades as $oActividad) {
                $a++;
                $id_activ = $oActividad->getId_activ();
                $a_actividades[$c][$a]['nom_activ'] = $oActividad->getNom_activ();
                $cEncargados = $CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ);
                $i = 0;
                $txt_ctr = "";
                foreach ($cEncargados as $oUbi) {
                    $i++;
                    $id_ctr = $oUbi->getId_ubi();
                    $ctr = $oUbi->getNombre_ubi();
                    if ($id_ctr !== $id_ubi) {
                        $clase = ($i === 1) ? "class='responsable'" : "";
                        $txt_ctr .= "<span $clase> $ctr;</span>";
                    }
                    $a_actividades[$c][$a]["mas_ctr"] = $txt_ctr;
                }
            }
        }

        $html = "<style>.responsable { text-decoration: underline; }</style>";
        $num_ctr = count($a_centros);
        for ($c = 1; $c <= $num_ctr; $c++) {
            $centro = $a_centros[$c];
            $html .= "<h3>" . htmlspecialchars($centro, ENT_QUOTES, 'UTF-8') . "</h3>";
            $html .= "<table>";
            if (!empty($a_actividades[$c])) {
                foreach ($a_actividades[$c] as $actividad) {
                    $html .= "<tr><td>" . htmlspecialchars($actividad['nom_activ'], ENT_QUOTES, 'UTF-8') . "</td>";
                    $html .= "<td>" . ($actividad['mas_ctr'] ?? '') . "</td></tr>";
                }
            }
            $html .= "</table>";
        }

        return [
            'html' => $html,
        ];
    }
}
