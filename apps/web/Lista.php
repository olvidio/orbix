<?php

namespace web;

use core\ConfigGlobal;
use src\usuarios\application\repositories\PreferenciaRepository;
use function core\is_true;

//require_once ("classes/personas/ext_web_preferencias.class");

/**
 * Listas
 *
 * Classe per gestionar llistes de dades tipus taula.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/10/2010
 */
class Lista
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * sNombre del Lista
     *
     * @var string
     */
    private $sNombre;
    /**
     * ikey del Lista
     *
     * @var integer ?
     */
    private $ikey;

    /**
     * aGrupos de la Lista
     *
     * @var array ( id => titulo)
     */
    private $aGrupos;
    private $botones_grupo = FALSE;

    /**
     * aCabeceras de la Lista
     *
     * @var array
     */
    private $aCabeceras;
    /**
     * sPie de la Lista
     *
     * @var string
     */
    private $sPie;
    /**
     * ssortcol de la Lista. Columna por la que se ordena la tabla inicialmente.
     *
     * @var string
     */
    private $ssortCol;
    /**
     * aColVisible de la Lista. columnas visibles inicialmente.
     *
     * @var array
     */
    private $aColVisible = [];
    /**
     * aDatos de la Lista
     *
     * @var array lista de arrays (id el del titulo) cada sub-array es la fila.
     */
    private $aDatos;
    /**
     * aBotones de la Lista
     *
     * @var array
     */
    private $aBotones;
    /**
     * sid_tabla de la Lista
     *
     * @var array
     */
    private $sid_tabla = 'uno';
    /**
     * bFiltro de la Lista
     *
     * @var boolean
     */
    private $bFiltro = TRUE;
    /**
     * bColVis de la Lista
     *
     * @var boolean
     */
    private $bColVis = TRUE;
    /**
     * brecordar de la lista
     *
     * @var boolean
     */
    private $brecordar = TRUE;
    /**
     * formato_tabla de la lista
     *
     * @var string
     */
    private $formato_tabla = '';

    private mixed $bRecordar;

    private PreferenciaRepository $preferenciaRepository;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return Lista
     *
     */
    function __construct()
    {
        $this->preferenciaRepository = new PreferenciaRepository();
    }

    /**
     * Muestra una tabla simple
     *
     * @return string Html
     *
     */
    function lista()
    {
        $aCabeceras = $this->aCabeceras;
        $aDatos = $this->aDatos;
        $key = $this->ikey;
        $id_tabla = $this->sid_tabla;
        $clase = 'lista';
        //------------------------------------ html ------------------------------
        $Html = "<table class=\"$clase\"><tr>";
        foreach ($aCabeceras as $cabecera) {
            $Html .= "<th>$cabecera</th>";
        }
        $Html .= "</tr>";

        if (empty($aDatos)) {
            return _("no hay ninguna fila");
        }
        $Html .= "<tbody class=\"$clase\">";
        foreach ($aDatos as $aFila) {
            $Html .= "<tr>";
            $clase = 'lista';
            if (!empty($aFila['clase'])) {
                $clase .= " " . $aFila['clase'];
            }
            foreach ($aFila as $col => $valor) {
                if ($col === "clase") {
                    continue;
                }
                if ($col === "order") {
                    continue;
                }
                if ($col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    continue;
                }
                if (is_array($valor)) {
                    $val = $valor['valor'];
                    $td_id = empty($valor['id']) ? '' : "id=\"" . $valor['id'] . "\"";
                    if (!empty($valor['ira'])) {
                        $ira = $valor['ira'];
                        $Html .= "<td $td_id class=\"$clase\"><span class=link onclick=fnjs_update_div('#main','$ira') >$val</span></td>";
                    }
                    if (!empty($valor['script'])) {
                        $Html .= "<td $td_id class=\"$clase\"><span class=link onclick=\"" . $valor['script'] . "\" >$val</span></td>";
                    }
                    if (!empty($valor['span'])) {
                        $Html .= "<td $td_id class=\"$clase\" onclick=\"toggleFilterRow_$id_tabla()\" colspan=\"" . $valor['span'] . "\">$val</td>";
                    }
                    if (!empty($valor['clase'])) {
                        $Html .= "<td $td_id class=\"$clase " . $valor['clase'] . "\">$val</td>";
                    }
                } else {
                    $es_fecha = FALSE;
                    // si es una fecha, pongo la clase fecha, para exportar a excel...
                    $formato_fecha = DateTimeLocal::getFormat();
                    if ($formato_fecha === 'd/m/Y') {
                        if (preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
                            list($d, $m, $y) = preg_split('/[:\/\.-]/', $valor);
                            $es_fecha = TRUE;
                        }
                    }
                    if ($formato_fecha === 'm/d/Y') {
                        if (preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
                            list($m, $d, $y) = preg_split('/[:\/\.-]/', $valor);
                            $es_fecha = TRUE;
                        }
                    }
                    if ($es_fecha) {
                        $fecha_iso = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));
                        $clase = "fecha $clase";
                        $Html .= "<td class=\"$clase\" fecha_iso='$fecha_iso'>$valor</td>";
                    } else {
                        $Html .= "<td class=\"$clase\">$valor</td>";
                    }
                }
            }
            $Html .= "</tr>";
        }
        $Html .= "</tbody></table>";
        return $Html;
    }

    /**
     * Constructor de la classe.
     *
     * @return string Html
     *
     */
    function listaPaginada()
    {
        $aGrupos = $this->aGrupos;
        $aCabeceras = $this->aCabeceras;
        $aDatos = $this->aDatos;
        //------------------------------------ html ------------------------------
        reset($aGrupos);
        $Html = '';
        foreach ($aGrupos as $key => $titulo) {
            $this->aDatos = $aDatos[$key];
            $this->ikey = $key;
            $Html .= "<div class=salta_pag>";
            $Html .= "<h2>$titulo</h2>";
            $Html .= $this->lista();
            if (!empty($this->sPie)) {
                $Html .= "<p>$this->sPie</p>";
            }
            $Html .= "</div>";
        }
        return $Html;
    }

    function mostrar_tabla_grupos()
    {
        $aGrupos = $this->aGrupos;
        $a_botones = $this->aBotones;
        $aDatos = $this->aDatos;
        $id_tabla = $this->sid_tabla;
        //------------------------------------ html ------------------------------
        reset($aGrupos);
        $Html = '';
        if (!empty($a_botones)) {
            $botones = '';
            if ($a_botones === "ninguno") {
                $b = 0;
            } else {
                $b = 0;
                foreach ($a_botones as $a_boton) {
                    $prefix = empty($a_boton['prefix']) ? '' : $a_boton['prefix'] . '_';
                    $btn = $prefix . "btn" . $b++;
                    $botones .= "<INPUT id='$btn' name='$btn' type=button value=\"" . $a_boton['txt'] . "\" onClick='" . $a_boton['click'] . "'>";
                }
                $botones .= "</td></tr>";
            }
            $cab = count($this->aCabeceras);
            $botones = "<tr class=botones><td colspan='$cab'>" . $botones;
            if ($b > 0) {
                $this->botones_grupo = TRUE;
                $Html .= "<table>$botones</table>\n";
            }
        }
        $this->setBotones([]);
        foreach ($aGrupos as $key => $titulo) {
            $this->aDatos = $aDatos[$key];
            $this->ikey = $key;
            $id_tabla_key = $id_tabla.'_'.$key;
            $this->setId_tabla($id_tabla_key);
            $Html .= "<div class=salta_pag>";
            $Html .= "<h3>$titulo</h3>";
            $Html .= $this->mostrar_tabla_html();
            if (!empty($this->sPie)) {
                $Html .= "<p>$this->sPie</p>";
            }
            $Html .= "</div>";
        }
        return $Html;
    }

    /**
     * Muestra una tabla ordenable, con  botones en la cabecera y check box en cada lina.
     *
     * @return string Html
     *
     */
    function mostrar_tabla()
    {
        $sPrefs = '';
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $tipo = 'tabla_presentacion';
        if (empty($this->formato_tabla)) {
            $cPref = $this->preferenciaRepository->getPreferencias(['id_usuario' => $id_usuario, 'tipo' => $tipo]);
            if (!empty($cPref)) {
                $sPrefs = $cPref[0]->getPreferencia();
            } else {
                $sPrefs = '';
            }
        } else {
            $sPrefs = $this->formato_tabla;
        }
        if ($sPrefs === 'html') {
            return $this->mostrar_tabla_html();
        } else {
            return $this->mostrar_tabla_slickgrid();
        }
    }

    /**
     * Muestra una tabla ordenable, con  botones en la cabecera y check box en cada lina.
     *
     * $a_cabeceras:  array(
     *        [col] = array('name'=>_("inicio"),'width'=>40,'class'=>'fecha', 'formatter'=>'clickFormatter')
     *                'name'=> texto de la cabecera de la columna
     *                'width'=> ancho de la columna
     *                'class'=> se añade al atributo class
     *                'formatter'=> ['clickFormatter'|'clickFormatter2'|'clickFormatter3'] post-formateo a aplicar en la columna.
     *                              Para el caso de 'ira', 'script', 'ira2', 'script2' y  'ira3', 'script3' respectivamente
     *
     * $a_valores:  array(
     *    [fila]['clase'] = valor => añade añade 'valor' en el atributo class de la fila
     *    [fila]['sel'] = valor => crea la columna sel con un checkbox con id='#'.addslashes(valor)
     *    [fila]['select'] = array(valor)    => marca como checked los checkbox de la columna 'sel' con id = valor
     *    [fila]['scroll_id'] = valor    => ejecuta: " grid_$id_tabla.scrollRowToTop($scroll_id);" Que desplaza las filas hasta la linea correspondiente.
     *    [fila][col] = txt
     *    [fila][col] = array( 'ira'=>$pagina, 'valor'=>$txt)
     *                => crea un 'link' que ejecuta "fnjs_update_div('#main','pagina'):
     *                return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"') \\\" >\"+value+\"</span>\";
     *    [fila][col] = array( 'script'=>$script, 'valor'=>$txt);
     *                => crea un 'link' que ejecuta  al funcion de $cript:
     * return \"<span class=link onclick='this.closest(\\\".slick-cell\\\").trigger("click");\"+ira+\";' >\"+value+\"</span>\";
     *    [fila][col] = array( 'span'=>3, 'valor'=> $txt) => de momento no hace nada. Sirve para la funcion mostrar_tabla_html
     * @return string Html Grid
     *
     */
    function mostrar_tabla_slickgrid()
    {
        $a_botones = $this->aBotones;
        $a_cabeceras = $this->aCabeceras;
        $a_valores = $this->aDatos;
        $id_tabla = $this->sid_tabla;
        $grid_width = '900';
        $grid_height = '0';

        $sortcol = $this->ssortCol;
        $botones = "";
        $cabecera = "";
        $tt = "";
        $clase = "";
        $chk = "";
        $b = 0;
        $height_botones = 0;
        // Quitar al principio el scroll_id, select por si no hay filas.
        $scroll_id = !empty($a_valores['scroll_id']) ? $a_valores['scroll_id'] : 0;
        unset($a_valores['scroll_id']);
        if (isset($a_valores['select'])) {
            $a_valores_chk = $a_valores['select'];
            unset($a_valores['select']);
        } else {
            $a_valores_chk = [];
        }
        if (empty($a_valores)) {
            return _("no hay ninguna fila");
        }
        if (!empty($a_botones)) {
            if ($a_botones === "ninguno") {
                $b = "x";
            } else {
                foreach ($a_botones as $a_boton) {
                    $prefix = empty($a_boton['prefix']) ? '' : $a_boton['prefix'] . '_';
                    $btn = $prefix . "btn" . $b++;
                    $botones .= "<INPUT id='$btn' name='$btn' type=button value=\"" . $a_boton['txt'] . "\" onClick='" . $a_boton['click'] . "'>";
                }
            }
        }

        $id_usuario = ConfigGlobal::mi_id_usuario();
        $idioma = ConfigGlobal::mi_Idioma();
        $tipo = 'slickGrid_' . $id_tabla . '_' . $idioma;
        $aUser = array(0 => $id_usuario, 1 => 44); // 44 es el id_usuario para default.
        $aColsVisible = '';
        $bPanelVis = FALSE;
        for ($i = 0, $iMax = count($aUser); $i < $iMax; $i++) {
            $user = $aUser[$i];
            $cPref = $this->preferenciaRepository->getPreferencias(['id_usuario' => $user, 'tipo' => $tipo]);
            if (!empty($cPref)) {
                $sPrefs = $cPref[0]->getPreferencia();
                $aPrefs = json_decode($sPrefs, TRUE, 512, JSON_THROW_ON_ERROR);
                if (!empty($aPrefs['colVisible'])) {
                    $aColsVisible = $aPrefs['colVisible'];
                    //$aColsVisible = empty($aPrefs['colVisible'])? '*' : $aPrefs['colVisible'];
                    //$aColsVisible = explode(',',$aPrefs['colVisible']);
                }
                $bPanelVis = $aPrefs['panelVis'] === "si";
                if (!empty($aPrefs['colWidths'])) {
                    $aColsWidth = $aPrefs['colWidths'];
                }
                // Anchura del grid
                $grid_width = (!empty($aPrefs['widthGrid'])) ? $aPrefs['widthGrid'] : '900';
                // Altura del grid. Si no está en prefs: 0 para que calcule.
                $grid_height = (!empty($aPrefs['heightGrid'])) ? $aPrefs['heightGrid'] : 0;
                break; // sale del bucle.
            } else { // buscar las opciones por defecto
                continue;
            }
        }

        $c = 0;
        $cv = 0;
        $sColumns = '[';
        $sColumnsVisible = '[';
        $sColFilters = '[';
        $aFields = [];
        if ($b !== 0 || $b === 'x') {
            $c++;
            $width = isset($aColsWidth['sel']) ? $aColsWidth['sel'] : 30;
            $sColumns .= "{id: \"sel\", name: \"sel\", field: \"sel\", width:$width, sortable: false, formatter: checkboxSelectionFormatter}";
            if (!is_array($aColsVisible) || is_true($aColsVisible['sel'])) {
                $sColumnsVisible .= "{id: \"sel\", name: \"sel\", field: \"sel\", width:$width, sortable: false, formatter: checkboxSelectionFormatter},";
            }
        }
        foreach ($a_cabeceras as $Cabecera) {
            $visible = TRUE;
            if (is_array($Cabecera)) {
                $name = $Cabecera['name']; // esta tiene que existir siempre
                $name_idx = str_replace(' ', '', $name); // quito posibles espacios en el indice
                $id = !empty($Cabecera['id']) ? $Cabecera['id'] : str_replace(' ', '', $name); // quito posibles espacios en el indice
                $field = !empty($Cabecera['field']) ? $Cabecera['field'] : str_replace(' ', '', $name); // quito posibles espacios en el indice
                $toolTip = !empty($Cabecera['title']) ? ", toolTip: \"{$Cabecera['title']}\"" : ", toolTip: \"{$Cabecera['name']}\"";
                $class = !empty($Cabecera['class']) ? ", cssClass: \"{$Cabecera['class']}\"" : '';
                $sortable = !empty($Cabecera['sortable']) ? $Cabecera['sortable'] : 'true';
                $width = !empty($Cabecera['width']) ? $Cabecera['width'] : '';
                // asegurar que es sólo número (en pixels) no debe haber unidades (da error el javascript)
                $width = filter_var($width, FILTER_SANITIZE_NUMBER_INT);
                $formatter = !empty($Cabecera['formatter']) ? $Cabecera['formatter'] : '';
                if (!empty($Cabecera['visible'])) {
                    if (strtolower($Cabecera['visible'] ?? '') === 'no') {
                        $visible = FALSE;
                    }
                }
                $sDefCol = "id: \"$id\", name: \"$name\", field: \"$field\", sortable: $sortable" . $class . $toolTip;

                if (isset($aColsWidth[$name_idx])) {
                    $sDefCol .= ", width: " . $aColsWidth[$name_idx];
                } else {
                    if (!empty($width)) $sDefCol .= ", width: $width";
                }

                if (!empty($formatter)) $sDefCol .= ", formatter: $formatter";
                $sDefCol = "{" . $sDefCol . "}";
                $aFields[] = $field;
            } else {
                $name = $Cabecera;
                $name_idx = str_replace(' ', '', $Cabecera); // quito posibles espacios en el indice
                $toolTip = ", toolTip: \"$name\"";
                $sDefCol = "{id: \"$name_idx\", name: \"$name\", field: \"$name_idx\", sortable: true" . $toolTip;
                if (isset($aColsWidth[$name_idx])) {
                    //$sDefCol .= ", width: \"".$aColsWidth[$c]."\"";
                    $sDefCol .= ", width: " . $aColsWidth[$name_idx];
                }
                $sDefCol .= "}";
                $aFields[] = $name_idx;
            }
            if ((is_array($aColsVisible) && !empty($aColsVisible[$name_idx]) && ($aColsVisible[$name_idx] === "true")) || !is_array($aColsVisible)) {
                if (!$visible) continue;
                if ($cv > 0) {
                    $sColumnsVisible .= ',';
                }
                $sColumnsVisible .= $sDefCol;
                $cv++;
            }
            if ($c > 0) {
                $sColumns .= ',';
                $sColFilters .= ',';
            }
            $sColumns .= $sDefCol;
            $sColFilters .= "\"$name_idx\"";
            $c++;
        }
        $sColumns .= ']';
        $sColumnsVisible .= ']';
        $sColFilters .= ']';

        // Para generar un id único
        $ahora = date("Hms");
        $f = 1;
        $aFilas = [];
        foreach ($a_valores as $num_fila => $fila) {
            $f++;
            $id_fila = $f . $ahora;
            //uksort($fila,[$this, 'text_first']);
            ksort($fila);
            $icol = 0;
            $aFilas[$num_fila]["id"] = $id_fila;
            foreach ($fila as $col => $valor) {
                if ($col === "clase") {
                    $id = $valor;
                    $aFilas[$num_fila]["clase"] = addslashes($id);
                    continue;
                }
                if ($col === "order") {
                    continue;
                }
                if ($col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    if (empty($b)) {
                        continue;
                    } // si no hay botones (por permisos...) no tiene sentido el checkbox
                    //$col="";
                    if (is_array($valor)) {
                        if (!empty($valor['select'])) {
                            $chk = $valor['select'];
                        } else {
                            $chk = "";
                        }
                        $id = $valor['id'];
                    } else {
                        $id = $valor;
                    }
                    if (!empty($id)) {
                        if (in_array($id, $a_valores_chk)) {
                            $chk = 'checked';
                        } else {
                            $chk = '';
                        }
                        $aFilas[$num_fila]["sel"] = $chk . '#' . addslashes($id);
                    } else { // no hay que dibujar el checkbox, pero si la columna
                        $aFilas[$num_fila]["sel"] = '';
                    }
                } else {
                    if (is_array($valor)) {
                        $val = $valor['valor'];
                        if (!empty($valor['clase'])) {
                            $ira = $valor['clase'];
                            $aFilas[$num_fila]['clase'] = $ira;
                        }
                        if (!empty($valor['ira'])) {
                            $ira = $valor['ira'];
                            $aFilas[$num_fila]['ira'] = $ira;
                        }
                        if (!empty($valor['ira2'])) {
                            $ira = $valor['ira2'];
                            $aFilas[$num_fila]['ira2'] = $ira;
                        }
                        if (!empty($valor['ira3'])) {
                            $ira = $valor['ira3'];
                            $aFilas[$num_fila]['ira3'] = $ira;
                        }
                        if (!empty($valor['script'])) {
                            $ira = $valor['script'];
                            $aFilas[$num_fila]['script'] = addslashes($ira ?? '');
                        }
                        if (!empty($valor['script2'])) {
                            $ira = $valor['script2'];
                            $aFilas[$num_fila]['script2'] = addslashes($ira ?? '');
                        }
                        if (!empty($valor['script3'])) {
                            $ira = $valor['script3'];
                            $aFilas[$num_fila]['script3'] = addslashes($ira ?? '');
                        }
                        if (!empty($valor['span'])) {
                            $span = $valor['span'];
                            $aFilas[$num_fila][$aFields[$icol]] = addslashes($val ?? '');
                            $icol++;
                            for ($s = 1; $s < $span; $s++) {
                                $aFilas[$num_fila][$aFields[$icol]] = '';
                                $icol++;
                            }
                            $icol--;
                        } else {
                            $aFilas[$num_fila][$aFields[$icol]] = addslashes($val ?? '');
                        }
                    } else {
                        $aFilas[$num_fila][$aFields[$icol]] = empty($valor) ? '' : addslashes($valor ?? '');
                    }
                    $icol++;
                }
            }
        }

        $f = 0;
        $sData = '[';
        foreach ($aFilas as $num_fila => $fila) {
            $f++;
            if ($f > 1) $sData .= ',';
            $c = 0;
            $sData .= '{';
            foreach ($fila as $camp => $valor) {
                $c++;
                if ($c > 1) $sData .= ',';
                // Sólo elimino los saltos de lineas. las comillas las pone bien.
                $remove = array("\r\n", "\n", "\r");
                $valor = str_replace($remove, ' ', $valor);
                $sData .= "\"$camp\": \"$valor\"";
                //$sData .= "\"$camp\": ".json_encode($valor); //para los saltos de linea. json ya pone comillas.
                //$sData .= "\"$camp\": decodeURIComponent('". rawurlencode($valor)."')"; //para los saltos de linea.
            }
            $sData .= '}';
        }
        $sData .= ']';

        // calculo la altura de la tabla
        if (empty($grid_height) && $f < 12) {
            $grid_height = (3 + $f) * 25; // +4 (cabecera y última linea en blanco). 25 = rowheight
            // mínimo, porque sino al desplegar el cuadro de búsqueda tapa tota la información
            $grid_height = ($grid_height < 200) ? 200 : $grid_height;
        } else {
            $grid_height = empty($grid_height) ? 350 : $grid_height;
        }


        $tt = "<input id=\"scroll_id\" name=\"scroll_id\" value=\"$scroll_id\" type=\"hidden\">";
        $tt .= "
			<script>
			
			var dataView_$id_tabla;
			var grid_$id_tabla;
			var columns_$id_tabla = $sColumnsVisible;
			var columnsAll_$id_tabla = $sColumns;
			var data_$id_tabla = $sData;

            var resizer = new Slick.Plugins.Resizer({
              container: '#grid_$id_tabla',
              rightPadding: 5,
              bottomPadding: 5,
              minHeight: 80,
              minWidth: 200,
              calculateAvailableSizeBy:  'container',
            });
            //grid.registerPlugin(resizer);	
 
			var options = {
                enableAutoResize: true
                ,enableCellNavigation: true
                ,enableAddRow: false
                ,enableColumnReorder: true
                ,topPanelHeight: 50
                ,autoHeight: false
                ,syncColumnCellResize: true
                ,autosizeColumns: true
                ,autosizeColsMode: Slick.GridAutosizeColsMode.LegacyForceFit
			};
            
			var sortcol = \"" . $sortcol . "\";
			var sortdir = 1;
			var searchString = \"\";
			var columnFilters_$id_tabla = $sColumns;
			
			function metadata(previousItemMetadata) {
                return (rowNumber) => {
                    const item = dataView_$id_tabla.getItem(rowNumber);
                    
                    let meta = {
                        cssClasses: ''
                      };
                    if (typeof previousItemMetadata === 'object') {
                        meta = previousItemMetadata(rowNumber);
                    }

                    if (meta && item && item.clase) {
                        meta.cssClasses = (meta.cssClasses || '') + ' ' + item.clase;
                    }

                    return meta;
				};
			}
			
			function add_scroll_id(row) {
				$(\"#scroll_id\").val(row);
				//console.log(row);
			}
            
			function resumeAutoResize() {
              resizer.pauseResizer(false);
              resizer.resizeGrid();
              // you could also delay the resize (in milliseconds)
              resizer.resizeGrid(500);
            }
    
			function clickFormatter(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"'); return false; \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script']) {
					//return \"<span class=link onclick='grid_$id_tabla.setSelectedRows([\"+row+\"]); setTimeout(\"+ira+\",5000); return false;' >\"+value+\"</span>\";
                    // asegurarme que:
                    // - No se propaga el onclick
                    // - primero acaba con lo de seleccionar la fila
                    var fun = \"event.stopPropagation(); (function (s) { \"+ira+\"; })(grid_$id_tabla.setSelectedRows([\"+row+\"]))\";
					return \"<span class=link onclick=' \"+fun+\"; return false;' >\"+value+\"</span>\";
				}
				return value;
			}
			function clickFormatter2(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira2']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"'): return false; \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script2']) {
					return \"<span class=link onclick='\"+ira+\"; return false;' >\"+value+\"</span>\";
				}
				return value;
			}
			function clickFormatter3(row, cell, value, columnDef, dataContext) {
				if (ira=dataContext['ira3']) {
					return \"<span class=link onclick=\\\"fnjs_update_div('#main','\"+ira+\"'): return false; \\\" >\"+value+\"</span>\";
				}
				if (ira=dataContext['script3']) {
					return \"<span class=link onclick='\"+ira+\"; return false;' >\"+value+\"</span>\";
				}
				return value;
			}
			function checkboxSelectionFormatter(row, cell, value, columnDef, dataContext) {
				if (value == null || value === \"\") {
				  return \"\";
				} else {
				  // formato: checked#id#nom_activ [#otro#otro..n]
				  var array_val=value.split('#');
				  var chk = array_val[0];
				  if (chk.length) {
				  	chk = 'checked=\"checked\"';
					// Desactivo el formateo, porque si se seleccionan varias filas, esto borra las anteriores.
					//grid_$id_tabla.setSelectedRows([row]);
				  }
				  var val = '';
				  $.each(array_val, function(index, value) {
					if (index==0) return true;
					if (index>1) {
						val = val+'#';
					}
						val = val+value.replace(/\\\"/g,\"'\");
				  });
				  var id = '#'+val;
				  return  \"<input class=\\\"sel\\\" type=\\\"checkbox\\\" name=\\\"sel[]\\\" id=\\\"\"+id+\"\\\" value=\\\"\"+val+\"\\\" \"+chk+\">\";
				}
			}
			
			";

        // Formato de fecha:
        $idioma = $_SESSION['session_auth']['idioma'];
        # Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
        if (!isset($idioma)) {
            $idioma = $_SESSION['oConfig']->getIdioma_default();
        }
        $a_idioma = explode('.', $idioma);
        $code_lng = $a_idioma[0];
        //$code_char = $a_idioma[1];
        switch ($code_lng) {
            case 'en_US':
                // formato = mes/dia/año;
                $fecha_local = "
                    // OJO moth is 0 index => restar 1.
					var date_a = new Date(fecha_a[2], fecha_a[0]-1, fecha_a[1], hora_a[0], hora_a[1], hora_a[2]);
					var date_b = new Date(fecha_b[2], fecha_b[0]-1, fecha_b[1], hora_b[0], hora_b[1], hora_b[2]);
                    ";
                break;
            default:
                // formato = dia/mes/año;
                $fecha_local = "
                    // OJO moth is 0 index => restar 1.
					var date_a = new Date(fecha_a[2], fecha_a[1]-1, fecha_a[0], hora_a[0], hora_a[1], hora_a[2]);
					var date_b = new Date(fecha_b[2], fecha_b[1]-1, fecha_b[0], hora_b[0], hora_b[1], hora_b[2]);
                    ";
        }

        $tt .= "
			// Define search filter
			function myFilter_$id_tabla(item,args) {
				var searchFields = $sColFilters;
				if (args.searchString != \"\") {
					var searchWord = args.searchString.toUpperCase();
					var itemFound = false;
					for (var i = 0; i < searchFields.length; i++) {
						if (item[searchFields[i]] != undefined){
							if (item[searchFields[i]].toUpperCase().indexOf(searchWord) != -1){
								itemFound = true;
							}
						}
					}
					if (itemFound === false){
						return false;
					}
				}
				return true;
			}
			function comparer(a,b) {
				var dateformat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4}$/;
				var dateTimeFormat = /^\d{1,2}(\-|\/|\.)\d{1,2}(\-|\/|\.)\d{2,4} \d{2}:\d{2}:\d{2}$/;
				
				if ( dateTimeFormat.test(a[sortcol]) && dateTimeFormat.test(b[sortcol]) ) {
					var dateTime_a = a[sortcol].split(' ');
					var dateTime_b = b[sortcol].split(' ');
					var fecha_a = dateTime_a[0].split('/');
					var hora_a = dateTime_a[1].split(':');
					var fecha_b = dateTime_b[0].split('/');
					var hora_b = dateTime_b[1].split(':');
                ";
        $tt .= $fecha_local;
        $tt .= "
					var diff = date_a.getTime()-date_b.getTime();
					return (diff==0?diff:diff/Math.abs(diff));
				}
				if ( dateformat.test(a[sortcol]) && dateformat.test(b[sortcol]) ) {
					var fecha_a = a[sortcol].split('/');
					var fecha_b = b[sortcol].split('/');
                ";
        $tt .= $fecha_local;
        $tt .= "
					var diff = date_a.getTime()-date_b.getTime();
					return (diff==0?diff:diff/Math.abs(diff));
				} else {
					var x = a[sortcol], y = b[sortcol];
					if (isNaN(x) || isNaN(y)) {
						x=x.toUpperCase();
						y=y.toUpperCase();
						x=fnjs_sin_acentos(x);
						y=fnjs_sin_acentos(y);
						return (x == y ? 0 : (x > y ? 1 : -1));
					} else {
						int_a=parseInt(a[sortcol],10);
						int_b=parseInt(b[sortcol],10);
						return (int_a == int_b ? 0 : (int_a > int_b ? 1 : -1));
					}
				}
			}
			";
        $tt .= "
			function toggleFilterRow_$id_tabla() {
			  if ($(grid_$id_tabla.getTopPanel()).is(\":visible\")) {
				grid_$id_tabla.setTopPanelVisibility(false);
			  } else {
				grid_$id_tabla.setTopPanelVisibility(true);
			  }
			}
			";

        $tt .= "
			$(\".grid-header .ui-icon\")
				.addClass(\"ui-state-default ui-corner-all\")
				.on(\"mouseover\", function (e) {
				  $(e.target).addClass(\"ui-state-hover\")
				})
				.on(\"mouseout\", function (e) {
				  $(e.target).removeClass(\"ui-state-hover\")
				});
				
				
			$(function () {
				dataView_$id_tabla = new Slick.Data.DataView();
				grid_$id_tabla = new Slick.Grid(\"#grid_$id_tabla\", dataView_$id_tabla, columns_$id_tabla, options);
				grid_$id_tabla.setSelectionModel(new Slick.RowSelectionModel());
				grid_$id_tabla.registerPlugin(new Slick.AutoTooltips());
				grid_$id_tabla.registerPlugin(resizer);
				//grid_$id_tabla.registerPlugin(new Slick.AutoColumnSize());
				
				var pager = new Slick.Controls.Pager(dataView_$id_tabla, grid_$id_tabla, $(\"#pager\"));
				var columnpicker = new Slick.Controls.ColumnPicker(columnsAll_$id_tabla, grid_$id_tabla, options);
				
				// move the filter panel defined in a hidden div into grid top panel
				$(\"#inlineFilterPanel_" . $id_tabla . "\")
				  .appendTo(grid_$id_tabla.getTopPanel())
				  .show();
				  
				dataView_$id_tabla.getItemMetadata = metadata(dataView_$id_tabla.getItemMetadata);
				
				grid_$id_tabla.onClick.subscribe(function (e,args) {
					add_scroll_id(args.row);
					grid_$id_tabla.setSelectedRows([args.row]);
				    //console.log(args.row);
					//e.stopPropagation();
				});
				
				grid_$id_tabla.onSelectedRowsChanged.subscribe(function (e,args) {
					$.when($(\"input:checkbox\").prop('checked', false));
					$.when($(\".selected input:checkbox\").prop('checked', true));
					//e.stopPropagation();
				});
				
				grid_$id_tabla.onCellChange.subscribe(function (e, args) {
					dataView_$id_tabla.updateItem(args.item.id, args.item);
				});
				
				grid_$id_tabla.onAddNewRow.subscribe(function (e, args) {
					var item = {\"num\": data_$id_tabla.length, \"id\": \"new_\" + (Math.round(Math.random() * 10000)), \"title\": \"New task\", \"duration\": \"1 day\", \"percentComplete\": 0, \"start\": \"01/01/2009\", \"finish\": \"01/01/2009\", \"effortDriven\": false};
					$.extend(item, args.item);
					dataView_$id_tabla.addItem(item);
				});
				
				grid_$id_tabla.onKeyDown.subscribe(function (e) {
				  // select all rows on ctrl-a
				  if (e.which != 65 || !e.ctrlKey) {
					return false;
				  }
				  
				  var rows = [];
				  for (var i = 0; i < dataView_$id_tabla.getLength(); i++) {
					rows.push(i);
				  }
				  
				  grid_$id_tabla.setSelectedRows(rows);
				  e.preventDefault();
				});
				
				grid_$id_tabla.onSort.subscribe(function (e, args) {
					sortdir = args.sortAsc ? 1 : -1;
					sortcol = args.sortCol.field;
					
					dataView_$id_tabla.sort(comparer, args.sortAsc);
				});
				// wire up model events to drive the grid
				dataView_$id_tabla.onRowCountChanged.subscribe(function (e, args) {
					grid_$id_tabla.updateRowCount();
					grid_$id_tabla.render();
				});
				
				dataView_$id_tabla.onRowsChanged.subscribe(function (e, args) {
					grid_$id_tabla.invalidateRows(args.rows);
					grid_$id_tabla.render();
				});
				
				dataView_$id_tabla.onPagingInfoChanged.subscribe(function (e, pagingInfo) {
					var isLastPage = pagingInfo.pageSize * (pagingInfo.pageNum + 1) - 1 >= pagingInfo.totalRows;
					var enableAddRow = isLastPage || pagingInfo.pageSize == 0;
					var options = grid_$id_tabla.getOptions();
					
					if (options.enableAddRow != enableAddRow) {
					  grid_$id_tabla.setOptions({enableAddRow: enableAddRow});
					}
				});
				
				// wire up the search textbox to apply the filter to the model
				$(\"#txtSearch_" . $id_tabla . "\").on(\"keydown\", function (e) {
				    // No hacer nada si es el 'enter'
				    if (e.keyCode == 13) {
					  return false;
				    }
                });
				$(\"#txtSearch_" . $id_tabla . "\").on(\"keyup\", function (e) {
					Slick.GlobalEditorLock.cancelCurrentEdit();
					// clear on Esc
					if (e.which == 27) {
					  this.value = \"\";
					}
					searchString = this.value;
					updateFilter();
				});
				
				function updateFilter() {
					dataView_$id_tabla.setFilterArgs({
						searchString: searchString
					});
					dataView_$id_tabla.refresh();
				}
			";

        if ($sortcol) {
            $tt .= " data_$id_tabla.sort(comparer); ";
        }

        $tt .= "
			// initialize the model after all the events have been hooked up
			dataView_$id_tabla.beginUpdate();
			dataView_$id_tabla.setItems(data_$id_tabla);
			dataView_$id_tabla.setFilterArgs({
				searchString: searchString
			});
			dataView_$id_tabla.setFilter(myFilter_$id_tabla);
			dataView_$id_tabla.endUpdate();
			$(\"#grid_$id_tabla\").resizable();
		";

        if (isset($scroll_id)) {
            $tt .= " grid_$id_tabla.scrollRowToTop($scroll_id);";
        }
        if ($bPanelVis) {
            $tt .= "toggleFilterRow_$id_tabla();";
        }

        $tt .= "
			var container = $(grid_$id_tabla.getContainerNode());
			var h_header =  $('.grid-header').height();
			var vph = $grid_height - h_header;
			container.height(vph);
			grid_$id_tabla.resizeCanvas();
		  })
		</script>
		";


        //OJO. si height no es auto, al hacer resize desde html, los textos de debajo de la grid no se mueven.
        $ta = "<div id=\"GridContainer_" . $id_tabla . "\"  style=\"width:{$grid_width}px; height:auto;\" >
		<div class=\"grid-header\">
          <span style=\"width:90%; display: inline-block;\">$botones</span>
		  <span style=\"float:right\" class=\"ui-icon ui-icon-disk\" title=\"" . _("guardar selección de columnas") . "\"
				onclick=\"fnjs_def_tabla('" . $id_tabla . "')\"></span>
		  <span style=\"float:right\" class=\"ui-icon ui-icon-search\" title=\"" . _("ver/ocultar panel de búsqueda") . "\"
				onclick=\"toggleFilterRow_$id_tabla()\"></span>
		</div>
		<div id=\"grid_$id_tabla\"  style=\"width:{$grid_width}px; height:{$grid_height}px;\" onresize=\"resumeAutoResize()\" ></div>
		";
        $ta .= "</div>";

        $ta .= "
		<div id=\"inlineFilterPanel_" . $id_tabla . "\" style=\"background:#dddddd;padding:3px;color:black;\">
		  " . _("Buscar en todas las columnas") . " <input type=\"text\" id=\"txtSearch_" . $id_tabla . "\">
		</div>
		";

        //$ta .= "<button onmouseenter=\"resumeAutoResize()\">Resume Auto-Resize</button>";

        $tb = $ta . $tt;

        return $tb;
    }


    /**
     * Muestra una tabla ordenable, con  botones en la cabecera y check box en cada lina.
     *
     * @return string Html
     *
     */
    function mostrar_tabla_html()
    {
        $a_botones = $this->aBotones;
        $a_cabeceras = $this->aCabeceras;
        $a_valores = $this->aDatos;
        $id_tabla = $this->sid_tabla;

        $botones = "";
        $cabecera = "";
        $tbody = "";
        $tt = "";
        $clase = "";
        $chk = "";
        if (empty($a_valores)) {
            return _("no hay ninguna fila");
        }
        if (!empty($a_botones)) {
            if ($a_botones === "ninguno") {
                $b = "x";
            } else {
                $b = 0;
                foreach ($a_botones as $a_boton) {
                    $prefix = empty($a_boton['prefix']) ? '' : $a_boton['prefix'] . '_';
                    $btn = $prefix . "btn" . $b++;
                    $botones .= "<INPUT id='$btn' name='$btn' type=button value=\"" . $a_boton['txt'] . "\" onClick='" . $a_boton['click'] . "'>";
                }
                $botones .= "</td></tr>";
            }
        }
        // para los grupos
        if ($this->botones_grupo) {
            $b = 5; // número cualquiera, para que pinte el checkbox
        }

        $cab = 1;
        foreach ($a_cabeceras as $Cabecera) {
            $class = '';
            $width = '';
            if (is_array($Cabecera)) {
                $name = $Cabecera['name']; // esta tiene que existir siempre
                if (!empty($Cabecera['class'])) {
                    $class = "class=\"{$Cabecera['class']}\"";
                }
                if (!empty($Cabecera['width'])) {
                    $width = "width=\"{$Cabecera['width']}\"";
                }

            } else {
                $name = $Cabecera;
            }
            if (!empty($name)) {
                $cabecera .= "<th class=cabecera $width $class >" . trim($name) . "</th>\n";
            } else {
                $cabecera .= "<th class=cabecera tipo='notext' $width $class ></th>\n";
            }
            $cab++;
        }
        $cabecera .= "</tr>\n";
        // Para generar un id único
        $ahora = date("Hms");
        $f = 1;

        if (isset($a_valores['select'])) {
            $a_valores_chk = $a_valores['select'];
            unset($a_valores['select']);
        } else {
            $a_valores_chk = [];
        }

        unset($a_valores['scroll_id']);
        foreach ($a_valores as $num_fila => $fila) {
            $clase = "imp";
            $f % 2 ? 0 : $clase = "par";
            $f++;
            $id_fila = $f . $ahora;
            uksort($fila, [$this, 'text_first']);
            if (!empty($fila['clase'])) {
                $clase .= " " . $fila['clase'];
            }
            $tbody .= "<tr id='$id_fila' class='$clase'>";
            foreach ($fila as $col => $valor) {
                if ($col === "clase") {
                    continue;
                }
                if ($col === "order") {
                    continue;
                }
                if ($col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    if (empty($b)) continue; // si no hay botones (por permisos...) no tiene sentido el checkbox
                    $col = "";
                    if (is_array($valor)) {
                        if (!empty($valor['select'])) {
                            $chk = $valor['select'];
                        } else {
                            $chk = "";
                        }
                        $id = $valor['id'];
                    } else {
                        $id = $valor;
                    }
                    if (!empty($id)) {
                        if (!empty($a_valores_chk)) {
                            if (in_array($id, $a_valores_chk)) {
                                $chk = 'checked';
                            } else {
                                $chk = '';
                            }
                        }
                        $tbody .= "<td tipo='sel' title='" . _("clic para seleccionar") . "'>";
                        $tbody .= "<input class='sel' type='checkbox' $chk  name='sel[]' id='a$id' value='$id'>";
                        $tbody .= "</td>";
                    } else { // no hay que dibujar el checkbox, pero si la columna
                        $tbody .= "<td></td>";
                    }
                } elseif (is_array($valor)) {
                    $val = $valor['valor'];
                    if (!empty($valor['ira'])) {
                        $ira = $valor['ira'];
                        $tbody .= "<td><span class=\"link\" onclick=\"fnjs_update_div('#main','$ira')\" >$val</span></td>";
                    } else {
                        $tbody .= "<td>$val</td>";
                    }
                    if (!empty($valor['ira2'])) {
                        $ira = $valor['ira2'];
                        $tbody .= "<td><span class=\"link\" onclick=\"fnjs_update_div('#main','$ira')\" >$val</span></td>";
                    }
                    if (!empty($valor['ira3'])) {
                        $ira = $valor['ira3'];
                        $tbody .= "<td><span class=\"link\" onclick=\"fnjs_update_div('#main','$ira')\" >$val</span></td>";
                    }
                    if (!empty($valor['script'])) {
                        $ira = $valor['script'];
                        $tbody .= "<td><span class=\"link\" onclick='$ira' >$val</span></td>";
                    }
                    if (!empty($valor['script2'])) {
                        $ira = $valor['script2'];
                        $tbody .= "<td><span class=\"link\" onclick='$ira' >$val</span></td>";
                    }
                    if (!empty($valor['script3'])) {
                        $ira = $valor['script3'];
                        $tbody .= "<td><span class=\"link\" onclick='$ira' >$val</span></td>";
                    }
                    if (!empty($valor['span'])) {
                        $tbody .= "<td colspan='" . $valor['span'] . "'>$val</td>";
                    }
                } else {
                    $valor = $valor ?? '';
                    // si es una fecha, pongo la clase fecha, para exportar a excel...
                    if (preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
                        list($d, $m, $y) = preg_split('/[:\/\.-]/', $valor);
                        $fecha_iso = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));
                        $tbody .= "<td class='fecha' fecha_iso='$fecha_iso'>$valor</td>";
                    } else {
                        $tbody .= "<td>$valor</td>";
                    }
                }
            }
            $tbody .= "</tr>\n";
        }

        if (!empty($b) && $b !== 'x') {
            $botones = "<tr class=botones><td colspan='$cab'>" . $botones;
        }
        // No puedo poner los botones como thead y tbody porque el sorteable.js se hace un lio.
        $tt = "<table>$botones</table>\n";
        $tt .= "<table border=1  class='sortable' id='$id_tabla'>\n";
        $tt .= "<thead><tr>";
        if (!empty($b)) $tt .= "<th class='unsortable' tipo='notext'></th>";
        $tt .= "$cabecera</thead><tbody>";
        $tt .= $tbody;
        $tt .= "</tbody></table>\n";
        $tt .= "<script>
			$(document).ready(function() {
				var h = $('input:checked');
				if (h.length) {
					var h = (h.offset().top) - 300;
					$('#main').scrollTop(h);
				}
			});
			</script>";

        return $tt;
    }

    public function text_first($a, $b)
    {
        if (is_numeric($a)) {
            if (is_numeric($b)) {
                if ($a === $b) {
                    $rta = 0;
                } else {
                    $rta = ($a < $b) ? -1 : 1;
                }
            } else {
                $rta = 1;
            }
        } else {
            if (is_numeric($b)) {
                $rta = -1;
            } else {
                $rta = strcasecmp($a, $b);
            }
        }
        return $rta;
    }

    public function getCsv($filename)
    {
        $a_valores = $this->aDatos;

        // http headers for downloads
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Content-Transfer-Encoding: binary");

        $fp = fopen('php://output', 'w');
        foreach ($a_valores as $num_fila => $fila) {
            $a_valores_simple = [];
            uksort($fila, [$this, 'text_first']);
            //$tbody.="<tr id='$id_fila' class='$clase'>";
            foreach ($fila as $col => $valor) {
                if ($col === "clase") {
                    continue;
                }
                if ($col === "order") {
                    continue;
                }
                if ($col === "select") {
                    continue;
                }
                if ($col === "sel") {
                    if (is_array($valor)) {
                        $id = $valor['id'];
                    } else {
                        $id = $valor;
                    }
                    if (!empty($id)) {
                        $a_valores_simple[] = $id;
                    }
                } elseif (is_array($valor)) {
                    $val = $valor['valor'];
                    $a_valores_simple[] = $val;
                } else {
                    // si es una fecha, pongo la clase fecha, para exportar a excel...
                    if (preg_match("/^(\d)+[\/-](\d)+[\/-](\d\d)+$/", $valor)) {
                        list($d, $m, $y) = preg_split('/[:\/\.-]/', $valor);
                        $fecha_iso = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));
                        //$tbody.="<td class='fecha' fecha_iso='$fecha_iso'>$valor</td>";
                        $a_valores_simple[] = $fecha_iso;
                    } else {
                        //$tbody.="<td>$valor</td>";
                        $a_valores_simple[] = $valor;
                    }
                }
            }
            fputcsv($fp, $a_valores_simple, "\t", '"');
        }
        fclose($fp);

    }

    /* MÉTODOS GET y SET ----------------------------------------------------------*/

    public function setGrupos($aGrupos)
    {
        $this->aGrupos = $aGrupos;
    }

    public function setCabeceras($aCabeceras)
    {
        $this->aCabeceras = $aCabeceras;
    }

    public function setPie($str)
    {
        $this->sPie = $str;
    }

    public function setSortCol($ssortcol)
    {
        $this->ssortCol = str_replace(' ', '', $ssortcol);
    }

    public function setColVisible($aColVisible)
    {
        $this->aColVisible = $aColVisible;
    }

    public function setDatos($aDatos)
    {
        $this->aDatos = $aDatos;
    }

    public function setBotones($aBotones)
    {
        $this->aBotones = $aBotones;
    }

    public function setId_tabla($sid_tabla)
    {
        $this->sid_tabla = $sid_tabla;
    }

    public function setFiltro($bFiltro)
    {
        $this->bFiltro = $bFiltro;
    }

    public function setColVis($bColVis)
    {
        $this->bColVis = $bColVis;
    }

    public function setRecordar($bRecordar)
    {
        $this->bRecordar = $bRecordar;
    }

    public function setFormatoTabla($formatoTabla)
    {
        $this->formato_tabla = $formatoTabla;
    }
}