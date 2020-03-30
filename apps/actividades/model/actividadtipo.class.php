<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace actividades\model;

use core\ConfigGlobal;
use core\ViewTwig;
use web;

/**
 * Description of actividadtipo
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class ActividadTipo {

	private $ssfsv;
	private $sasistentes;
	private $sactividad;
	private $snom_tipo;
	private $status;
	private $que;
	private $id_tipo_activ;
	private $para;
	private $bperm_jefe = FALSE;
	private $bAll = FALSE;
			
	public function getHtml() {
		$isfsv=ConfigGlobal::mi_sfsv();

		$aSfsv=array(1=>'sv',2=>'sf');

		if (empty($this->ssfsv)) { $this->ssfsv=$aSfsv[$isfsv]; }
		if (empty($this->status)) $this->status = entity\ActividadAll::STATUS_ACTUAL;

		if (!empty($this->id_tipo_activ))  {
			$oTipoActiv= new web\TiposActividades($this->id_tipo_activ);
			$this->ssfsv=$oTipoActiv->getSfsvText();
			$this->sasistentes=$oTipoActiv->getAsistentesText();
			$this->sactividad=$oTipoActiv->getActividadText();
			$this->snom_tipo=$oTipoActiv->getNom_tipoText();
		} else {
			$oTipoActiv= new web\TiposActividades();
			// puede ser que tenga parte del id_tipo_activ.
			if (!empty($this->ssfsv)) $oTipoActiv->setSfsvText($this->ssfsv);
			if (!empty($this->sasistentes)) $oTipoActiv->setAsistentesText($this->sasistentes);
			if (!empty($this->sactividad)) $oTipoActiv->setActividadText($this->sactividad);
			// limitar el uso de all (sv,sf, resevado):
            $oTipoActiv->setPosiblesAll($this->bAll); 
		}

		$a_sfsv_posibles=$oTipoActiv->getSfsvPosibles();
		$a_actividades_posibles=$oTipoActiv->getActividadesPosibles();
		$a_nom_tipo_posibles=$oTipoActiv->getNom_tipoPosibles();


		$array2=array();
		if ($_SESSION['oPerm']->have_perm_oficina('est')) {
			$array_n = array(1=>'n', 3=>'agd');
			$array2 = array_merge($array2,$array_n);
		}
		if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
			$array_n = array(1=>'n');
			$array2 = array_merge($array2,$array_n);
		}
		if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
			$array_nax = array(1=>'nax');
			$array2 = array_merge($array2,$array_nax);
		}
		if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
			$array_agd = array(3=>'agd');
			$array2 = array_merge($array2,$array_agd);
		}
		if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
			$array_sg = array(4=>'s', 5=>'sg');
			$array2 = array_merge($array2,$array_sg);
		}
		if ($_SESSION['oPerm']->have_perm_oficina('des')) {
			if($this->status == entity\ActividadAll::STATUS_ACTUAL) {
				$array_des = $oTipoActiv->getAsistentesPosibles(); //todos
			} else {
				$array_des = array(6=>'sss+');
			}
			$array2 = array_merge($array2,$array_des);
		}
		if ($_SESSION['oPerm']->have_perm_oficina('sr')) {
			$array_sr = array(7=>'sr');
			$array2 = array_merge($array2,$array_sr);
		}

		if ($_SESSION['oPerm']->have_perm_oficina('calendario')) { // desde la sf
			$array_des = $oTipoActiv->getAsistentesPosibles(); //todos
			$array2 = array_merge($array2,$array_des);
		}


		// si es una búsqueda, también puedo buscar todos. (Excepto sf/sv)
		if ($_SESSION['oConfig']->is_jefeCalendario() || (isset($this->que) && $this->que=="buscar" || $this->bperm_jefe)) {
			$oTipoActivB= new web\TiposActividades();
			if ($this->ssfsv) $oTipoActivB->setSfsvText($this->ssfsv);
			$a_asistentes_posibles =$oTipoActivB->getAsistentesPosibles();
		} else {
			//$array1=$oTipoActiv->getAsistentesPosibles();
			$oTipoActivB= new web\TiposActividades();
			if ($this->ssfsv) $oTipoActivB->setSfsvText($this->ssfsv);
			$array1=$oTipoActivB->getAsistentesPosibles();

			$a_asistentes_posibles = array_intersect($array1, $array2);
		}
		// pasar texto a numero
		$isfsv = $oTipoActiv->getSfsvId();
		$iactividad = $oTipoActiv->getActividadId();
		$iasistentes = $oTipoActiv->getAsistentesId();
		$inom_tipo = $oTipoActiv->getnom_tipoId();
		

		$oDesplSfsv = new web\Desplegable();
		$oDesplSfsv->setNombre('isfsv_val');
		$oDesplSfsv->setOpciones($a_sfsv_posibles);
		$oDesplSfsv->setOpcion_sel($isfsv);
		if ($this->bAll === TRUE ) {
            $oDesplSfsv->setBlanco('t');
            $oDesplSfsv->setValBlanco('.');
		}
		$oDesplSfsv->setAction('fnjs_asistentes()');

		$oDesplAsistentes = new web\Desplegable();
		$oDesplAsistentes->setNombre('iasistentes_val');
		$oDesplAsistentes->setOpciones($a_asistentes_posibles);
		$oDesplAsistentes->setOpcion_sel($iasistentes);
		$oDesplAsistentes->setBlanco('t');
		$oDesplAsistentes->setValBlanco('.');
		$oDesplAsistentes->setAction('fnjs_actividad()');

		$oDesplActividad = new web\Desplegable();
		$oDesplActividad->setNombre('iactividad_val');
		$oDesplActividad->setOpciones($a_actividades_posibles);
		$oDesplActividad->setOpcion_sel($iactividad);
		$oDesplActividad->setBlanco('t');
		$oDesplActividad->setValBlanco('.');
		$oDesplActividad->setAction('fnjs_nom_tipo()');

		$oDesplNomTipo = new web\Desplegable();
		$oDesplNomTipo->setNombre('inom_tipo_val');
		$oDesplNomTipo->setOpciones($a_nom_tipo_posibles);
		$oDesplNomTipo->setOpcion_sel($inom_tipo);
		$oDesplNomTipo->setBlanco('t');
		$oDesplNomTipo->setValBlanco('...');
		if (isset($this->que)) {
		    if ( $this->que == 'buscar') {
                $oDesplNomTipo->setAction('fnjs_id_activ()');
            } else {
                $oDesplNomTipo->setAction('fnjs_act_id_activ()');
            }
		}

		$url = ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_tipo_get.php';
		$oHashTipo = new web\Hash();
		$oHashTipo->setUrl('apps/actividades/controller/actividad_tipo_get.php');
		$oHashTipo->setCamposForm('modo!salida!entrada');
		$h = $oHashTipo->linkSinVal();

		$url_act = ConfigGlobal::getWeb().'/apps/actividades/controller/actividad_ver.php';
		$oHashAct = new web\Hash();
		$oHashAct->setUrl('apps/actividades/controller/actividad_ver.php');
		$oHashAct->setCamposForm('id_tipo_activ!refresh');
		$h_act = $oHashAct->linkSinVal();

		
		$procesos_installed = ConfigGlobal::is_app_installed('procesos');
		
		$a_campos = [
		            'url' => $url,
					'h' => $h,
		            'url_act' => $url_act,
					'h_act' => $h_act,
					'perm_jefe' => $this->bperm_jefe,
					'isfsv' => $isfsv,
					'oDesplSfsv' => $oDesplSfsv,
					'oDesplAsistentes' => $oDesplAsistentes,
					'oDesplActividad' => $oDesplActividad,
					'oDesplNomTipo' => $oDesplNomTipo,
                    'procesos_installed' => $procesos_installed,
					];

		switch ($this->para) {
		    case 'tipoactiv-tarifas':
                $aditionalPaths = ['actividades' => 'actividades/view'];
                $oView = new ViewTwig('actividadtarifas/controller',$aditionalPaths);
                return $oView->render('actividad_tipo_que.html.twig',$a_campos);
                break;
		    case 'tipoactiv-procesos':
                $aditionalPaths = ['actividades' => 'actividades/view'];
                $oView = new ViewTwig('procesos/controller',$aditionalPaths);
                return $oView->render('actividad_tipo_proceso.html.twig',$a_campos);
                break;
		    case 'procesos':
                $aditionalPaths = ['actividades' => 'actividades/view'];
                $oView = new ViewTwig('procesos/controller',$aditionalPaths);
                return $oView->render('actividad_tipo_que_perm.html.twig',$a_campos);
                break;
		    case 'cambios':
                $aditionalPaths = ['actividades' => 'actividades/view'];
                $oView = new ViewTwig('cambios/controller',$aditionalPaths);
                return $oView->render('actividad_tipo_que_perm.html.twig',$a_campos);
		        break;
		    case 'actividades':
		    default:
                $aditionalPaths = ['actividades' => 'actividades/view'];
                $oView = new ViewTwig('actividades/controller',$aditionalPaths);
                return $oView->render('actividad_tipo_que.html.twig',$a_campos);
		}
	}

	public function setPerm_jefe($perm_jefe) {
		$this->bperm_jefe = $perm_jefe;
	}

	public function setSfsvAll($bAll=FALSE) {
		$this->bAll = $bAll;
	}

	public function setSfsv($ssfsv) {
		$this->ssfsv = $ssfsv;
	}

	public function setAsistentes($sasistentes) {
		$this->sasistentes = $sasistentes;
	}

	public function setActividad($sactividad) {
		$this->sactividad = $sactividad;
	}

	public function setNom_tipo($snom_tipo) {
		$this->snom_tipo = $snom_tipo;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function setQue($que) {
		$this->que = $que;
	}

	public function setId_tipo_activ($id_tipo_activ) {
		$this->id_tipo_activ = $id_tipo_activ;
	}

	public function setPara($para='actividades') {
	    $this->para = $para;
	}

}