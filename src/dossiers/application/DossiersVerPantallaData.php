<?php

namespace src\dossiers\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\domain\DatosTablaRepo;
use src\shared\infrastructure\ProvidesRepositories;
use src\personas\domain\entity\Persona;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use frontend\shared\web\Lista;
use frontend\shared\security\HashFront;

/**
 * Cuerpo de dossiers_ver: cabecera (campos) + lista o ficha (HTML).
 * Las URLs van como placeholders `__ORBIX_DSG_*__`; `url_specs` mapea cada placeholder
 * a `{path, query}` y se firma en `dossiers_ver_pantalla_data.php`.
 *
 * @return array{error?: string, top_html: string, web_icons: string, modo: 'lista'|'ficha', cuerpo_html: string, lista_a_filas?: list<array<string, mixed>>, url_specs: array<string, array{path: string, query: array<string, mixed>}>}
 */
class DossiersVerPantallaData
{
    private const PH_GODOSSIERS = '__ORBIX_DSG_godossiers__';

    private const PH_GO_HOME = '__ORBIX_DSG_go_home__';

    private static function phActionDatos(int $idDossier): string
    {
        return '__ORBIX_DSG_action_datos_' . $idDossier . '__';
    }

    private const PH_INS_TRASLADO = '__ORBIX_DSG_ins_traslado__';

    public static function build(array $post): array
    {
        $Qrefresh = (int)($post['refresh'] ?? 0);
        $a_sel = isset($post['sel']) ? (array)$post['sel'] : [];
        if ($a_sel === []) {
            $a_sel = null;
        }
        $Qmod = (string)($post['mod'] ?? '');
        if (isset($a_sel) && ($Qmod === 'eliminar' || $Qmod === 'nuevo')) {
            $a_sel = null;
        }

        $Qid_sel = '';
        $Qscroll_id = (int)($post['scroll_id'] ?? 0);
        $stack = '';
        if (isset($post['stack']) && (string)$post['stack'] !== '') {
            $stack = (string)filter_var($post['stack'], FILTER_SANITIZE_NUMBER_INT);
            if ($stack !== '') {
                // Parámetros restaurados por el controller frontend vía $oPosicion.
                if (array_key_exists('restored_id_sel', $post)) {
                    $Qid_sel = $post['restored_id_sel'];
                }
                if (array_key_exists('restored_scroll_id', $post)) {
                    $Qscroll_id = (int) $post['restored_scroll_id'];
                }
            }
        } elseif (!empty($a_sel)) {
            $Qid_sel = $a_sel;
        }

        $Qid_pau = (int)($post['id_pau'] ?? 0);
        $pau = (string)($post['pau'] ?? '');
        $Qobj_pau = (string)($post['obj_pau'] ?? '');
        $Qid_dossier = (string)($post['id_dossier'] ?? '');
        $Qpermiso = (string)($post['permiso'] ?? '');
        $QqueSel = (string)($post['queSel'] ?? '');
        $Qclase_info_encoded = (string)($post['clase_info'] ?? '');

        if (empty($Qid_dossier) && !empty($Qclase_info_encoded)) {
            $obj = urldecode($Qclase_info_encoded);
            $oInfoClase = new $obj();
            if (method_exists($oInfoClase, 'setObj_pau')) {
                $oInfoClase->setObj_pau($Qobj_pau);
            }
            $Qid_dossier = (string)$oInfoClase->getId_dossier();
            $pau = $oInfoClase->getPau();
        }

        if (!empty($Qrefresh)) {
            $id_pau = $Qid_pau;
        } elseif (!empty($a_sel)) {
            $id_pau = (int)strtok($a_sel[0], "#");
        } else {
            $id_pau = $Qid_pau;
        }

        $Qid_activ = 0;
        $Qmodo_curso = 0;

        switch ($QqueSel) {
            case "activ":
                $pau = "p";
                $Qpermiso = "3";
                break;
            case "matriculas":
                $Qid_activ = (int)($post['id_activ'] ?? 0);
                $pau = "p";
                $Qpermiso = "3";
                if ($Qmod === "sel_es_asistente" && !empty($a_sel)) {
                    $id_pau = (int)strtok($a_sel[0], "#");
                }
                break;
            case "asis":
                $pau = "a";
                $Qpermiso = "3";
                $Qid_dossier = "3101";
                break;
            case "asig":
                $pau = "a";
                $Qpermiso = "3";
                $Qid_dossier = "3005";
                break;
            case "carg":
                $pau = "a";
                $Qpermiso = "3";
                $Qid_dossier = "3102";
                break;
            default:
                break;
        }

        $repositoryProvider = new class {
            use ProvidesRepositories;

            public function get(string $entityType): object
            {
                return $this->getRepository($entityType);
            }
        };
        $getRepository = function (string $obj_pau) use ($repositoryProvider) {
            return $repositoryProvider->get($obj_pau);
        };

        $urlSpecs = [];
        $urlSpecs[self::PH_GODOSSIERS] = [
            'path' => 'frontend/dossiers/controller/dossiers_ver.php',
            'query' => ['pau' => $pau, 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau],
        ];
        $godossiersPh = self::PH_GODOSSIERS;

        switch ($pau) {
            case 'p':
                if (empty($Qobj_pau) || $Qobj_pau === 'Persona') {
                    $oPersona = Persona::findPersonaEnGlobal($id_pau);
                    if (!is_object($oPersona)) {
                        return [
                            'error' => "<br>No encuentro a nadie con id_nom: $id_pau en  " . __FILE__ . ': line (Persona lookup)',
                            'url_specs' => [],
                        ];
                    }
                    $clase = get_class($oPersona);
                    $Qobj_pau = implode('', array_slice(explode('\\', $clase), -1));
                } else {
                    $repo = $getRepository($Qobj_pau);
                    $oPersona = $repo->findById($id_pau);
                }
                $nom_cabecera = $oPersona->getNombreApellidos();
                $urlSpecs[self::PH_GO_HOME] = [
                    'path' => 'frontend/personas/controller/home_persona.php',
                    'query' => ['id_nom' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            case 'u':
                $repo = $getRepository($Qobj_pau);
                $oUbi = $repo->findById($id_pau);
                $nom_cabecera = $oUbi->getNombre_ubi();
                $urlSpecs[self::PH_GO_HOME] = [
                    'path' => 'frontend/ubis/controller/home_ubis.php',
                    'query' => ['id_ubi' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            case 'a':
                $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
                $oActividad = $ActividadAllRepository->findById($id_pau);
                $nom_cabecera = $oActividad->getNom_activ();
                $urlSpecs[self::PH_GO_HOME] = [
                    'path' => 'frontend/actividades/controller/actividad_ver.php',
                    'query' => ['id_activ' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            default:
                return ['error' => 'pau desconocido', 'url_specs' => []];
        }

        $goHomePh = self::PH_GO_HOME;

        $alt = _("ver dossiers");
        $dos = _("dossiers");
        $web_icons = ConfigGlobal::getWeb_icons();
        $titulo = "<span class=link onclick=fnjs_update_div('#main','$goHomePh')>$nom_cabecera</span>";

        $top_html = '<div id="top">'
            . '<table><tr>'
            . '<td><span class="link" onclick="fnjs_update_div(\'#main\',\'' . $godossiersPh . '\')">'
            . '<img src="' . $web_icons . '/dossiers.gif" width="40" height="40" alt="' . htmlspecialchars($alt) . '">( ' . $dos . ' )</span></td>'
            . '<td class="titulo">' . $titulo . '</td>'
            . '</tr></table></div>';

        if (empty($Qid_dossier)) {
            $lista = DossiersListaFichasData::build($pau, $id_pau, $Qobj_pau);
            return [
                'top_html' => $top_html,
                'web_icons' => $web_icons,
                'modo' => 'lista',
                'cuerpo_html' => '',
                'lista_a_filas' => $lista['a_filas'],
                'url_specs' => $urlSpecs,
            ];
        }

        $cuerpo = '';
        $id_dossier = strtok($Qid_dossier, "y");
        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        while ($id_dossier) {
            $nom_bloque = 'ficha' . $id_dossier;
            $bloque = '#ficha' . $id_dossier;
            $cuerpo .= "<div id=\"$nom_bloque\">";
            $oTipoDossier = $TipoDossierRepository->findById($id_dossier);
            $oResSelect = DossierTipoFileSuffixResolver::fromDefaultProjectRoot();
            $nameClaseSelect = $oResSelect->resolveSelectClassFqcn($oTipoDossier) ?? '';

            if (!empty($nameClaseSelect)) {
                $claseSelect = new $nameClaseSelect();
                $claseSelect->setId_dossier($id_dossier);
                $claseSelect->setPau($pau);
                $claseSelect->setObj_pau($Qobj_pau);
                $claseSelect->setId_pau($id_pau);
                $claseSelect->setPermiso($Qpermiso);
                $claseSelect->setBloque($bloque);
                $claseSelect->setQueSel($QqueSel);
                if (method_exists($claseSelect, 'setStackActual')) {
                    $claseSelect->setStackActual((int)($post['stack_actual'] ?? 0));
                }

                if (isset($post['stack']) && (string)$stack !== '') {
                    $claseSelect->setQId_sel($Qid_sel);
                    $claseSelect->setQScroll_id($Qscroll_id);
                }

                switch ((int)$id_dossier) {
                    case 1301:
                    case 1302:
                        $Qmodo_curso = (int)($post['modo_curso'] ?? 0);
                        $claseSelect->setModo_curso($Qmodo_curso);
                        break;
                    case 1303:
                        if (!empty($Qid_activ)) {
                            $claseSelect->setQId_activ($Qid_activ);
                        }
                        break;
                }
                $cuerpo .= $claseSelect->getHtml();
            } else {
                $clase = $oTipoDossier->getClass();
                $app = $oTipoDossier->getApp();
                $clase_info = "src\\$app\\domain\\Info$clase";
                $Qclase_info_enc = urlencode($clase_info);
                $oInfoClase = new $clase_info();
                $oInfoClase->setId_pau($id_pau);
                if (method_exists($oInfoClase, 'setObj_pau')) {
                    $oInfoClase->setObj_pau($Qobj_pau);
                }
                $oDatosTabla = new DatosTablaRepo();
                $oDatosTabla->setBloque($bloque);
                $oDatosTabla->setExplicacion_txt($oInfoClase->getTxtExplicacion());
                $oDatosTabla->setEliminar_txt($oInfoClase->getTxtEliminar());
                $oDatosTabla->setColeccion($oInfoClase->getColeccion());
                $oDatosTabla->setId_sel($Qid_sel);
                $oDatosTabla->setScroll_id($Qscroll_id);

                $aQuery = [
                    'clase_info' => $Qclase_info_enc,
                    'id_pau' => $id_pau,
                    'bloque' => $bloque,
                    'permiso' => $Qpermiso,
                    'obj_pau' => $Qobj_pau,
                ];
                $phAction = self::phActionDatos((int) $id_dossier);
                $urlSpecs[$phAction] = [
                    'path' => 'frontend/dossiers/controller/dossiers_ver.php',
                    'query' => $aQuery,
                ];
                $oDatosTabla->setAction_tabla($phAction);

                $oHashSelect = new HashFront();
                $oHashSelect->setCamposForm('mod');
                $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
                $a_camposHidden = [
                    'clase_info' => $Qclase_info_enc,
                    'pau' => $pau,
                    'id_pau' => $id_pau,
                    'obj_pau' => $Qobj_pau,
                    'permiso' => $Qpermiso,
                    'bloque' => $bloque,
                ];
                $oHashSelect->setArraycamposHidden($a_camposHidden);

                $html = '';
                $html .= '<script>' . $oDatosTabla->getScript() . '</script>';
                $html .= "<h3 class=subtitulo>" . $oInfoClase->getTxtTitulo() . "</h3>"
                    . "<form id='seleccionados' name='seleccionados' action='' method='post'>";
                $html .= $oHashSelect->getCamposHtml();
                $html .= "<input type='hidden' id='mod' name='mod' value=''>";

                $oTabla = new Lista();
                $oTabla->setId_tabla('datos_sql' . $id_dossier);
                $oTabla->setCabeceras($oDatosTabla->getCabeceras());
                $oTabla->setBotones($oDatosTabla->getBotones());
                $oTabla->setDatos($oDatosTabla->getValores());

                if (!empty($oDatosTabla->getValores())) {
                    $html .= $oTabla->mostrar_tabla();
                }
                if ((int)$Qpermiso === 3) {
                    $html .= "<br><table class=botones><tr class=botones>\n"
                        . "\t\t\t\t\t<td class=botones><input name=\"btn_new\" type=\"button\" value=\"";
                    $html .= _("nuevo");
                    if ((int)$id_dossier === 1004) {
                        $urlSpecs[self::PH_INS_TRASLADO] = [
                            'path' => 'frontend/personas/controller/traslado_form.php',
                            'query' => [
                                'cabecera' => 'no',
                                'id_pau' => $id_pau,
                                'id_dossier' => $id_dossier,
                                'obj_pau' => $Qobj_pau,
                            ],
                        ];
                        $html .= '" onclick="fnjs_update_div(\'#main\',\'' . self::PH_INS_TRASLADO . '\');"></td></tr></table>';
                    } else {
                        $html .= "\" onclick=\"fnjs_nuevo('#seleccionados');\"></td></tr></table>";
                    }
                }
                $cuerpo .= $html;
            }
            $cuerpo .= "</div>";
            $id_dossier = strtok("y");
        }

        return [
            'top_html' => $top_html,
            'web_icons' => $web_icons,
            'modo' => 'ficha',
            'cuerpo_html' => $cuerpo,
            'url_specs' => $urlSpecs,
        ];
    }
}
