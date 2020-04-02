<?php
namespace actividadessacd\model;
use actividadcargos\model\entity\ActividadCargo;
use actividadcargos\model\entity\GestorActividadCargo;
use actividadcargos\model\entity\GestorCargo;
use actividades\model\entity\GestorActividadDl;
use actividadescentro\model\entity\GestorCentroEncargado;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoSacd;

class AsignarSacd {
    
	/* ATRIBUTS ----------------------------------------------------------------- */
    
    private $f_ini_iso;
    private $a_actividades;
    private $a_activ_ctr;
    private $a_ctr_sacd;
    
    public function setF_ini($f_ini) {
        $this->f_ini_iso = $f_ini;
    }

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
    
	private  function selActividades() {
	   $aWhere = [];
	   $aOperador = [];
	   $aWhere['id_tipo_activ'] = '.(4|5|7)';
	   $aOperador['id_tipo_activ'] = '~';
	   $aWhere['f_ini'] = $this->f_ini_iso;
	   $aOperador['f_ini'] = '>';
	   $aWhere['status'] = 2;
	   
	   $oGesActividades = new GestorActividadDl();
	   $this->a_actividades = $oGesActividades->getArrayIds($aWhere,$aOperador);
	   
       return $this->a_actividades; 
	}
	
	private function selCtrEncargados() {
	    $a_actividades = $this->a_actividades;
	    $a_ctr = [];
	    $oGesCentroEncargado = new GestorCentroEncargado();
	    foreach ($a_actividades as $id_activ) {
	        $cCetrosEncargados = $oGesCentroEncargado->getCentrosEncargados(['id_activ'=>$id_activ, 'num_orden'=>0]);
	        // sólo debería haber uno
	        if (count($cCetrosEncargados) == 1) {
	           $oCentroEncargado = $cCetrosEncargados[0];
	           $a_ctr[$id_activ] = $oCentroEncargado->getId_ubi(); 
	        }
	    }
	    return $a_ctr;
	}

	
	private function selCtrSacd() {
	 
	    if (empty($this->a_ctr_sacd)) {
            // tipo encargo: 1100 atn ctr sv, 1200 atn ctr sf
            $a_ctr_sacd = [];
            
            $aWhere = [];
            $aOperador = [];
            $oGesEncargos = new GestorEncargo();
            $aWhere['id_tipo_enc'] = '^1[12]00';
            $aOperador['id_tipo_enc'] = '~';
            $cEncargos = $oGesEncargos->getEncargos($aWhere,$aOperador);
            foreach ($cEncargos as $oEncargo) {
                $id_enc  = $oEncargo->getId_enc();
                $id_ubi  = $oEncargo->getId_ubi();
                
                $oGesEncargosSacd = new GestorEncargoSacd();
                $aWhereS = [];
                $aOperadorS = [];
                $aWhereS['id_enc'] = $id_enc;
                $aWhereS['f_fin'] = 'x';
                $aOperadorS['f_fin'] = 'IS NULL';
                $aWhereS['modo'] = '2|3';
                $aOperadorS['modo'] = '~';
                $cEncargosSacd = $oGesEncargosSacd->getEncargosSacd($aWhereS,$aOperadorS);
                foreach ($cEncargosSacd as $oEncargoSacd) {
                    $id_nom = $oEncargoSacd->getId_nom();
                    $a_ctr_sacd[$id_ubi] = $id_nom;
                }
            }
            $this->a_ctr_sacd = $a_ctr_sacd;
        }
        return $this->a_ctr_sacd;
	}
	
	public function getCtrActiv() {
	    if (empty($this->a_activ_ctr)) {
            $this->selActividades();
            $this->a_activ_ctr = $this->selCtrEncargados();
	    }
	    return $this->a_activ_ctr;
	}

	public function ActivSinSacd() {
	    // valores del id_cargo de tipo_cargo = sacd:
	    $gesCargos = new GestorCargo();
	    $aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');
	    $txt_where_cargos = implode(',',array_keys($aIdCargos_sacd));
	    
	    $aWhere = [];
	    $aOperador = [];
	    $aWhere['id_cargo'] = $txt_where_cargos;
	    $aOperador['id_cargo']= 'IN';
	    
	    $a_sin_sacd = [];
	    $oGesActividadCargo = new GestorActividadCargo();
	    $a_actividades = $this->a_actividades;
	    foreach($a_actividades as $id_activ) {
            $aWhere['id_activ']=$id_activ;
	        $cActividadCargo = $oGesActividadCargo->getActividadCargos($aWhere,$aOperador);
	        // me interesa los que no tienen asignado a nadie:
	        if (count($cActividadCargo) == 0) {
	           $a_sin_sacd[] = $id_activ;
	        }
	    }
	    return $a_sin_sacd;
	}
	
	public function asignarAuto() {
	    $this->selActividades();
        $a_sin_sacd = $this->ActivSinSacd();
        
        // asigno los cargos:
        $i=0;
        $asig=0;
        foreach ($a_sin_sacd as $id_activ) {
            $i++;
            $n = $this->AsignarSacd($id_activ);
            $asig += $n;
        }
        $sin_asig = $i-$asig;
        
        return ['asignadas' => $asig,
                'sin_asignar' => $sin_asig,
        ];
	}
	
	public function AsignarSacd($id_activ) {
	    // valores del id_cargo de tipo_cargo = sacd:
	    $gesCargos = new GestorCargo();
	    $aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');
	    // Solo a partir de php 7.3: $id_cargo = array_key_first($aIdCargos_sacd);
	    $id_cargo = key($aIdCargos_sacd);
	   
	    // ctr encargado de la actividad
	    $a_activ_ctr = $this->getCtrActiv();
	    $id_ubi = $a_activ_ctr[$id_activ];
	    
	    //sacd encargado del ctr
        $a_ctr_sacd = $this->selCtrSacd();
        if (!empty($a_ctr_sacd[$id_ubi])) {
            $id_nom = $a_ctr_sacd[$id_ubi];
            $oActividadcargo = new ActividadCargo();
            $oActividadcargo->setId_activ($id_activ);
            $oActividadcargo->setId_cargo($id_cargo);
            $oActividadcargo->setId_nom($id_nom);
            $oActividadcargo->setObserv('auto');
            $oActividadcargo->DBGuardar();
            $n = 1;
        } else {
            $n = 0;
        }
        return $n;
	}
    
}