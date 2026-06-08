<?php

namespace src\shared\domain;

use src\shared\domain\contracts\DatosFichaInterface;
use src\shared\domain\contracts\DatosLookupRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use function src\shared\domain\helpers\is_true;
use function src\shared\domain\helpers\urlsafe_b64encode;

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
    private string $tit_txt = '';
    private string $explicacion_txt = '';
    private string $eliminar_txt = '';

    /** @var iterable<DatosFichaInterface>|null */
    private ?iterable $Coleccion = null;
    private ?object $oFicha = null;
    private string $bloque = '#main';
    private string $action_form = 'frontend/shared/controller/tablaDB_formulario_ver.php';
    private string $action_update = 'src/shared/tablaDB_update';
    private string $action_tabla = 'frontend/shared/controller/tablaDB_lista_ver.php';

    private mixed $id_sel = null;
    private mixed $scroll_id = null;

    private string $html_script = '';
    /** @var list<string>|null */
    private ?array $a_cabeceras = null;
    /** @var array<int|string, mixed>|null */
    private ?array $a_valores = null;

    public function getScript(): string
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

    /**
     * @return list<array{txt: string, click: string}>
     */
    public function getBotones(): array
    {
        return [
            ['txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"],
            ['txt' => _("eliminar"), 'click' => "fnjs_eliminar(this.form)"],
        ];
    }

    /**
     * @return list<string>
     */
    public function getCabeceras(): array
    {
        if (empty($this->a_cabeceras)) {
            $this->getTabla();
        }

        return $this->a_cabeceras ?? [];
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getValores(): array
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }

        return $this->a_valores ?? [];
    }

    private function getTabla(): void
    {
        /** @var list<string> $a_cabeceras */
        $a_cabeceras = [];
        /** @var array<int, array<string, mixed>> $a_valores */
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
        $coleccion = $this->Coleccion ?? [];
        foreach ($coleccion as $oFila) {
            $v = 0;
            $primaryKey = $oFila->getPrimary_key();
            $pkName = is_string($primaryKey) ? $primaryKey : '';
            $pks1 = 'get' . ucfirst($pkName);
            $val_pks = $oFila->$pks1();
            $pks = urlsafe_b64encode(json_encode($val_pks, JSON_THROW_ON_ERROR));
            /** @var array<string, mixed> $filaValores */
            $filaValores = ['sel' => $pks];
            foreach ($oFila->getDatosCampos() as $oDatosCampo) {
                if ($c == 0) {
                    $a_cabeceras[] = ucfirst($oDatosCampo->getEtiqueta() ?? '');
                }
                $v++;
                $metodo = $oDatosCampo->getMetodoGet() ?? '';
                if (substr($metodo, -2) === 'Vo') {
                    $valor_camp = $oFila->$metodo()->value();
                } else {
                    $valor_camp = $oFila->$metodo();
                }
                if (!$valor_camp) {
                    $filaValores[(string) $v] = '';
                    continue;
                }
                $var_1 = $oDatosCampo->getArgument();
                $var_2 = $oDatosCampo->getArgument2();
                switch ($oDatosCampo->getTipo()) {
                    case "fecha":
                        $filaValores[(string) $v] = $valor_camp->getFromLocal();
                        break;
                    case "array":
                        $lista = $oDatosCampo->getLista() ?? [];
                        $filaValores[(string) $v] = $lista[$valor_camp];
                        break;
                    case 'depende':
                    case 'opciones':
                        if (!is_string($var_1) || $var_1 === '' || (!interface_exists($var_1) && !class_exists($var_1))) {
                            $filaValores[(string) $v] = '?';
                            break;
                        }
                        /** @var DatosLookupRepositoryInterface $RepoRelacionado */
                        $RepoRelacionado = DependencyResolver::get($var_1);
                        $oRelacionado = $RepoRelacionado->findById($valor_camp);
                        if ($oRelacionado !== null) {
                            $var2Method = $var_2 ?? '';
                            if (substr($var2Method, -2) === 'Vo') {
                                $var = $oRelacionado->$var2Method()->value();
                            } else {
                                $var = $oRelacionado->$var2Method();
                            }
                            if (empty($var)) {
                                $var = $valor_camp;
                            }
                        } else {
                            $var = '?';
                        }
                        $filaValores[(string) $v] = $var;
                        break;
                    case "check":
                        if (is_true($valor_camp)) {
                            $filaValores[(string) $v] = _("sí");
                        } else {
                            $filaValores[(string) $v] = _("no");
                        }
                        break;
                    case "hidden":
                        if ($c == 0) {
                            array_pop($a_cabeceras);
                        }
                        $a_valores[$c] = $filaValores;
                        continue 2;
                    default:
                        $filaValores[(string) $v] = $valor_camp;
                }
            }
            $a_valores[$c] = $filaValores;
            $c++;
        }

        $this->a_cabeceras = $a_cabeceras;
        $this->a_valores = $a_valores;
    }

    public function getAction_form(): string
    {
        return $this->action_form;
    }

    public function getAction_update(): string
    {
        return $this->action_update;
    }

    public function getAction_tabla(): string
    {
        return $this->action_tabla;
    }

    public function setAction_form(string $action_form): void
    {
        $this->action_form = $action_form;
    }

    public function setAction_update(string $action_update): void
    {
        $this->action_update = $action_update;
    }

    public function setAction_tabla(string $action_tabla): void
    {
        $this->action_tabla = $action_tabla;
    }

    public function getTit_txt(): string
    {
        return $this->tit_txt;
    }

    public function getExplicacion_txt(): string
    {
        return $this->explicacion_txt;
    }

    public function getEliminar_txt(): string
    {
        return $this->eliminar_txt;
    }

    /**
     * @return iterable<DatosFichaInterface>|null
     */
    public function getColeccion(): ?iterable
    {
        return $this->Coleccion;
    }

    public function getBloque(): string
    {
        return $this->bloque;
    }

    public function getId_sel(): mixed
    {
        return $this->id_sel;
    }

    public function getScroll_id(): mixed
    {
        return $this->scroll_id;
    }

    public function getHtml_script(): string
    {
        return $this->html_script;
    }

    /**
     * @return list<string>|null
     */
    public function getA_cabeceras(): ?array
    {
        return $this->a_cabeceras;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getA_valores(): ?array
    {
        return $this->a_valores;
    }

    public function getFicha(): ?object
    {
        return $this->oFicha;
    }

    public function setTit_txt(string $tit_txt): void
    {
        $this->tit_txt = $tit_txt;
    }

    public function setExplicacion_txt(string $explicacion_txt): void
    {
        $this->explicacion_txt = $explicacion_txt;
    }

    public function setEliminar_txt(string $eliminar_txt): void
    {
        $this->eliminar_txt = $eliminar_txt;
    }

    /**
     * @param iterable<DatosFichaInterface>|null $Coleccion
     */
    public function setColeccion(?iterable $Coleccion): void
    {
        $this->Coleccion = $Coleccion;
    }

    public function setBloque(string $bloque): void
    {
        $this->bloque = $bloque;
    }

    public function setFicha(?object $oFicha): void
    {
        $this->oFicha = $oFicha;
    }

    public function setId_sel(mixed $id_sel): void
    {
        $this->id_sel = $id_sel;
    }

    public function setScroll_id(mixed $scroll_id): void
    {
        $this->scroll_id = $scroll_id;
    }

    public function setHtml_script(string $html_script): void
    {
        $this->html_script = $html_script;
    }

    /**
     * @param list<string>|null $a_cabeceras
     */
    public function setA_cabeceras(?array $a_cabeceras): void
    {
        $this->a_cabeceras = $a_cabeceras;
    }

    /**
     * @param array<int|string, mixed>|null $a_valores
     */
    public function setA_valores(?array $a_valores): void
    {
        $this->a_valores = $a_valores;
    }
}
