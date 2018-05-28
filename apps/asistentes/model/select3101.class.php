<?php
namespace asistentes\model;

use actividades\model\entity as actividades;
use actividadcargos\model\entity as actividadcargos;
use asistentes\model\entity as asistentes;
use dossiers\model\entity as dossiers;
use personas\model\entity as personas;
use ubis;
use core;
use web;

/**
 * Esta página muestra una tabla con los asistentes de una actividad.
 * Primero los miembros del cl y después el resto.
 *  Con los botones de:
 *			modificar y borrar asistencia.
 *			añadir, modificar y quitar cargo.
 *			plan de estudios
 *			transferir a históricos.
 *  En el caso de ser "des" o "vcsd" al quitar cargo, también elimino la asistencia.
 * abajo se añaden los botones para añadir una nueva persona.
 *
 * OJO Está como include de dossiers_ver.php
 *
 * @package	delegacion
 * @subpackage	actividades
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @ajax		23/8/2007.
 * @version 1.0
 * @created 23/09/2010
 *		
 * @param integer $_POST['obj_pau']  Se pasa a otras páginas.
 */

class Select3101 {

	// ------ de uso interno
	
	/**
	 * array con los permisos (si o no) para añadir las personas (agd, n...) 
	 * según el tipo de actividad de que se trate y quién seamos nosotros.
	 * 
	 * @var array $ref_perm
	 */
	private  $a_ref_perm;
	/* @var $msg_err string */
	private $msg_err;
	/* @var $a_valores array */
	private $a_valores;

	private $a_asistentes;
	private $mi_dele;
	private $plazas_txt;
	private $plazas_totales;
	private $id_tipo_activ;
	private $dl_org;
	private $id_ubi;
	private $num; // numero ordinal de asistencias
	private $aListaCargos;

	private $publicado;
	private $leyenda_html;
	private $resumen_plazas;
	private $resumen_plazas2;
	private $aLinks_dl;
	
	private $a_plazas_resumen;
	private $a_plazas_conseguidas;

	/**
	 * Para pasar a la vista, aparece como alerta antes de ejecutarse
	 * @var string $txt_eliminar
	 */
	private $txt_eliminar;
	/* @var $bloque string  necesario para el script*/
	private $bloque;
	
	// ---------- Variables requeridas
	/* @var $queSel integer */
	private $queSel;
	/* @var $id_dossier integer */
	private $id_dossier;
	/* @var $pau string */
	private $pau;
	/* @var $obj_pau string */
	private $obj_pau;
	/* @var $id_pau integer */
	private $id_pau;
	/**
	 * 3: para todo, 2, 1:solo lectura
	 * @var integer $permiso
	 */
	private $permiso;

	// ------ Variables para mantener la selección de la grid al volver atras
	private $Qid_sel;
	private $Qscroll_id;
	
	public function __construct() {
	}

	private function incrementa (&$var){
		if (empty($var)) {
			$var = 1;
		} else {
			$var++;
		}
	}

	private function getBotones() {
		if (core\configGlobal::is_app_installed('asistentes')) {
			$a_botones[] = array( 'txt' => _('modificar asistencia'),
								'click' =>"fnjs_modificar(this.form)"
							);
			$a_botones[] = array( 'txt' => _('cambiar actividad'),
								'click' =>"fnjs_mover(this.form,$this->id_pau)" 
							);
			$a_botones[] = array( 'txt' => _('borrar asistencia'),
								'click' =>"fnjs_borrar(this.form)" 
							);
			$a_botones[] = array( 'txt' => _("transferir a históricos"),
								'click'=>"fnjs_transferir(this.form)"
							);
		}
		if (core\configGlobal::is_app_installed('actividadcargos')) {
			$a_botones[] = array( 'txt' => _('añadir cargo'),
									'click' =>"fnjs_add_cargo(this.form)" 
							);
			$a_botones[] = array( 'txt' => _('modificar cargo'),
									'click' =>"fnjs_mod_cargo(this.form)" 
							);
			$a_botones[] = array( 'txt' => _('quitar cargo'),
									'click' =>"fnjs_borrar_cargo(this.form)" 
							);
		}
		if (core\configGlobal::is_app_installed('actividadestudios')) {
			$a_botones[] = array( 'txt' => _('plan estudios'),
									'click' =>"fnjs_matriculas(this.form,\"#frm_matriculas\")" 
							);
			$a_botones[] = array( 'txt' => _('E43'),
									'click' =>"fnjs_e43(this.form)" 
							);
		}

		return $a_botones;
	}

	private function getCabeceras() {
		$a_cabeceras=array( array('name'=>_("num"),'width'=>40),
							array('name'=>_("nombre y apellidos"),'width'=>300),
							array('name'=>_("propio"),'width'=>40),
							array('name'=>_("est. ok"),'width'=>40),
							array('name'=>_("falta"),'width'=>40),
							array('name'=>_("observ."),'width'=>150)
						);
		return $a_cabeceras;
	}


	private function getDatosActividad() {
		$oActividad = new actividades\Actividad($this->id_pau);
		$this->id_tipo_activ = $oActividad->getId_tipo_activ();
		$this->dl_org = $oActividad->getDl_org();
		$this->plazas_totales = $oActividad->getPlazas();
		$this->id_ubi = $oActividad->getId_ubi();
		$this->publicado = $oActividad->getPublicado();
		
	}

	private function getTituloPlazas() {
		if (empty($this->plazas_totales)) {
			$oCasa = ubis\model\entity\Ubi::NewUbi($this->id_ubi);
			// A veces por error se puede poner una actividad a un ctr...
			if (method_exists($oCasa,'getPlazas')) {
				$plazas_max = $oCasa->getPlazas();
				$plazas_min = $oCasa->getPlazas_min();
			} else {
				$plazas_max = '';
				$plazas_min = '';
			}
			$plazas_txt = _("Plazas casa (max - min)").": ";
			$plazas_txt .= !empty($plazas_max)? $plazas_max : '?';
			$plazas_txt .= !empty($plazas_min)? ' - '.$plazas_min : '';
		} else {
			$plazas_txt = _("Plazas actividad").": ";
			$plazas_txt .= !empty($this->plazas_totales)? $this->plazas_totales : '?';
		}
		$this->plazas_txt = $plazas_txt;
	}

	/**
	 * Genera:
	 * $this->a_plazas_conseguidas
	 * $this->a_pazas_resumen
	 */
	private function contarPlazas() {
		$a_plazas_resumen = array();
		$a_plazas_conseguidas = array();
		// Si no esta publicada todas las plazas de la actividad son para la dl.
		// No hay plazas de calendario.
		if ($this->publicado === false) {
			$dl = $this->dl_org;
			$a_plazas_resumen[$dl]['calendario'] = $this->plazas_totales;
			$a_plazas_resumen[$dl]['conseguidas'] = 0;
			$a_plazas_resumen[$dl]['disponibles'] = $this->plazas_totales;
			$a_plazas_resumen[$dl]['total_cedidas'] = 0;
		} else {
			// array para pasar id_dl a dl.
			$gesDelegacion = new ubis\model\entity\GestorDelegacion();
			$a_dl = $gesDelegacion->getArrayDelegaciones(array("H"));
			//print_r($a_dl);
			
			$gesActividadPlazasR = new \actividadplazas\model\entity\GestorResumenPlazas();
			$gesActividadPlazasR->setId_activ($this->id_pau);
			
			$gesActividadPlazas = new \actividadplazas\model\entity\GestorActividadPlazas();
			$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_activ'=>  $this->id_pau));
			$a_plazas_resumen =array();
			foreach ($cActividadPlazas as $oActividadPlazas) {
				$dl_tabla = $oActividadPlazas->getDl_tabla();
				$id_dl = $oActividadPlazas->getId_dl();
				$json_cedidas = $oActividadPlazas->getCedidas();
				$dl = $a_dl[$id_dl];
				$calendario = $gesActividadPlazasR->getPlazasCalendario($dl);
				//if (empty($calendario)) { continue; }
				$a_plazas_resumen[$dl]['calendario'] = $gesActividadPlazasR->getPlazasCalendario($dl);
				$a_plazas_resumen[$dl]['conseguidas'] = $gesActividadPlazasR->getPlazasConseguidas($dl);
				$a_plazas_resumen[$dl]['disponibles'] = $gesActividadPlazasR->getPlazasDisponibles($dl);
				$a_plazas_resumen[$dl]['total_cedidas'] = $gesActividadPlazasR->getPlazasCedidas($dl);
				if ($this->dl_org == $dl_tabla) {
					// las cedidas se guardan en la tabla que pertenece a la dl
					if($dl === $this->dl_org) {
						if (!empty($json_cedidas)){
							//$aCedidas = json_decode($json_cedidas,TRUE);
							//$a_plazas_resumen[$dl]['cedidas'] = $aCedidas;
							$a_plazas_resumen[$dl]['json_cedidas'] = $json_cedidas;
						} else {
							//$a_plazas_resumen[$dl]['cedidas'] = array();
							$a_plazas_resumen[$dl]['json_cedidas'] = array();
						}
					}
				} else {
					if (!empty($json_cedidas)){
						//$aCedidas = json_decode($json_cedidas,TRUE);
						//$a_plazas_resumen[$dl]['cedidas'] = $aCedidas;
						$a_plazas_resumen[$dl]['json_cedidas'] = $json_cedidas;
					} else {
						//$a_plazas_resumen[$dl]['cedidas'] = array();
						$a_plazas_resumen[$dl]['json_cedidas'] = array();
					}
				}
				if (!empty($json_cedidas)){
					$aCedidas = json_decode($json_cedidas,TRUE);
					foreach ($aCedidas as $dl2 => $num) {
						$a_plazas_conseguidas[$dl2][$dl]['cedidas'] = $num;
					}
				}
			}
		}
		ksort($a_plazas_resumen);
		$this->a_plazas_resumen = $a_plazas_resumen;
		$this->a_plazas_conseguidas = $a_plazas_conseguidas;
	}


	/**
	 * Establece
	 *		$this->num = $num;
	 *		$this->a_valores = $a_valores;
	 * Incrementa:
	 *		$this->a_plazas_conseguidas
	 *		$this->a_pazas_resumen
	 */
	public function getCargos() {
		// Permisos según el tipo de actividad
		$oPermDossier = new dossiers\PermDossier();
		$this->a_ref_perm = $oPermDossier->perm_pers_activ($this->id_tipo_activ);

		// primero el cl:
		// primero los cargos
		$gesAsistentes = new asistentes\GestorAsistente();
		$c=0;
		$num=0;
		$a_valores=array();
		$this->aListaCargos=array();
		$GesCargosEnActividad=new actividadcargos\GestorActividadCargo();
		$cCargosEnActividad = $GesCargosEnActividad->getActividadCargos(array('id_activ'=>  $this->id_pau));
		$mi_sfsv = core\ConfigGlobal::mi_sfsv();
		foreach($cCargosEnActividad as $oActividadCargo) {
			$c++;
			$num++; // número total de asistentes.
			$id_item_cargo=$oActividadCargo->getId_item();
			$id_nom=$oActividadCargo->getId_nom();
			$this->aListaCargos[]=$id_nom;
			$id_cargo=$oActividadCargo->getId_cargo();
			$oCargo = new actividadcargos\Cargo(array('id_cargo'=>$id_cargo));
			$tipo_cargo=$oCargo->getTipo_cargo();		
			// para los sacd en sf
			if ($tipo_cargo == 'sacd' && $mi_sfsv == 2) {
				continue;
			}

			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$this->msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
				continue;
			}
			$oCargo=new actividadcargos\Cargo($id_cargo);

			$nom=$oPersona->getApellidosNombre();

			$cargo=$oCargo->getCargo();
			$puede_agd=$oActividadCargo->getPuede_agd();
			$observ=$oActividadCargo->getObserv();
			$ctr_dl=$oPersona->getCentro_o_dl();
			// permisos (añado caso de cargo sin nombre = todos permiso)
			if ($id_tabla=$oPersona->getId_tabla()) {
				$a_act=  $this->a_ref_perm[$id_tabla];
				if ($a_act["perm"]) { $this->permiso = 3; } else { $this->permiso = 1; }
			} else {
				$this->permiso = 3;
			}

			// ahora miro si también asiste:
			$plaza = asistentes\Asistente::PLAZA_PEDIDA ;
			$aWhere=array('id_activ'=>  $this->id_pau,'id_nom'=>$id_nom);
			$aOperador=array('id_activ'=>'=','id_nom'=>'=');
			// me aseguro de que no sea un cargo vacio (sin id_nom)
			if (!empty($id_nom) && $cAsistente=$gesAsistentes->getAsistentes($aWhere,$aOperador)) {
				if(is_array($cAsistente) && count($cAsistente)>1) {
					$tabla = '';
					foreach ($cAsistente as $Asistente) {
						$tabla .= "<li>".$Asistente->getNomTabla()."</li>";
					}
					$msg_err = "ERROR: más de un asistente con el mismo id_nom<br>";
					$msg_err .= "<br>$nom(".$oPersona->getId_tabla().")<br><br>En las tablas:<ul>$tabla</ul>";
					exit ("$msg_err");
				}
				$oAsistente = $cAsistente[0];
				$propio=$oAsistente->getPropio();
				$falta=$oAsistente->getFalta();
				$est_ok=$oAsistente->getEst_ok();
				$observ1=$oAsistente->getObserv();
				$plaza= empty($oAsistente->getPlaza())? asistentes\Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();

				// contar plazas
				if (core\configGlobal::is_app_installed('actividadplazas')) {
					// las cuento todas y a la hora de enseñar miro si soy la dl org o no.
					// propiedad de la plaza:
					$propietario = $oAsistente->getPropietario();
					$padre = strtok($propietario,'>');
					$child = strtok('>');
					$dl = $child;
					//si es de otra dl no distingo cedidas.
					// no muestro ni cuento las que esten en estado distinto al asignado o confirmado (>3)
					if ($padre != $this->mi_dele) {
						if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) {
							$this->incrementa($this->a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
							if (!empty($child) && $child != $padre) {
								$this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
							}
						} else {
							if (!empty($child) && $child == $this->mi_dele) {
								$this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
							}elseif (!empty($padre)) {
								continue;
							}
						}
					} else {  // En mi dl distingo las cedidas
						// si no es de (la dl o de paso ) y no tiene la plaza asignada o confirmada no lo muestro
						if ($child != $this->mi_dele) {
							if ($plaza < asistentes\Asistente::PLAZA_ASIGNADA) {
								continue;
							} else {
								$this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
								$this->incrementa($this->a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
							}
						} else {
							$this->incrementa($this->a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
						}
					}
				}

				$puede_agd=='t' ? $chk_puede_agd="si" : $chk_puede_agd="no" ;

				if ($propio=='t') {
					$chk_propio=_("si");
					// Para los de des, elimino el cargo y la asistencia. Para el resto, sólo el cargo (no la asistencia).
					if (($_SESSION['oPerm']->have_perm("des")) or ($_SESSION['oPerm']->have_perm("vcsd"))) {
						$eliminar = 2;
					} else {
						$eliminar = 1;
					}
				} else { 
					$chk_propio=_("no") ;
					$eliminar = 2;  //si no es propio, al eliminar el cargo, elimino la asistencia
				}
				$falta=='t' ? $chk_falta=_("si") : $chk_falta=_("no") ;
				$est_ok=='t' ? $chk_est_ok=_("si") : $chk_est_ok=_("no") ;
				$asis="t";
				
				if ($this->permiso == 3) {
					$a_valores[$c]['sel']="$id_nom#$id_item_cargo#$eliminar";
				} else {
					$a_valores[$c]['sel']="";
				}
				$a_valores[$c][3]=$chk_propio;
				$a_valores[$c][4]=$chk_est_ok;
				$a_valores[$c][5]=$chk_falta;
			} else {
				$a_valores[$c][3]= array( 'span'=>3, 'valor'=> _("no asiste"));
				$observ1='';
				$num--;
				$asis="f";
			}

			if(!empty($plaza)) {
				$a_valores[$c]['clase']='plaza'.$plaza;
			} else {
				$a_valores[$c]['clase']='plaza1';
			}
				
			$a_valores[$c][1]=$cargo;
			$a_valores[$c][2]="$nom  ($ctr_dl)";
			$a_valores[$c][6]="$observ $observ1";
		}
		
		$this->num = $num;
		$this->a_valores = $a_valores;
	}

	/**
	 * Establece:
	 * 		$a_asistentes
	 * Incrementa las propiedades:
	 * $this->a_plazas_resumen
	 * $this->a_plazas_conseguidas
	 * 
	 */
	public function getAsistentes() {
		$gesAsistentes = new asistentes\GestorAsistente();
		$this->a_asistentes = array();
		$cAsistentes = $gesAsistentes->getAsistentes(array('id_activ'=>  $this->id_pau));
		foreach($cAsistentes as $oAsistente) {
			$this->num++;
			$id_nom=$oAsistente->getId_nom();
			// si ya está en la lista voy a por otro asistente
			if(in_array($id_nom,  $this->aListaCargos)) { $this->num--; continue; }

			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$this->msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
				continue;
			}
			$nom=$oPersona->getApellidosNombre();
			//$dl=$oPersona->getDl();
			$ctr_dl=$oPersona->getCentro_o_dl();

			$propio=$oAsistente->getPropio();
			$falta=$oAsistente->getFalta();
			$est_ok=$oAsistente->getEst_ok();
			$observ=$oAsistente->getObserv();
			$plaza = asistentes\Asistente::PLAZA_PEDIDA;
			
			// contar plazas
			//if (core\configGlobal::is_app_installed('actividadplazas') && !empty($dl)) {
			if (core\configGlobal::is_app_installed('actividadplazas')) {
				$plaza= empty($oAsistente->getPlaza())? asistentes\Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();
				// las cuento todas y a la hora de enseñar miro si soy la dl org o no.
				// propiedad de la plaza:
				$propietario = $oAsistente->getPropietario();
				$padre = strtok($propietario,'>');
				$child = strtok('>');
				$dl = $child;
				//si es de otra dl no distingo cedidas.
				// no muestro ni cuento las que esten en estado distinto al asignado o confirmado (>3)
				if ($padre != $this->mi_dele) {
					if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) {
						$this->incrementa($this->a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
						if (!empty($child) && $child != $padre) {
							$this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
							//$this->incrementa($this->a_plazas_resumen[$child]['ocupadas'][$dl][$plaza]);
						} else {
							//$this->incrementa($this->a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
						}
					} else {
						if (!empty($child) && $child == $this->mi_dele) {
							$this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
						}elseif (!empty($padre)) {
							continue;
						}
					}
				} else {  // En mi dl distingo las cedidas
					// si no es de (la dl o de paso ) y no tiene la plaza asignada o confirmada no lo muestro
					if ($child != $this->mi_dele) {
						if ($plaza < asistentes\Asistente::PLAZA_ASIGNADA) {
							continue;
						} else {
							$this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
							$this->incrementa($this->a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
						}
					} else {
						$this->incrementa($this->a_plazas_resumen[$padre]['ocupadas'][$dl][$plaza]);
					}
				}
			}

			if ($propio=='t') {
				$chk_propio=_("si");
			} else { 
				$chk_propio=_("no") ;
			}
			$falta=='t' ? $chk_falta=_("si") : $chk_falta=_("no") ;
			$est_ok=='t' ? $chk_est_ok=_("si") : $chk_est_ok=_("no") ;
			
			if ($this->permiso == 3) {
				$a_val['sel']="$id_nom";
			} else {
				$a_val['sel']="";
			}
			
			$a_val['clase']='plaza1';
			if(!empty($plaza)) {
				$a_val['clase']='plaza'.$plaza;
			}
					
			$a_val[2]="$nom  ($ctr_dl)";
			$a_val[3]=$chk_propio;
			$a_val[4]=$chk_est_ok;
			$a_val[5]=$chk_falta;
			$a_val[6]=$observ;
			
			$this->a_asistentes[$nom] = $a_val;
		}
		uksort($this->a_asistentes,"core\strsinacentocmp");

	}

	/**
	 * Establece los textos:
	 *	$this->leyenda_html
	 *	$this->resumen_plazas
	 *	$this->resumen_plazas2
	 * 
	 * Incrementa
	 * $this->a_plazas_resumen
	 * $this->a_asistentes
	 * 
	 */
	public function getLeyenda() {
		//leyenda colores
		$leyenda_html = '';
		// resumen plazas
		$disponibles ='';
		$resumen_plazas = '';
		$resumen_plazas2 = '';
		if (core\configGlobal::is_app_installed('actividadplazas')) {
			//leyenda colores
			$explicacion1 = _("plaza que contabiliza pero que las otras delegaciones no ven. Podría explicarse como una plaza que se desea pero no se puede conceder porque no hay sitio.");
			$explicacion2 = _("como la plaza pedida, pero cuando ya se ha solicitado a la otra delegación que nos conceda ese plaza. Implica que por nuestra parte nos parece correcto que vaya pero necesitamos confirmación de que hay sitio.");
			$explicacion4 = _("plaza ocupada en toda regla. Las delegaciones organizadoras ven a los nuestros. Si somos nosotros los organizadores, podemos ocupar más plazas de las previstas. Si son de otra delegación, no debería poder pasar a asignada si no hay plazas.");
			$explicacion5 = _("como la anterior pero con el plus de que se ha comunicado al interesado y no hay cambio.");
			
			$leyenda_html = '<p class="contenido">';
			$leyenda_html .= _("Para seleccionar varios: 'Ctrl+Click' o bien 'Mays+Click'");
			$leyenda_html .= "<br><style>
				.box {
				display: inline;
				height: 1em;
				line-height: 3;
				padding: 0.3em;
				border-style: outset;
				cursor: pointer;
				}
				</style>
				";
			$oGesAsistente = new asistentes\GestorAsistente();
			$aOpciones = $oGesAsistente->getOpcionesPosiblesPlaza();
			foreach ($aOpciones as $plaza => $plaza_txt) {
				$expl = "explicacion$plaza";
				$explicacion = $$expl;
				// No se puede poner 'this.form' com formulario, porque <div> no es un elemento de formulario
				$leyenda_html .= "<div class='box plaza$plaza' onCLick=fnjs_cmb_plaza('#frm_3101','$plaza') title='$explicacion'>$plaza_txt</div>  ";
			}
			$leyenda_html .= "</p>";
			////////////////////////////////////////////////////////////////////
			// Si no está publicada no hace falta el resumen de plazas
			if ($this->publicado === true) {
				if (array_key_exists($this->mi_dele, $this->a_plazas_resumen)) {
					foreach ($this->a_plazas_resumen as $padre => $aa) {
						if ($padre != $this->mi_dele && $this->mi_dele != $this->dl_org) {	continue; }
						$calendario = empty($aa['calendario'])? 0 : $aa['calendario']; // calendario.
						$conseguidas = empty($aa['conseguidas'])? 0 : $aa['conseguidas']; // conseguidas.
						$total_cedidas = empty($aa['total_cedidas'])? 0 : $aa['total_cedidas'];
						$disponibles = empty($aa['disponibles'])? 0 : $aa['disponibles'];
						$json_cedidas = empty($aa['json_cedidas'])? '' : $aa['json_cedidas'];
						$total = $calendario + $conseguidas;
						$aCed = array();
						if (!empty($json_cedidas)){
							$aCed = json_decode($json_cedidas,TRUE);
						}
						$decidir = 0;
						$espera = 0;
						$ocupadas = 0;
						$resumen_plazas .= "$padre: " 	;
						// ocupadas por la dl padre
						$plazas = empty($aa['ocupadas'][$padre])? array() : $aa['ocupadas'][$padre];
						$ocupadas_dl = 0;
						foreach ($plazas as $plaza => $num) {
							if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir = $num; }
							if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera = $num; }
							if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_dl += $num; }
						}
						$ocu_padre = $ocupadas_dl;
						$ocupadas += $ocupadas_dl;
						$resumen_plazas .= 	"$ocupadas_dl($padre)";

						// ocupadas por las dl cedidas
						$i = 0;
						foreach ($aCed as $dl2 => $numCedidas) {
							$plazas = empty($aa['ocupadas'][$dl2])? array() : $aa['ocupadas'][$dl2];
							$i++;
							$ocupadas_dl = 0;
							foreach ($plazas as $plaza => $num) {
								if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir = $num; }
								if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera = $num; }
								if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_dl += $num; }
							}
							$ocupadas += $ocupadas_dl;
							$resumen_plazas .= " + ";
							$resumen_plazas .= 	"$ocupadas_dl($dl2)";
							$this->a_plazas_resumen[$padre]['cedidas'][$dl2] = array('ocupadas' => $ocupadas_dl);
							// pongo los de otras dl, que todavia no estan asignados como genéricos:
							if ($this->mi_dele != $dl2 && $dl2 != $this->dl_org) {
								$pl = empty($aCed[$dl2])? 0 : $aCed[$dl2];
								if (!array_key_exists($dl2, $this->a_plazas_resumen)) {
									for ($i=$ocupadas_dl+1; $i <= $pl ;$i++ ) {
										$nom = "$dl2----$i";
										$a_val['sel'] = '';
										$a_val['clase'] = 'plaza4';
										$a_val[2] = $nom;
										$a_val[3] = ''; 
										$a_val[4] = ''; 
										$a_val[5] = ''; 
										$a_val[6] = ''; 
										
										$this->a_asistentes[$nom] = $a_val;
									}
									//$pl_relleno[$dl2] = $i-1;
								}
								$pl_relleno[$dl2] = $pl-$ocupadas_dl;
							}
						}
						// Conseguidas	
						if (array_key_exists($padre, $this->a_plazas_conseguidas)) {
							$a_dl_plazas = $this->a_plazas_conseguidas[$padre];
							//$decidir = 0;
							//$espera = 0;
							$ocupadas_otra = 0;
							// ocupadas por la dl padre
							foreach ($a_dl_plazas as $dl3 => $pla) {
								$plazas = empty($pla['ocupadas'])? array() : $pla['ocupadas'];
								$pla['cedidas'] = empty($pla['cedidas'])? '?' : $pla['cedidas'];
								foreach ($plazas as $dl => $pl) {
									foreach ($pl as $plaza => $num) {
										if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir += $num; }
										if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera += $num; }
										if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_otra += $num; }
									}
									if (!empty($ocupadas_otra)) { $resumen_plazas .= " + "; }
									$txt = sprintf(_("(de las %s cedidas por %s)"),$pla['cedidas'],$dl3);
									$resumen_plazas .= $ocupadas_otra." ".$txt;
								}
							}
							$ocupadas += $ocupadas_otra;
							$ocu_padre += $ocupadas_otra;
						}

						$resumen_plazas .= 	"  => "._("ocupadas")."=$ocupadas/($total)";
						if (!empty($json_cedidas)) { $resumen_plazas .= " "._("cedidas")."=$total_cedidas $json_cedidas"; }
						$libres = $disponibles - $ocu_padre;
						if (($libres < 0)) {
							$resumen_plazas .= 	"<span style='background-color: red'> disponibles= $libres</span>";
						} else {
							$resumen_plazas .= 	" disponibles=$libres";
						}
						if ($this->mi_dele == $padre) {
							if (!empty($espera)) { $resumen_plazas .= " ".sprintf(_("(%s en espera)"),$espera); }
							if (!empty($decidir)) { $resumen_plazas .= " ".sprintf(_("(%s por decidir)"),$decidir); }
						}
						$resumen_plazas .= ";<br>";
						// pongo los de otras dl, que todavia no estan asignados como genéricos:
						if ($this->mi_dele != $padre && $padre != $this->dl_org) {
							$ocu_relleno = $total - $libres;
							for ($i=$ocu_relleno+1; $i <= $total ;$i++ ) {
								$nom = "$padre-$i";
								$a_val['sel'] = '';
								$a_val['clase'] = 'plaza4';
								$a_val[2] = $nom;
								$a_val[3] = ''; 
								$a_val[4] = ''; 
								$a_val[5] = ''; 
								$a_val[6] = ''; 
								
								$this->a_asistentes[$nom] = $a_val;
							}
						}
					}
				} elseif (array_key_exists($this->mi_dele, $this->a_plazas_conseguidas)) {  // No es una dl organizadora/colaboradora
					$a_dl_plazas = $this->a_plazas_conseguidas[$this->mi_dele];
					$decidir = 0;
					$espera = 0;
					$ocupadas_dl = 0;
					// ocupadas por la dl padre
					$resumen_plazas2 = "$this->mi_dele: ";
					foreach ($a_dl_plazas as $dl2 => $pla) {
						$plazas = empty($pla['ocupadas'])? array() : $pla['ocupadas'];
						$pla['cedidas'] = empty($pla['cedidas'])? '?' : $pla['cedidas'];
						foreach ($plazas as $dl => $pl) {
							foreach ($pl as $plaza => $num) {
								if ($plaza == asistentes\Asistente::PLAZA_PEDIDA) { $decidir += $num; }
								if ($plaza == asistentes\Asistente::PLAZA_EN_ESPERA) { $espera += $num; }
								if ($plaza > asistentes\Asistente::PLAZA_DENEGADA) { $ocupadas_dl += $num; }
							}
							$txt = sprintf(_("(de las %s cedidas por %s)"),$pla['cedidas'],$dl2);
							$resumen_plazas2 .= $ocupadas_dl." ".$txt;
							if (!empty($espera)) { $resumen_plazas2 .= " ".sprintf(_("(%s en espera)"),$espera); }
							if (!empty($decidir)) { $resumen_plazas2 .= " ".sprintf(_("(%s por decidir)"),$decidir); }
						}
					}
					$resumen_plazas2 .= ";<br>";
				}
			}
		}
		$this->leyenda_html = $leyenda_html;
		$this->resumen_plazas = $resumen_plazas;
		$this->resumen_plazas2 = $resumen_plazas2;
	}

	public function getValores() {
		return $this->a_valores;
	}
	
	public function getTabla() {
		if (core\configGlobal::is_app_installed('actividadcargos')) {
			$this->getCargos();
			$c = count($this->a_valores);
		} else {
			$c = 0;
			$this->num = 0;
			$this->a_valores = array();
		}

		$this->getAsistentes();
		$this->getLeyenda();

		$n = $c;
		foreach ($this->a_asistentes as $nom => $val) {
			$c++;
			$val[1] = "-";
			// sólo numero los asignados y confirmados
			if (core\configGlobal::is_app_installed('actividadplazas')) {
				if ($val['clase'] == 'plaza4' || $val['clase'] == 'plaza5') {
					$n++;
					$val[1] = "$n.-";
				}
			} else {
				$n++;
				$val[1] = "$n.-";
			}
			// Los añado a los cargos
			$this->a_valores[$c] = $val;
		}
		if (!empty($this->a_valores)) {
			// Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
			// las pongo al final, porque al contar los valores del array se despista.
			if (!empty($this->Qid_sel)) { $this->a_valores['select'] = $this->Qid_sel; }
			if (!empty($this->Qscroll_id)) { $this->a_valores['scroll_id'] = $this->Qscroll_id; }
		}
	}


	public function getHtml() {
		$this->msg_err = '';
		$this->txt_eliminar = _("¿Esta Seguro que desea borrar a esta persona de esta actividad?");
	
		if (core\configGlobal::is_app_installed('actividadplazas')) {
			$this->contarPlazas();
		}
		$this->getTabla(); // antes debe estar el contarPlazas

		$oTabla = new web\Lista();
		$oTabla->setId_tabla('sql_3101');
		$oTabla->setCabeceras($this->getCabeceras());
		$oTabla->setBotones($this->getBotones());
		$oTabla->setDatos($this->getValores());

		
		$oHash = new web\Hash();
		$oHash->setcamposForm('');
		$oHash->setCamposNo('sel!scroll_id!mod!que!refresh');
		/*
		$a_camposHidden = array(
				'pau' => $this->pau,
				'id_pau' => $this->id_pau,
				'obj_pau' => $this->obj_pau,
				'id_dossier' => $this->id_dossier,
				'queSel' => $this->queSel,
				'permiso' => 3,
				);
		 * */
		$a_camposHidden = array(
				'pau' => $this->pau,
				'id_pau' => $this->id_pau,
				'obj_pau' => $this->obj_pau,
				'id_dossier' => $this->id_dossier,
				'queSel' => $this->queSel,
				'permiso' => 3,
				);
		 
		$oHash->setArraycamposHidden($a_camposHidden);

		// para el hash de las matrículas. Hago otro formulario, pues cambio demasiadas cosas
		$oHash1 = new web\Hash();
		$oHash1->setcamposForm('');
		$oHash1->setCamposNo('sel!scroll_id!mod');
		$a_camposHidden = array(
				'queSel' => 'matriculas',
				'pau' => 'p',
				'id_pau' => $this->id_pau,
				'obj_pau' => 'Persona',
				'id_dossier' => 1303,
				'permiso' => 3,
				);
		$oHash1->setArraycamposHidden($a_camposHidden);

		$url = core\ConfigGlobal::getWeb()."/apps/dossiers/controller/dossiers_ver.php";
		$oHash2 = new web\Hash();
		$oHash2->setUrl($url);
		$oHash2->setCamposForm('depende!pau!obj_pau!id_pau!id_dossier!permiso'); 
		$h = $oHash2->linkSinVal();
		$pagina = "depende=1&pau=a&obj_pau=Actividad&id_pau=$this->id_pau&id_dossier=3101&permiso=3$h";

		$oHash3 = new web\Hash();
		$oHash3->setUrl(core\ConfigGlobal::getWeb()."/apps/asistentes/controller/form_mover.php");
		$oHash3->setCamposForm('id_pau!id_activ'); 
		$h3 = $oHash3->linkSinVal();

		$oHash4 = new web\Hash();
		$oHash4->setUrl(core\ConfigGlobal::getWeb()."/apps/asistentes/controller/update_3101.php");
		$oHash4->setCamposForm('mod!plaza!lista_json!id_activ'); 
		$h4 = $oHash4->linkSinVal();

		$this->setLinksInsert();

		/* ---------------------------------- html --------------------------------------- */
		$a_campos = ['oTabla' => $oTabla,
					'oHash' => $oHash,
					'id_pau' => $this->id_pau,
					'h4' => $h4,
					'h3' => $h3,
					'oTabla' => $oTabla,
					'oHash1' => $oHash1,
					'plazas_txt' => $this->plazas_txt,
					'resumen_plazas' => $this->resumen_plazas,
					'resumen_plazas2' => $this->resumen_plazas2,
					'leyenda_html' => $this->leyenda_html,
					'aLinks_dl' => $this->aLinks_dl,
					'msg_err' => $this->msg_err,
					'txt_eliminar' => $this->txt_eliminar,
					'bloque' => $this->bloque,
					];
		
		$oView = new core\View(__NAMESPACE__);
		
		return $oView->render('select3101.phtml',$a_campos);

	}
	
	public function setLinksInsert() {
		$this->aLinks_dl = array();
		$ref_perm = $this->a_ref_perm;
		if (empty($ref_perm) OR $this->permiso < 2) { // si es nulo, no tengo permisos de ningún tipo
			return '';
		}
		$mi_dele = core\ConfigGlobal::mi_dele();
		reset($ref_perm);
		foreach ($ref_perm as $clave =>$val) {
			$permis=$val["perm"];
			$obj_pau=$val["obj"];
			$nom=$val["nom"];
			if (!empty($permis)) {
				$aQuery = array('mod'=>'nuevo',
								'que_dl'=>$mi_dele,
								'pau'=>  $this->pau,
								'obj_pau'=>  $obj_pau,
								'id_dossier'=>  $this->id_dossier, //Para que al volver a la pagina 'dossiers_ver' sepa cual mostrar.
								'id_pau'=>  $this->id_pau);
				$pagina=web\Hash::link('apps/asistentes/controller/form_3101.php?'.http_build_query($aQuery));
				$this->aLinks_dl[$nom] = $pagina;
			}
		}
	}
		
	
	public function getId_dossier() {
		return $this->id_dossier;
	}

	public function getPau() {
		return $this->pau;
	}

	public function getObj_pau() {
		return $this->obj_pau;
	}

	public function getId_pau() {
		return $this->id_pau;
	}

	public function getPermiso() {
		return $this->permiso;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setId_dossier($Qid_dossier) {
		$this->id_dossier = $Qid_dossier;
	}

	public function setPau($Qpau) {
		$this->pau = $Qpau;
	}

	public function setObj_pau($Qobj_pau) {
		$this->obj_pau = $Qobj_pau;
	}

	public function setId_pau($Qid_pau) {
		$this->id_pau = $Qid_pau;
		$this->mi_dele = core\ConfigGlobal::mi_dele();
		$this->getDatosActividad();
	}

	public function setPermiso($Qpermiso) {
		$this->permiso = $Qpermiso;
	}

	public function setStatus($Qstatus) {
		$this->status = $Qstatus;
	}

	public function setQid_sel($Qid_sel) {
		$this->Qid_sel = $Qid_sel;
	}

	public function setQscroll_id($Qscroll_id) {
		$this->Qscroll_id = $Qscroll_id;
	}
	public function setBloque($bloque) {
		$this->bloque = $bloque;
	}
	public function setQueSel($queSel) {
		$this->queSel = $queSel;
	}


}
		