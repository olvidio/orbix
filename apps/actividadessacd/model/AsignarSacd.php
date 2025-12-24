<?php

namespace actividadessacd\model;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;

class AsignarSacd
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private $f_ini_iso;
    private $a_actividades;
    private $a_activ_ctr;
    private $a_ctr_sacd;

    public function setF_ini($f_ini)
    {
        $this->f_ini_iso = $f_ini;
    }

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /**
     * Constructor de la classe.
     *
     */
    function __construct()
    {
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    private function selActividades()
    {
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tipo_activ'] = '.(4|5|7)';
        $aOperador['id_tipo_activ'] = '~';
        $aWhere['f_ini'] = $this->f_ini_iso;
        $aOperador['f_ini'] = '>';
        $aWhere['status'] = 2;

        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $this->a_actividades = $ActividadDlRepository->getArrayIdsWithKeyFini($aWhere, $aOperador);

        return $this->a_actividades;
    }

    private function selCtrEncargados()
    {
        $a_actividades = $this->a_actividades;
        $a_ctr = [];
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        foreach ($a_actividades as $id_activ) {
            $cCetrosEncargados = $CentroEncargadoRepository->getCentrosEncargados(['id_activ' => $id_activ, 'num_orden' => 0]);
            // sólo debería haber uno
            if (count($cCetrosEncargados) === 1) {
                $oCentroEncargado = $cCetrosEncargados[0];
                $a_ctr[$id_activ] = $oCentroEncargado->getId_ubi();
            }
        }
        return $a_ctr;
    }


    private function selCtrSacd()
    {

        if (empty($this->a_ctr_sacd)) {
            // tipo encargo: 1100 atn ctr sv, 1200 atn ctr sf
            $a_ctr_sacd = [];
            $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
            $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);

            $aWhere = [];
            $aOperador = [];
            $aWhere['id_tipo_enc'] = '^1[12]00';
            $aOperador['id_tipo_enc'] = '~';
            $cEncargos = $EncargoRepository->getEncargos($aWhere, $aOperador);
            foreach ($cEncargos as $oEncargo) {
                $id_enc = $oEncargo->getId_enc();
                $id_ubi = $oEncargo->getId_ubi();

                $aWhereS = [];
                $aOperadorS = [];
                $aWhereS['id_enc'] = $id_enc;
                $aWhereS['f_fin'] = 'x';
                $aOperadorS['f_fin'] = 'IS NULL';
                $aWhereS['modo'] = '2|3';
                $aOperadorS['modo'] = '~';
                $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd($aWhereS, $aOperadorS);
                foreach ($cEncargosSacd as $oEncargoSacd) {
                    $id_nom = $oEncargoSacd->getId_nom();
                    $a_ctr_sacd[$id_ubi] = $id_nom;
                }
            }
            $this->a_ctr_sacd = $a_ctr_sacd;
        }
        return $this->a_ctr_sacd;
    }

    public function getCtrActiv()
    {
        if (empty($this->a_activ_ctr)) {
            $this->selActividades();
            $this->a_activ_ctr = $this->selCtrEncargados();
        }
        return $this->a_activ_ctr;
    }

    public function ActivSinSacd()
    {
        // valores del id_cargo de tipo_cargo = sacd:
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_cargo'] = $txt_where_cargos;
        $aOperador['id_cargo'] = 'IN';

        $a_sin_sacd = [];
        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $a_actividades = $this->a_actividades;
        foreach ($a_actividades as $id_activ) {
            $aWhere['id_activ'] = $id_activ;
            $cActividadCargo = $ActividadCargoRepository->getActividadCargos($aWhere, $aOperador);
            // me interesa los que no tienen asignado a nadie:
            if (count($cActividadCargo) == 0) {
                $a_sin_sacd[] = $id_activ;
            }
        }
        return $a_sin_sacd;
    }

    public function asignarAuto()
    {
        $this->selActividades();
        $a_sin_sacd = $this->ActivSinSacd();

        // asigno los cargos:
        $i = 0;
        $asig = 0;
        foreach ($a_sin_sacd as $id_activ) {
            $i++;
            $n = $this->AsignarSacd($id_activ);
            $asig += $n;
        }
        $sin_asig = $i - $asig;

        return ['asignadas' => $asig,
            'sin_asignar' => $sin_asig,
        ];
    }

    public function AsignarSacd($id_activ)
    {
        // valores del id_cargo de tipo_cargo = sacd:
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        // Solo a partir de php 7.3: $id_cargo = array_key_first($aIdCargos_sacd);
        $id_cargo = key($aIdCargos_sacd);

        // ctr encargado de la actividad
        $a_activ_ctr = $this->getCtrActiv();
        $id_ubi = $a_activ_ctr[$id_activ];

        //sacd encargado del ctr
        $a_ctr_sacd = $this->selCtrSacd();
        if (!empty($a_ctr_sacd[$id_ubi])) {
            $id_nom = $a_ctr_sacd[$id_ubi];
            $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
            $newIdItem = $ActividadCargoRepository->getNewId();
            $oActividadcargo = new ActividadCargo();
            $oActividadcargo->setId_item($newIdItem);
            $oActividadcargo->setId_activ($id_activ);
            $oActividadcargo->setId_cargo($id_cargo);
            $oActividadcargo->setId_nom($id_nom);
            $oActividadcargo->setObserv('auto');
            $ActividadCargoRepository->Guardar($oActividadcargo);
            $n = 1;
        } else {
            $n = 0;
        }
        return $n;
    }

}