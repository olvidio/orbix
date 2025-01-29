<?php

namespace planning\domain;

use actividades\model\entity\GestorActividad;
use ubis\model\entity\GestorCasaDl;
use ubis\model\entity\GestorCentroEllas;
use ubis\model\entity\Ubi;

class ActividadesPorCasas
{

    /**
     * @param int $Qcdc_sel
     * @param \web\DateTimeLocal $oIniPlanning
     * @param \web\DateTimeLocal $oFinPlanning
     * @param mixed $sin_activ
     * @param string $fin_iso
     * @param string $inicio_iso
     * @return array
     */
    public static function actividadesPorCasas(int $Qcdc_sel, \web\DateTimeLocal $oIniPlanning, \web\DateTimeLocal $oFinPlanning, mixed $sin_activ, string $fin_iso, string $inicio_iso): array
    {
        $GesActividades = new GestorActividad();
        $sCdc = '';
        if ($Qcdc_sel < 10) { //Para buscar por casas.
            $aWhere = array();
            $aOperador = array();
            switch ($Qcdc_sel) {
                case 1:
                    $aWhere['sv'] = 't';
                    $aWhere['sf'] = 't';
                    break;
                case 2:
                    $aWhere['sv'] = 'f';
                    $aWhere['sf'] = 't';
                    break;
                case 3: // casas comunes: cdr + dlb + sf +sv
                    $aWhere['sv'] = 't';
                    $aWhere['sf'] = 't';
                    $aWhere['tipo_ubi'] = 'cdcdl';
                    $aWhere['tipo_casa'] = 'cdc|cdr';
                    $aOperador['tipo_casa'] = '~';
                    break;
                case 4:
                    $aWhere['sv'] = 't';
                    break;
                case 5:
                    $aWhere['sf'] = 't';
                    break;
                case 6:
                    $aWhere['sf'] = 't';
                    // también los centros que son como cdc
                    $GesCentrosSf = new GestorCentroEllas();
                    $cCentrosSf = $GesCentrosSf->getCentros(array('cdc' => 't', '_ordre' => 'nombre_ubi'));
                    break;
                case 9:
                    // posible selección múltiple de casas
                    $a_id_cdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                    if (!empty($a_id_cdc)) {
                        $sCdc = implode(',', $a_id_cdc);
                        //$aWhere['id_ubi'] = '^' . implode('$|^', $a_id_cdc) . '$';
                        //$aOperador['id_ubi'] = '~';
                        $aWhere['id_ubi'] = $sCdc;
                        $aOperador['id_ubi'] = 'IN';
                    }
                    break;
            }
            $aWhere['_ordre'] = 'nombre_ubi';
            $GesCasaDl = new GestorCasaDl();
            $cCasasDl = $GesCasaDl->getCasas($aWhere, $aOperador);

            if ($Qcdc_sel === 6) { //añado los ctr de sf
                foreach ($cCentrosSf as $oCentroSf) {
                    $cCasasDl[] = $oCentroSf;
                }
            }

            $p = 0;
            $cdc = [];
            $a_actividades = [];
            foreach ($cCasasDl as $oCasaDl) {
                $a_cdc = array();
                $id_ubi = $oCasaDl->getId_ubi();
                $nombre_ubi = $oCasaDl->getNombre_ubi();

                $cdc[$p] = "u#$id_ubi#$nombre_ubi";

                $a_cdc = $GesActividades->actividadesDeUnaCasa($id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
                if ($a_cdc !== false) {
                    $a_actividades[$nombre_ubi] = array($cdc[$p] => $a_cdc);
                    $p++;
                } elseif ($sin_activ === 1) {
                    $a_actividades[$nombre_ubi] = array($cdc[$p] => array());
                    $p++;
                }
            }
            ksort($a_actividades);
            /*
             lo que sigue es para que nos represente una linea en blanco al final:
             esto permite visualizar correctamente las 3 divisiones en los días
             en que todas las casas están ocupadas.
             */
            $cdc[$p + 1] = "##";
            $a_actividades[] = array($cdc[$p + 1] => array());
        } else { // cdc_sel > 10 Para buscar por actividades (todas).
            // busco todas las actividades del periodo y las agrupo por ubis.
            $oGesActividades = new GestorActividad();
            $aWhere = array();
            $aOperador = array();
            switch ($Qcdc_sel) {
                case 11:
                    $aWhere['id_tipo_activ'] = '^1';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
                case 12:
                    $aWhere['id_tipo_activ'] = '^2';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
            }
            $aWhere['f_ini'] = "'$fin_iso'";
            $aOperador['f_ini'] = '<=';
            $aWhere['f_fin'] = "'$inicio_iso'";
            $aOperador['f_fin'] = '>=';
            $aWhere['status'] = 4;
            $aOperador['status'] = '<';
            $aWhere['_ordre'] = 'id_ubi';

            $aUbis = $oGesActividades->getUbis($aWhere, $aOperador);
            $p = 0;
            $a_actividades = array();
            foreach ($aUbis as $id_ubi) {
                $a_cdc = array();
                if (empty($id_ubi)) {
                    $nombre_ubi = _("por determinar");
                    $cdc[$p] = "u#2#$nombre_ubi"; // hay que poner un id_ubi para que vaya bien la función de dibujar el calendario.
                } elseif ($id_ubi == 1) {
                    $nombre_ubi = _("otros lugares");
                    $cdc[$p] = "u#$id_ubi#$nombre_ubi";
                } else {
                    $oCasa = Ubi::NewUbi($id_ubi);
                    $id_ubi = $oCasa->getId_ubi();
                    $nombre_ubi = $oCasa->getNombre_ubi();
                    $cdc[$p] = "u#$id_ubi#$nombre_ubi";
                }
                $a_cdc = $GesActividades->actividadesDeUnaCasa($id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
                if ($a_cdc !== false) {
                    $a_actividades[$nombre_ubi] = array($cdc[$p] => $a_cdc);
                    $p++;
                } elseif ($sin_activ === 1) {
                    $a_actividades[$nombre_ubi] = array($cdc[$p] => array());
                    $p++;
                }
            }
            ksort($a_actividades);
            /*
             lo que sigue es para que nos represente una linea en blanco al final:
             esto permite visualizar correctamente las 3 divisiones en los días
             en que todas las casas están ocupadas.
             */
            $cdc[$p + 1] = "##";
            $a_actividades[] = array($cdc[$p + 1] => array());
        }
        return array($sCdc, $a_actividades);
    }
}