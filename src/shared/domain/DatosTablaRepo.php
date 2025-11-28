<?php

namespace src\shared\domain;

use function core\is_true;
use function core\urlsafe_b64encode;

/**
 * Fitxer amb la Classe que accedeix a la taula d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/11/2014
 */

/**
 * Clase que implementa la entidad d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/04/2018
 */
class DatosTablaRepo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    private string $tit_txt;
    private string $explicacion_txt;
    private string $eliminar_txt;

    private $Coleccion;
    private $oFicha;
    private $bloque; // necesario para el script
    private $action_form; // necesario para el script
    private $action_update; // necesario para el script
    private $action_tabla; // necesario para el script

    private $id_sel;
    private $scroll_id;

    private $html_script;
    private $a_cabeceras;
    private $a_valores;

    public function getScript()
    {
        $bloque = $this->getBloque();
        $action_form = $this->getAction_form();
        $action_update = $this->getAction_update();
        $action_tabla = $this->getAction_tabla();
        $respuesta = _("respuesta");
        $html_script = "
		fnjs_nuevo=function(formulario){
			$('#mod').val(\"nuevo\");
			$(formulario).attr('action',\"$action_form\");
			fnjs_enviar_formulario(formulario,'$bloque');
		}
		fnjs_modificar=function(formulario){
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				$('#mod').val(\"editar\");
				$(formulario).attr('action',\"$action_form\");
				fnjs_enviar_formulario(formulario,'$bloque');
			}
		}
		fnjs_eliminar=function(formulario){
			var err;
			var eliminar;
			eliminar=\"$this->eliminar_txt\";

			if (!eliminar) eliminar=\"" . _("¿Está seguro que desea eliminar este registro?") . "\";
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				if (confirm(eliminar) ) {
					$('#mod').val(\"eliminar\");
					$(formulario).attr('action',\"$action_update\");
					$(formulario).one(\"submit\", function() {
						$.ajax({
							url: $(this).attr('action'),
							type: 'post',
							data: $(this).serialize()},
							)
                        .done(function (json) {
                            if (json.success !== true) {
                                alert(\"$respuesta\" + ': ' + json.mensaje);
                            } else {
                                fnjs_actualizar(formulario);
                            }
                        });
						return false;
					});
					$(formulario).trigger(\"submit\");
					$(formulario).off();
				}
			}
		}
		fnjs_actualizar=function(formulario){
			var campo = '<input name=\"refresh\" type=\"hidden\" value=1>';
			$(formulario).attr('action',\"$action_tabla\");
			$(formulario).append(campo);
			fnjs_enviar_formulario(formulario,'#main');
		}
		";
        return $html_script;
    }

    public function getBotones()
    {
        $a_botones = array(array('txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"),
            array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(this.form)")
        );
        return $a_botones;
    }

    public function getCabeceras()
    {
        if (empty($this->a_cabeceras)) {
            $this->getTabla();
        }
        return $this->a_cabeceras;
    }

    public function getValores()
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla()
    {
        $a_cabeceras = [];
        $a_valores = [];
        $Qid_sel = $this->id_sel;
        $Qscroll_id = $this->scroll_id;
        if (isset($Qid_sel) && !empty($Qid_sel)) {
            $a_valores['select'] = $Qid_sel;
        }
        if (isset($Qscroll_id) && !empty($Qscroll_id)) {
            $a_valores['scroll_id'] = $Qscroll_id;
        }
        $c = 0;
        foreach ($this->Coleccion as $oFila) {
            $v = 0;
            $pks1 = 'get' . ucfirst($oFila->getPrimary_key());
            $val_pks = $oFila->$pks1();
            $pks = urlsafe_b64encode(json_encode($val_pks, JSON_THROW_ON_ERROR));
            $a_valores[$c]['sel'] = $pks;
            foreach ($oFila->getDatosCampos() as $oDatosCampo) {
                if ($c == 0) {
                    $a_cabeceras[] = ucfirst($oDatosCampo->getEtiqueta());
                }
                $v++;
                $metodo = $oDatosCampo->getMetodoGet();
                // si el metodo obtiene un valueobject
                if (substr($metodo,-2) === 'Vo') {
                    $valor_camp = $oFila->$metodo()->value();
                } else {
                    $valor_camp = $oFila->$metodo();
                }
                if (!$valor_camp) {
                    $a_valores[$c][$v] = '';
                    continue;
                }
                $var_1 = $oDatosCampo->getArgument();
                $var_2 = $oDatosCampo->getArgument2();
                switch ($oDatosCampo->getTipo()) {
                    case "fecha":
                        $a_valores[$c][$v] = $valor_camp->getFromLocal();
                        break;
                    case "array":
                        $lista = $oDatosCampo->getLista();
                        $a_valores[$c][$v] = $lista[$valor_camp];
                        break;
                    case 'depende':
                    case 'opciones':
                        $RepoRelacionado = $GLOBALS['container']->get($var_1);
                        $oRelacionado = $RepoRelacionado->findById($valor_camp);
                        if ($oRelacionado !== null) {
                            if (substr($var_2,-2) === 'Vo') {
                                $var = $oRelacionado->$var_2()->value();
                            } else {
                                $var = $oRelacionado->$var_2();
                            }
                            if (empty($var)) {
                                $var = $valor_camp;
                            }
                        } else {
                            $var = '?';
                        }
                        $a_valores[$c][$v] = $var;
                        break;
                    case "check":
                        if (is_true($valor_camp)) {
                            $a_valores[$c][$v] = _("sí");
                        } else {
                            $a_valores[$c][$v] = _("no");
                        }
                        break;
                    default:
                        $a_valores[$c][$v] = $valor_camp;
                }
            }
            $c++;
        }

        $this->a_cabeceras = $a_cabeceras;
        $this->a_valores = $a_valores;
    }

    public function getAction_form()
    {
        if (!isset($this->action_form)) {
            $this->action_form = "frontend/shared/controller/tablaDB_formulario_ver.php";
        }
        return $this->action_form;
    }

    public function getAction_update()
    {
        if (!isset($this->action_update)) {
            $this->action_update = "src/shared/infrastructure/controllers/tablaDB_update.php";
        }
        return $this->action_update;
    }

    public function getAction_tabla()
    {
        if (!isset($this->action_tabla)) {
            $this->action_tabla = "frontend/shared/controller/tablaDB_lista_ver.php";
        }
        return $this->action_tabla;
    }

    public function setAction_form($action_form)
    {
        $this->action_form = $action_form;
    }

    public function setAction_update($action_update)
    {
        $this->action_update = $action_update;
    }

    public function setAction_tabla($action_tabla)
    {
        $this->action_tabla = $action_tabla;
    }

    public function getTit_txt()
    {
        return $this->tit_txt;
    }

    public function getExplicacion_txt()
    {
        return $this->explicacion_txt;
    }

    public function getEliminar_txt()
    {
        return $this->eliminar_txt;
    }

    public function getColeccion()
    {
        return $this->Coleccion;
    }

    public function getBloque()
    {
        if (!isset($this->bloque)) {
            $this->bloque = '#main';
        }
        return $this->bloque;
    }

    public function getId_sel()
    {
        return $this->id_sel;
    }

    public function getScroll_id()
    {
        return $this->scroll_id;
    }

    public function getHtml_script()
    {
        return $this->html_script;
    }

    public function getA_cabeceras()
    {
        return $this->a_cabeceras;
    }

    public function getA_valores()
    {
        return $this->a_valores;
    }

    public function setTit_txt($tit_txt)
    {
        $this->tit_txt = $tit_txt;
    }

    public function setExplicacion_txt($explicacion_txt)
    {
        $this->explicacion_txt = $explicacion_txt;
    }

    public function setEliminar_txt($eliminar_txt)
    {
        $this->eliminar_txt = $eliminar_txt;
    }

    public function setColeccion($Coleccion)
    {
        $this->Coleccion = $Coleccion;
    }

    public function setBloque($bloque)
    {
        $this->bloque = $bloque;
    }

    public function setFicha($oFicha)
    {
        $this->oFicha = $oFicha;
    }

    public function setId_sel($id_sel)
    {
        $this->id_sel = $id_sel;
    }

    public function setScroll_id($scroll_id)
    {
        $this->scroll_id = $scroll_id;
    }

    public function setHtml_script($html_script)
    {
        $this->html_script = $html_script;
    }

    public function setA_cabeceras($a_cabeceras)
    {
        $this->a_cabeceras = $a_cabeceras;
    }

    public function setA_valores($a_valores)
    {
        $this->a_valores = $a_valores;
    }
}
