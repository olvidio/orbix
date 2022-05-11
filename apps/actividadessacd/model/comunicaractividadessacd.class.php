<?php
namespace actividadessacd\model;

use actividadcargos\model\entity\GestorActividadCargo;
use actividadcargos\model\entity\GestorCargo;
use actividades\model\entity\Actividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use core\ConfigGlobal;
use function core\is_true;
use procesos\model\entity\GestorActividadProcesoTarea;
use ubis\model\entity\Ubi;
use web\TiposActividades;

class ComunicarActividadesSacd {
    
    /* ATRIBUTS ----------------------------------------------------------------- */
    private $cPersonas;
    private $inicioIso;
    private $finIso;
    private $propuesta;
    private $soloCargos = FALSE;
    private $quitarInactivos = FALSE;
    
    
    /* CONSTRUCTOR -------------------------------------------------------------- */
    
    
    /**
     * Constructor de la classe.
     *
     */
    function __construct() {
        
    }
    
    public function setPersonas($cPersonas) {
        $this->cPersonas = $cPersonas;
    }
    public function setInicioIso($inicioIso) {
        $this->inicioIso = $inicioIso;
    }
    public function setFinIso($finIso) {
        $this->finIso = $finIso;
    }
    public function setPropuesta($propuesta) {
        $this->propuesta = $propuesta;
    }
    public function setSoloCargos($soloCargos) {
        $this->soloCargos = $soloCargos;
    }
    public function setQuitarInactivos($quitarInactivos) {
        $this->quitarInactivos = $quitarInactivos;
    }

    public function getArrayComunicacion() {
        // valores del id_cargo de tipo_cargo = sacd:
        $gesCargos = new GestorCargo();
        $aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');

        $GesActividadProceso = new GestorActividadProcesoTarea();
        $oActividadesSacdFunciones = new ActividadesSacdFunciones();
        $s=0;
        $array_actividades = [];
        // busco los datos de las actividades 
        $aWhereAct = [];
        $aOperadorAct = [];
        $aWhereAct['f_ini']="'$this->finIso'";
        $aOperadorAct['f_ini']='<=';
        $aWhereAct['f_fin']="'$this->inicioIso'";
        $aOperadorAct['f_fin']='>=';
        $aWhereAct['status']='2';
        foreach ($this->cPersonas as $oPersona) {
            $s++;
            $id_nom=$oPersona->getId_nom();
            $nom_ap=$oPersona->getPrefApellidosNombre();
            $idioma = $oPersona->getLengua();

            $array_actividades[$id_nom]['txt']['com_sacd'] = $oActividadesSacdFunciones->getTraduccion('com_sacd', $idioma);
            $array_actividades[$id_nom]['txt']['t_propio'] = $oActividadesSacdFunciones->getTraduccion('t_propio',$idioma);
            $array_actividades[$id_nom]['txt']['t_f_ini'] = $oActividadesSacdFunciones->getTraduccion('t_f_ini',$idioma);
            $array_actividades[$id_nom]['txt']['t_f_fin'] = $oActividadesSacdFunciones->getTraduccion('t_f_fin',$idioma);
            $array_actividades[$id_nom]['txt']['t_nombre_ubi'] = $oActividadesSacdFunciones->getTraduccion('t_nombre_ubi',$idioma);
            $array_actividades[$id_nom]['txt']['t_sfsv'] = $oActividadesSacdFunciones->getTraduccion('t_sfsv',$idioma);
            $array_actividades[$id_nom]['txt']['t_actividad'] = $oActividadesSacdFunciones->getTraduccion('t_actividad',$idioma);
            $array_actividades[$id_nom]['txt']['t_asistentes'] = $oActividadesSacdFunciones->getTraduccion('t_asistentes',$idioma);
            $array_actividades[$id_nom]['txt']['t_encargado'] = $oActividadesSacdFunciones->getTraduccion('t_encargado',$idioma);
            $array_actividades[$id_nom]['txt']['t_observ'] = $oActividadesSacdFunciones->getTraduccion('t_observ',$idioma);
            $array_actividades[$id_nom]['txt']['t_nom_tipo'] = $oActividadesSacdFunciones->getTraduccion('t_nom_tipo',$idioma);
            
            $array_actividades[$id_nom]['nom_ap']=$nom_ap;
            
            $aWhere = ['id_nom' => $id_nom];
            $aOperador = [];
            
            $oGesActividadCargo = new GestorActividadCargo();
            
            if ($this->soloCargos === TRUE) {
                $cAsistentes = $oGesActividadCargo ->getCargoDeActividad($aWhere,$aOperador,$aWhereAct,$aOperadorAct);
            } else {
                $cAsistentes = $oGesActividadCargo ->getAsistenteCargoDeActividad($aWhere,$aOperador,$aWhereAct,$aOperadorAct);
            }
            
            $ord_activ = [];
            foreach ($cAsistentes as $aAsistente) {
                $id_activ = $aAsistente['id_activ'];
                $propio = $aAsistente['propio'];
                //$plaza = $aAsistente['plaza'];
                $id_cargo = empty($aAsistente['id_cargo'])? '' : $aAsistente['id_cargo'];
                
                $_SESSION['oPermActividades']->setId_activ($id_activ);
                    
                if( !is_true($this->propuesta) && ConfigGlobal::is_app_installed('procesos')) {
                	$permiso_ver = FALSE;
                	if (!empty($id_cargo)) {
                		// Si tiene cargo sacd (se supone que comunicaractidvidadessacd sólo es para los sacd), que la fase 'ok_sacd' esté completada
                		$sacd_aprobado = $GesActividadProceso->getSacdAprobado($id_activ);
                		if ($sacd_aprobado === TRUE) {
                			$permiso_ver = $_SESSION['oPermActividades']->havePermisoSacd($id_cargo, $propio);
                		}
                	} else {
                		// Si es asistente, que la fase ok_asistente esté completada.
						$permiso_ver = $_SESSION['oPermActividades']->havePermisoSacd($id_cargo, $propio);
                	}
                } else {
                    $permiso_ver = TRUE;
                }
                
                if ($permiso_ver === FALSE) { continue; }
                
                $oActividad = new Actividad($id_activ);
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $id_ubi = $oActividad->getId_ubi();
                $lugar_esp = $oActividad->getLugar_esp();
                $oF_ini = $oActividad->getF_ini();
                $oF_fin = $oActividad->getF_fin();
                $h_ini = $oActividad->getH_ini();
                $h_fin = $oActividad->getH_fin();
                $observ = $oActividad->getObserv();
                
                $f_ini=$oF_ini->formatRoman();
                $f_fin=$oF_fin->formatRoman();

                if (!empty($h_ini)) {
                       $h_ini = preg_replace('/(\d{2}):(\d{2}):(\d{2})/','\1:\2',$h_ini);
                       $f_ini.=" ($h_ini)";
                }
                if (!empty($h_fin)) {
                       $h_fin = preg_replace('/(\d{2}):(\d{2}):(\d{2})/','\1:\2',$h_fin);
                       $f_fin.=" ($h_fin)";
                }

                $oTipoActiv= new TiposActividades($id_tipo_activ);
                $ssfsv=$oTipoActiv->getSfsvText();
                $sasistentes=$oTipoActiv->getAsistentesText();
                $sactividad=$oTipoActiv->getActividadText();
                $snom_tipo=$oTipoActiv->getNom_tipoText();
                // lugar 
                if (empty($lugar_esp)) {
                    $oCasa = Ubi::NewUbi($id_ubi);
                    $nombre_ubi = $oCasa->getNombre_ubi();
                } else {
                    $nombre_ubi = $lugar_esp;
                }

                // ctr que organiza:
                $GesCentroEncargado = new GestorCentroEncargado();
                $ctrs = '';
                foreach($GesCentroEncargado->getCentrosEncargadosActividad($id_activ) as $oCentro) {;
                    if (!empty($ctrs)) $ctrs.=", ";
                    $ctrs .= $oCentro->getNombre_ubi();
                }

                $cargo='';
                if (!empty($id_cargo) && !array_key_exists($id_cargo, $aIdCargos_sacd)) {
                    $cargo='te carrec';
                }
                $array_act=array( "propio" => $propio,
                                    "f_ini" => $f_ini,
                                    "f_fin" =>		$f_fin, 
                                    "nombre_ubi" =>	$nombre_ubi, 
                                    "id_activ" 	=>	$id_activ, 
                                    "sfsv" 		=>	$ssfsv, 
                                    "asistentes" =>	$sasistentes, 
                                    "actividad" =>	$sactividad,
                                    "nom_tipo" =>	$snom_tipo, 
                                    "observ" =>		$observ, 
                                    "cargo" =>		$cargo, 
                                    "encargado" =>	$ctrs
                                );
                //if (!empty($id_activ)) { $array_actividades[$id_nom]['actividades'][]= $array_act; }
                // para ordenar por fecha_ini
                $f_ord = $oF_ini->format('Ymd');
                // ojo. Si hay más de una actividad que empieza el mismo día, hay que poner algo para distinguirlas: les sumo un dia.
                if (isset($ord_activ) && array_key_exists($f_ord,$ord_activ)) {
                    $f_ord++;
                    $ord_activ[$f_ord]=$array_act;
                } else {
                    $ord_activ[$f_ord]=$array_act;
                }
            }
            if (!empty($ord_activ)) {
                ksort($ord_activ);
                $array_actividades[$id_nom]['actividades']= $ord_activ;
            } else {
                $array_actividades[$id_nom]['actividades']= '';
                // No pongo a los sacd de paso, si no tienen actividades
                if ($this->quitarInactivos === TRUE) {
                    unset($array_actividades[$id_nom]);
                }
            }
            $ord_activ=array();
        } // fin del while de los sacd


        return $array_actividades;
    }
}