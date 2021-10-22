<?php
use encargossacd\model\EncargoConstants;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoSacdObserv;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoHorario;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use encargossacd\model\entity\GestorEncargoSacdObserv;
use encargossacd\model\entity\GestorEncargoTipo;
use personas\model\entity\GestorPersonaDl;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;

/**
* Esta página muestra los encargos de un sacd. 
*
*@package	delegacion
*@subpackage	des
*@author	Daniel Serrabou
*@since		12/12/06.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');

$oEncargoTipo  = new EncargoTipo();

$oF_hoy = new DateTimeLocal(date('Y-m-d')); //Hoy sólo fecha, no hora
$hoy = $oF_hoy->getIso();

// Para las funciones
$GesEncargoTipo = new GestorEncargoTipo();

switch ($Qque) {
    case 'get_select':
        $Qfiltro_sacd = (string) \filter_input(INPUT_POST, 'filtro_sacd');

		$sdonde = "AND id_tabla='$Qfiltro_sacd' ";
		$GesPersonas = new GestorPersonaDl();
		$DesplPersonas = $GesPersonas->getListaSacd($sdonde);
		$DesplPersonas->setOpcion_sel($Qid_nom);
		$DesplPersonas->setNombre('lst_sacds');
		$DesplPersonas->setAction('fnjs_ver_ficha()');
		
		echo ucfirst(_("sacd")).':&nbsp;&nbsp;&nbsp;';
		echo $DesplPersonas->desplegable();
		break;
	case 'ficha':
		// permiso para sf:
		if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
			$permiso=1;
		} else {
			$permiso=0;
		}

		// busco las observaciones (si las hay). Doy por supuesto que sólo hay una.
		$GesEncargoSacdObserv = new GestorEncargoSacdObserv();
		$cEncargoSacdObserv = $GesEncargoSacdObserv->getEncargoSacdObservs(array('id_nom'=>$Qid_nom));
		$observ_sacd = '';
		foreach ($cEncargoSacdObserv as $oEncargoSacdObserv) {
			$observ_sacd = $oEncargoSacdObserv->getObserv();
		}

		/* busco los datos del encargo que se tengan */
		$GesEncargosSacd = new GestorEncargoSacd();
		// No los personasles:
		$aWhereES = [];
		$aOperadorES = [];
        $aWhereES['id_nom'] = $Qid_nom;
        $aWhereES['f_fin'] = 'x';
        $aOperadorES['f_fin'] = 'IS NULL';
        $aWhereES['_ordre'] = 'modo, f_ini DESC';
		$cEncargosSacd1 = $GesEncargosSacd->getEncargosSacd($aWhereES, $aOperadorES);

        $aWhereES['f_fin'] = "'$hoy'";
        $aOperadorES['f_fin'] = '>';
		$cEncargosSacd2 = $GesEncargosSacd->getEncargosSacd($aWhereES,$aOperadorES);
			
		$cEncargosSacd = $cEncargosSacd1 + $cEncargosSacd2;
		$i=0;
		$a_modo = [];
		$a_id_tipo_enc = [];
		$a_mod_horario = [];
        $a_sf_sv = [];
        $a_id_enc = [];
        $a_desc_enc = [];
        $a_id_ubi = [];
        $a_dedic_ctr = [];
        
        
        $a_dedic_sacd = [];
        $a_dedic_m = [];
        $a_dedic_t= [];
        $a_dedic_v = [];
		foreach ($cEncargosSacd as $oEncargoSacd) {
			$i++;
			$id_enc = $oEncargoSacd->getId_enc();
			$modo = $oEncargoSacd->getModo();
			$a_modo[$i]=$modo;

			$oEncargo = new Encargo(array('id_enc'=>$id_enc));
			$id_enc = $oEncargo->getId_enc();
			$id_tipo_enc = $oEncargo->getId_tipo_enc();
			// Si es un encargo personal (7 o 4) me lo salto
			if (substr($id_tipo_enc, 0,1) == 7 || substr($id_tipo_enc, 0,1) == 4 )  {
			    $i--;
			    continue;
			}
			
			$sf_sv = $oEncargo->getSf_sv();
			$id_ubi = $oEncargo->getId_ubi();
			$desc_enc = $oEncargo->getDesc_enc();
			$a_id_tipo_enc[$i]=$id_tipo_enc;

			$oEncargoTipo->setId_tipo_enc($a_id_tipo_enc[$i]);
			$oEncargoTipo->DBCarregar();
			$a_mod_horario[$i] = $oEncargoTipo->getMod_horario();
			$a_sf_sv[$i]=$sf_sv;
			$a_id_enc[$i]=$id_enc;
			
			if ($permiso==1) {
				$a_desc_enc[$i]=$desc_enc;
			} elseif ($a_sf_sv[$i]==2) {
				$a_desc_enc[$i]= preg_replace('/\(.+\)/', '', $desc_enc);
			} else {
				$a_desc_enc[$i]=$desc_enc;
			}
			$a_id_ubi[$i]=$id_ubi;

			// horario del encargo (del ctr)
			$GesEncargoHorario = new GestorEncargoHorario();
			$aWhere = array();
			$aOperador = array();
			$aWhere['id_enc'] = $a_id_enc[$i];
			$aWhere['f_fin'] = 'x';
			$aOperador['f_fin'] = 'IS NULL';
			$aWhere['_ordre'] = 'f_ini DESC';
			$cEncargoHorarios0 = $GesEncargoHorario->getEncargoHorarios($aWhere,$aOperador);

			$aWhere['f_fin'] = "'$hoy'";
			$aOperador['f_fin'] = '>';
			$aWhere['_ordre'] = 'f_ini DESC';
			$cEncargoHorarios1 = $GesEncargoHorario->getEncargoHorarios($aWhere,$aOperador);
			$cEncargoHorarios = array_merge($cEncargoHorarios0,$cEncargoHorarios1);

			switch ($a_mod_horario[$i]) {
                case 3: //por horario.
                    $a_dedic_ctr[$i] = '';
                    $h=0;
                    foreach ($cEncargoHorarios as $oEncargoHorario) {
                        $h++;
						$mas_menos = $oEncargoHorario->getMas_menos();
						$dia_ref = $oEncargoHorario->getDia_ref();
						$dia_inc = $oEncargoHorario->getDia_inc();
						$dia_num = $oEncargoHorario->getDia_num();
						$h_ini = $oEncargoHorario->getH_ini();
						$h_fin = $oEncargoHorario->getH_fin();
						$n_sacd = $oEncargoHorario->getN_sacd();

                        $texto_horario=$GesEncargoTipo->texto_horario($mas_menos,$dia_ref,$dia_inc,$dia_num,$h_ini,$h_fin,$n_sacd);
						
						if ($h >1) $a_dedic_ctr[$i] .= " y ";
                        $a_dedic_ctr[$i] .= $texto_horario;
                    }
                    break;
                case 2: // por módulos.
                default:
                    $a_dedic_ctr_m[$i] = '';
                    $a_dedic_ctr_t[$i] = '';
                    $a_dedic_ctr_v[$i] = '';
                    foreach ($cEncargoHorarios as $oEncargoHorario) {
                        $modulo=$oEncargoHorario->getDia_ref();
                        switch ($modulo) {
                            case 'm':
                                $a_dedic_ctr_m[$i] = $oEncargoHorario->getDia_inc();
                                break;
                            case 't':
                                $a_dedic_ctr_t[$i] = $oEncargoHorario->getDia_inc();
                                break;
                            case 'v':
                                $a_dedic_ctr_v[$i] = $oEncargoHorario->getDia_inc();
                                break;
                        }
                    }
			}

			// horario
			//$sql_sacd_h="SELECT * FROM t_horario_sacd WHERE id_enc=$id_enc[$i] AND id_nom=".$_POST['id_nom']." AND ( f_fin is null OR f_fin > '$hoy' ) ";
			$aWhere = array();
			$aOperador = array();
			$GesHorario = new GestorEncargoSacdHorario();
			$aWhere['id_enc']=$a_id_enc[$i];
			$aWhere['id_nom']=$Qid_nom;
			$aWhere['f_fin']="'$hoy'";
			$aOperador['f_fin']='>';

			$cHorarios1 = $GesHorario->getEncargoSacdHorarios($aWhere,$aOperador);
			$aOperador['f_fin']='IS NULL';
			$cHorarios2 = $GesHorario->getEncargoSacdHorarios($aWhere,$aOperador);
			$cHorarios = $cHorarios1 + $cHorarios2;

			$a_dedic_m[$i]='';
			$a_dedic_t[$i]='';
			$a_dedic_v[$i]='';

			switch ($a_mod_horario[$i]) {
					case 3: //por horario.
						$a_dedic_sacd[$i] = '';
						$h=0;
						foreach ($cHorarios as $oEncargoSacdHorario) {
							$h++;
							$mas_menos = $oEncargoSacdHorario->getMas_menos();
							$dia_ref = $oEncargoSacdHorario->getDia_ref();
							$dia_inc = $oEncargoSacdHorario->getDia_inc();
							$dia_num = $oEncargoSacdHorario->getDia_num();
							$h_ini = $oEncargoSacdHorario->getH_ini();
							$h_fin = $oEncargoSacdHorario->getH_fin();

							$texto_horario=$GesEncargoTipo->texto_horario($mas_menos,$dia_ref,$dia_inc,$dia_num,$h_ini,$h_fin);
							if ($h >1) $a_dedic_sacd[$i] .= " y ";
							$a_dedic_sacd[$i] .= $texto_horario;
						}
						// si no tiene horario pongo el requerido por el centro
						$a_dedic_sacd[$i] = empty($a_dedic_sacd[$i])? _("horario del ctr").": ".$a_dedic_ctr[$i] : $a_dedic_sacd[$i];
						break;
					case 2: // por módulos.
					default:
						foreach ($cHorarios as $oEncargoSacdHorario) {
							$modulo=$oEncargoSacdHorario->getDia_ref();
							switch ($modulo) {
								case 'm':
									$a_dedic_m[$i]=$oEncargoSacdHorario->getDia_inc();
									break;
								case 't':
									$a_dedic_t[$i]=$oEncargoSacdHorario->getDia_inc();
									break;
								case 'v':
									$a_dedic_v[$i]=$oEncargoSacdHorario->getDia_inc();
									break;
							}
						}
						/*
						$dedicacion1.="<td><input type=text size=1 name=dedic_m value=$a_dedic_m[$i]>"._("mañanas");
						$dedicacion1.="</td><td><input type=text size=1 name=dedic_t value=$a_dedic_t[$i]>"._("tarde 1ª hora");
						$dedicacion1.="</td><td><input type=text size=1 name=dedic_v value=$a_dedic_v[$i]>"._("tarde 2ª hora")."</td></tr><tr>";
						*/
				}
			}
		$enc_num=$i;
		
	    $oEncargoConstants = new EncargoConstants();
		$opciones = $oEncargoConstants->getOpcionesEncargos();
		$oDesplEncs = new Desplegable();
		$oDesplEncs->setNombre('mas');
		$oDesplEncs->setOpciones($opciones);
		$oDesplEncs->setBlanco(1);
		$oDesplEncs->setAction("fnjs_mas_enc();");

		$oHash = new Hash();
		$aCamposHidden = [
		      'que'       => 'update',
              'id_nom'    => $Qid_nom,
		];
		//$oHash->setUrl($url_actualizar);
		$campos_form = 'enc_num!mas!observ!dedic_m!dedic_t!dedic_v!id_tipo_enc';
		$oHash->setcamposForm($campos_form);
		$oHash->setcamposNo('id_enc!mas!refresh');
		$oHash->setArrayCamposHidden($aCamposHidden);

		?>
		<script>
		fnjs_mas_enc=function(){
			var tipo_enc=$('#mas').val();
			var encargo=$('#mas :selected').text();
			if (!encargo) return;
			var n=$('#enc_num').val();
			var dedicacion;
			++n;
			// alert(encargo);
			
			/* dedicacion */
			switch (tipo_enc) {
				case '1110': //rtm
				case '1210': //rtm sf
					dedicacion="<tr><td>rtm en: <span class='link' onclick=fnjs_update_div('#main','des/tareas/horario_ver.php?id_tipo_enc="+tipo_enc+"')><?= _("definir horario") ?></span></td></tr>";
					break;
				case '6000': // otros
				case '5020':
				case '5030':
					dedicacion='<tr><td class=etiqueta>'+encargo+':<input type=hidden name=id_tipo_enc['+n+'] value='+tipo_enc+'></td><td><input type=text size=2 name=dedic_m['+n+']> <?= _("mañanas") ?></td><td><input type=text size=2 name=dedic_t['+n+']> <?= _("tarde 1ª hora") ?></td><td><input type=text size=2 name=dedic_v['+n+']> <?= _("tarde 2ª hora") ?></td></tr>';
				break;
			}
			
			/* antes del desplegable de añadir */
			$('#pie').before(dedicacion);
			$('#mas').val(0);
			$('#enc_num').val(n);
		}
		</script>
		<form id="datos_sacd" action="apps/encargossacd/controller/sacd_ficha_ajax.php" method="post">
		<?= $oHash->getCamposHtml(); ?>
		<input type="hidden" id="enc_num" name="enc_num" value="<?= $enc_num ?>">
		<table border=1>
		<?php 
		if (is_array($a_id_enc)) {
			$modo_ant='';
			$otros_enc = '';
			foreach ($a_id_enc as $j => $val ) {
				// las colatios y rtm los pongo al final
				if ($a_id_tipo_enc[$j]==4002 || $a_id_tipo_enc[$j]==1110 || $a_id_tipo_enc[$j]==1210 ) {
					$a_dedic_sacd[$j] = empty($a_dedic_sacd[$j])? _("crear horario") : $a_dedic_sacd[$j];
					$txt =  $a_dedic_sacd[$j];
					$otros_enc.="<tr><td>".$a_desc_enc[$j]."</td><td colspan=3>$txt</td></tr>";
					continue;
				}
				if ($modo_ant!=$a_modo[$j]) { 
					switch ($a_modo[$j]) {
						case 1:
							$cabecera=_("coordinador");
							break;
						case 2:
							$cabecera=_("titular");
							break;
						case 3:
							$cabecera=_("titular (no cl)");
							break;
						case 4:
							$cabecera=_("suplente");
							break;
						case 5:
							$cabecera=_("colaborador");
							break;
					}
					echo "<tr><th colspan=4>$cabecera</th></tr>";
					$cabecera="";
				}
				$modo_ant=$a_modo[$j];
				?>
				<tr>
				<td class=etiqueta><input type=hidden name=id_tipo_enc[<?= $j ?>] value=<?= $a_id_tipo_enc[$j] ?>><input type=hidden name=id_enc[<?= $j ?>] value=<?= $a_id_enc[$j] ?>>
				<?php
                $aQuery = [ 'id_ubi' => $a_id_ubi[$j], ];
                if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
                $pagina = Hash::link('apps/encargossacd/controller/ctr_ficha.php?'.http_build_query($aQuery));
				if ($permiso==1) {
					?>
					<span class="link" onclick="fnjs_update_div('#main','<?= $pagina ?>');">
						<?= $a_desc_enc[$j] ?></span></td>
					<?php
				} elseif ($a_sf_sv[$j]==2 ) {
					echo "$a_desc_enc[$j]</td>";
				} else {
					?>
					<span class="link" onclick="fnjs_update_div('#main','<?= $pagina ?>');">
						<?= $a_desc_enc[$j] ?></span></td>
					<?php
				}
				if ($a_modo[$j]!=4) { // Para el suplente no hay horario. 
					if ($a_mod_horario[$j] == 3) {
						$a_dedic_sacd[$j] = empty($a_dedic_sacd[$j])? _("crear horario") : $a_dedic_sacd[$j];
					?>
						<td colspan=3><span class="link" onclick="fnjs_crear_horario(<?= $e ?>);"><?= $a_dedic_sacd[$j] ?></span></td>
					<?php
					} else {
					?>
						<td><input type=text size=1 name="dedic_m[<?= $j ?>]" value=<?= $a_dedic_m[$j] ?>> <?= _("mañanas") ?></td>
						<td><input type=text size=1 name="dedic_t[<?= $j ?>]" value=<?= $a_dedic_t[$j] ?>> <?= _("tarde 1ª hora") ?></td>
						<td><input type=text size=1 name="dedic_v[<?= $j ?>]" value=<?= $a_dedic_v[$j] ?>> <?= _("tarde 2ª hora") ?></td></tr>
					<?php 
					}
				}
			}
		}
		// Añado los cuadros de estudio y descanso por defecto.
		if (!empty($otros_enc)) {
			$cabecera=_("otros");
			echo "<tr><th colspan=4>$cabecera</th></tr>";
			echo "$otros_enc"; 
		}
		?>
		<tr id=pie><td><?= _("añadir encargo") ?>
		<?= $oDesplEncs->desplegable(); ?>
		</td></tr>
		<tr>
		<td colspan=4><?= _("observaciones") ?>: <textarea rows=3 cols=50 name=observ ><?= $observ_sacd ?></textarea></td></tr>
		<?php
		if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
		?>
		<th colspan=4><input type="button" name="ok" onclick="fnjs_guardar('#datos_sacd');" value="<?php echo ucfirst(_("guardar")); ?>"></th>
		<?php
		}
		?>
		</table>
		<?php
		break;
	case 'update':
		//modificar 
        $Qenc_num = (integer) \filter_input(INPUT_POST, 'enc_num');
        $Qobserv = (string) \filter_input(INPUT_POST, 'observ');

		$QAid_tipo_enc = filter_input(INPUT_POST, 'id_tipo_enc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$QAid_enc = filter_input(INPUT_POST, 'id_enc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$QAdedic_m = filter_input(INPUT_POST, 'dedic_m', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$QAdedic_t = filter_input(INPUT_POST, 'dedic_t', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$QAdedic_v = filter_input(INPUT_POST, 'dedic_v', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

		for($i=1;$i<=$Qenc_num;$i++) {
			// caso especial para estudio/descanso/otros. Es para todos el mismo id_enc.
			if ($QAid_tipo_enc[$i]==5020 || $QAid_tipo_enc[$i]==5030 || $QAid_tipo_enc[$i]==6000) {
				if (empty($QAid_enc[$i])) {
					// primero se debe crear el encargo. busco si ya existe uno antes de crearlo.
					$GesEncargo = new GestorEncargo();
					$cEncargos = $GesEncargo->getEncargos(array('id_tipo_enc'=>$QAid_tipo_enc[$i]));
					if (is_array($cEncargos) && empty($cEncargos)) {
					    if  ($QAid_tipo_enc[$i]==5020) { $desc_enc="estudio"; }
					    if  ($QAid_tipo_enc[$i]==5030) { $desc_enc="descanso"; }
					    if  ($QAid_tipo_enc[$i]==6000) { $desc_enc="otros"; }
						$QAid_enc[$i]=$GesEncargoTipo->crear_encargo($QAid_tipo_enc[$i],1,"","",$desc_enc,"","","");
					} else {
						$QAid_enc[$i]=$cEncargos[0]->getId_enc();
					}
				}
				// si no hay dedicacion elimino el encargo al sacd
				if (empty($QAdedic_m[$i]) && empty($QAdedic_t[$i]) && empty($QAdedic_v[$i])) {
				    $GesEncargoTipo->delete_sacd($QAid_enc[$i],$Qid_nom,2);
				} else {
					$GesEncargoTipo->insert_sacd($QAid_enc[$i],$Qid_nom,2);
				}
			}
			if (!empty($QAid_enc[$i])) { // me aseguro que el encargo ya existe.
				// busco el id_item de la tarea_sacd.
				$GesEncargoSacd = new GestorEncargoSacd();
				$aWhere = array();
				$aOperador = array();
				$aWhere['id_nom'] = $Qid_nom;
				$aWhere['id_enc'] = $QAid_enc[$i];
				$aWhere['modo'] =  '(2|3|5)';
				$aWhere['f_fin'] = 'x';
				$aOperador['f_fin'] = 'IS NULL';
				$aOperador['modo'] = '~';
				$cEncargosSacd = $GesEncargoSacd->getEncargosSacd($aWhere,$aOperador);
				if (empty($cEncargosSacd)) { continue; }
				
				if (count($cEncargosSacd)>1) { echo _("Error con las tareas \n"); print_r($cEncargosSacd); }
				foreach ($cEncargosSacd as $oEncargoSacd) { // se supone que sólo hay uno.
					$id_item_t_sacd=$oEncargoSacd->getId_item();
				}

				$QAdedic_m[$i] = empty($QAdedic_m[$i])? '' : $QAdedic_m[$i];
				$GesEncargoTipo->modificar_horario_sacd($id_item_t_sacd,$QAid_enc[$i],$Qid_nom,'m',$QAdedic_m[$i]);
				$QAdedic_t[$i] = empty($QAdedic_t[$i])? '' : $QAdedic_t[$i];
				$GesEncargoTipo->modificar_horario_sacd($id_item_t_sacd,$QAid_enc[$i],$Qid_nom,'t',$QAdedic_t[$i]);
				$QAdedic_v[$i] = empty($QAdedic_v[$i])? '' : $QAdedic_v[$i];
				$GesEncargoTipo->modificar_horario_sacd($id_item_t_sacd,$QAid_enc[$i],$Qid_nom,'v',$QAdedic_v[$i]);
			}
		}

		// miro si tiene observaciones, y o actualizo, o creo una nueva.

		$GesEncargoSacdObserv = new GestorEncargoSacdObserv();
		$cEncargoSacdObserv = $GesEncargoSacdObserv->getEncargoSacdObservs(array('id_nom'=>$Qid_nom));
		if(!empty($cEncargoSacdObserv[0])) {
			if (empty($Qobserv)) {
			   if ($cEncargoSacdObserv[0]->DBEliminar() === false) {
					echo _("hay un error, no se ha eliminado");
				}
			} else {
				$cEncargoSacdObserv[0]->setObserv($Qobserv);
				if ($cEncargoSacdObserv[0]->DBGuardar() === false) {
					echo _("hay un error, no se ha guardado");
				}
			}
		} else {
			$oEncargoSacdObserv = new EncargoSacdObserv(); 
			$oEncargoSacdObserv->setId_nom($Qid_nom);
			$oEncargoSacdObserv->setObserv($Qobserv);
			if ($oEncargoSacdObserv->DBGuardar() === false) {
				echo _("hay un error, no se ha guardado");
				echo "\n".$oEncargoSacdObserv->getErrorTxt();
			}
		}
		break;
}
