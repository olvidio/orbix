<?php

namespace src\dossiers\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\RegionStgrAviso;
use src\ubis\domain\RegionStgrConfigException;
use src\shared\domain\DatosTablaRepo;
use src\shared\infrastructure\ProvidesRepositories;
use src\personas\domain\entity\Persona;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;

/**
 * Cuerpo de dossiers_ver: datos de cabecera + lista o ficha.
 * El backend NO firma URLs: devuelve `*_link_spec` ({path, query}) que firma el frontend.
 *
 * En modo ficha, `ficha_segmentos` mezcla:
 *  - Segmentos `html` ya generados por los `Select_*` (TODO: refactorizar para que tampoco
 *    lleven HTML/HashFront desde `src/`).
 *  - Segmentos `datos_tabla` con datos puros (`action_tabla_link_spec`, `ins_traslado_link_spec`,
 *    `script_ctx`, `hash`, `tabla`, `permiso`) que el frontend compone con HashFront, Lista y
 *    el script JS de `DatosTablaRepo`.
 *
 * @return array{
 *     error?: string,
 *     top_data: array{web_icons: string, alt_dossiers: string, txt_dossiers: string, nom_cabecera: string, go_dossiers_link_spec: array{path: string, query: array<string, mixed>}, go_home_link_spec?: array{path: string, query: array<string, mixed>}},
 *     modo: 'lista'|'ficha',
 *     lista_a_filas?: list<array<string, mixed>>,
 *     ficha_segmentos?: list<array<string, mixed>>,
 *     aviso?: string
 * }
 */
class DossiersVerPantallaData
{
    public static function build(array $post): array
    {
        try {
            return self::buildInternal($post);
        } catch (RegionStgrConfigException $e) {
            return self::respuestaSoloAvisoRegionStgr($e, $post);
        }
    }

    private static function buildInternal(array $post): array
    {
        $problemasRegionStgr = [];
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
            if ($stack !== 0) {
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
        // `personas_select` y otras vistas legacy envían `que`; otras pantallas usan `queSel`.
        $QqueSel = (string)($post['queSel'] ?? $post['que'] ?? '');
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

        $goDossiersLinkSpec = [
            'path' => 'frontend/dossiers/controller/dossiers_ver.php',
            'query' => ['pau' => $pau, 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau],
        ];

        $goHomeLinkSpec = null;
        switch ($pau) {
            case 'p':
                if (empty($Qobj_pau) || $Qobj_pau === 'Persona') {
                    $oPersona = Persona::findPersonaEnGlobal($id_pau, $problemasRegionStgr);
                    if (!is_object($oPersona)) {
                        return [
                            'error' => "<br>No encuentro a nadie con id_nom: $id_pau en  " . __FILE__ . ': line (Persona lookup)',
                            'ficha_segmentos' => [],
                        ];
                    }
                    $clase = get_class($oPersona);
                    $Qobj_pau = implode('', array_slice(explode('\\', $clase), -1));
                } else {
                    $repo = $getRepository($Qobj_pau);
                    $oPersona = $repo->findById($id_pau);
                }
                $nom_cabecera = $oPersona->getNombreApellidos();
                $goHomeLinkSpec = [
                    'path' => 'frontend/personas/controller/home_persona.php',
                    'query' => ['id_nom' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            case 'u':
                $repo = $getRepository($Qobj_pau);
                $oUbi = $repo->findById($id_pau);
                $nom_cabecera = $oUbi->getNombre_ubi();
                $goHomeLinkSpec = [
                    'path' => 'frontend/ubis/controller/home_ubis.php',
                    'query' => ['id_ubi' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            case 'a':
                $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
                $oActividad = $ActividadAllRepository->findById($id_pau);
                $nom_cabecera = $oActividad->getNom_activ();
                $goHomeLinkSpec = [
                    'path' => 'frontend/actividades/controller/actividad_ver.php',
                    'query' => ['id_activ' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            default:
                return ['error' => 'pau desconocido', 'ficha_segmentos' => []];
        }

        $top_data = [
            'web_icons' => ConfigGlobal::getWeb_icons(),
            'alt_dossiers' => _("ver dossiers"),
            'txt_dossiers' => _("dossiers"),
            'nom_cabecera' => $nom_cabecera,
            'go_dossiers_link_spec' => $goDossiersLinkSpec,
            'go_home_link_spec' => $goHomeLinkSpec,
        ];

        if (empty($Qid_dossier)) {
            $lista = DossiersListaFichasData::build($pau, $id_pau, $Qobj_pau);
            return self::withAvisoRegionStgr([
                'top_data' => $top_data,
                'modo' => 'lista',
                'lista_a_filas' => $lista['a_filas'],
                'ficha_segmentos' => [],
            ], $problemasRegionStgr);
        }

        $fichaSegmentos = [];
        $id_dossier = strtok($Qid_dossier, "y");
        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        while ($id_dossier) {
            $nom_bloque = 'ficha' . $id_dossier;
            $bloque = '#ficha' . $id_dossier;
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

                if (isset($post['stack']) && (string)$stack !== 0) {
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
                        if (method_exists($claseSelect, 'setTodos')) {
                            $claseSelect->setTodos($post['todos'] ?? null);
                        }
                        break;
                }
                if (method_exists($claseSelect, 'getSegmentData')) {
                    $segmentPayload = $claseSelect->getSegmentData();
                    $payload = is_array($segmentPayload) ? $segmentPayload : [];
                    $segmentTipo = (string)($payload['segment_tipo'] ?? 'select_habitaciones_cdc');
                    unset($payload['segment_tipo']);
                    $fichaSegmentos[] = array_merge(
                        [
                            'tipo' => $segmentTipo,
                            'id' => $nom_bloque,
                        ],
                        $payload
                    );
                } else {
                    $fichaSegmentos[] = [
                        'tipo' => 'html',
                        'id' => $nom_bloque,
                        'html' => $claseSelect->getHtml(),
                    ];
                }
            } else {
                $clase_info = DossierVerDatosTablaInfoClassResolver::resolveFullyQualifiedClassName($oTipoDossier);
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

                $actionTablaLinkSpec = [
                    'path' => 'frontend/dossiers/controller/dossiers_ver.php',
                    'query' => [
                        'clase_info' => $Qclase_info_enc,
                        'id_pau' => $id_pau,
                        'bloque' => $bloque,
                        'permiso' => $Qpermiso,
                        'obj_pau' => $Qobj_pau,
                    ],
                ];

                $a_camposHidden = [
                    'clase_info' => $Qclase_info_enc,
                    'pau' => $pau,
                    'id_pau' => $id_pau,
                    'obj_pau' => $Qobj_pau,
                    'permiso' => $Qpermiso,
                    'bloque' => $bloque,
                ];

                $insTrasladoLinkSpec = null;
                if ((int)$id_dossier === 1004) {
                    $insTrasladoLinkSpec = [
                        'path' => 'frontend/personas/controller/traslado_form.php',
                        'query' => [
                            'cabecera' => 'no',
                            'id_pau' => $id_pau,
                            'id_dossier' => $id_dossier,
                            'obj_pau' => $Qobj_pau,
                        ],
                    ];
                }

                $fichaSegmentos[] = [
                    'tipo' => 'datos_tabla',
                    'id' => $nom_bloque,
                    'titulo' => $oInfoClase->getTxtTitulo(),
                    // Datos puros para que el frontend genere el <script> con HashFront::link
                    // aplicado a `action_tabla_link_spec`.
                    'script_ctx' => [
                        'bloque' => $bloque,
                        'action_form' => $oDatosTabla->getAction_form(),
                        'action_update' => $oDatosTabla->getAction_update(),
                        'eliminar_txt' => $oInfoClase->getTxtEliminar(),
                    ],
                    'action_tabla_link_spec' => $actionTablaLinkSpec,
                    'hash' => [
                        'campos_form' => 'mod',
                        'campos_no' => 'sel!mod!scroll_id!refresh',
                        'campos_hidden' => $a_camposHidden,
                    ],
                    'tabla' => [
                        'id_tabla' => 'datos_sql' . $id_dossier,
                        'cabeceras' => $oDatosTabla->getCabeceras(),
                        'botones' => $oDatosTabla->getBotones(),
                        'valores' => $oDatosTabla->getValores(),
                    ],
                    'permiso' => (int) $Qpermiso,
                    'ins_traslado_link_spec' => $insTrasladoLinkSpec,
                ];
            }
            $id_dossier = strtok("y");
        }

        return self::withAvisoRegionStgr([
            'top_data' => $top_data,
            'modo' => 'ficha',
            'ficha_segmentos' => $fichaSegmentos,
        ], $problemasRegionStgr);
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, array<string, string>> $problemasRegionStgr
     * @return array<string, mixed>
     */
    private static function withAvisoRegionStgr(array $result, array $problemasRegionStgr): array
    {
        $aviso = RegionStgrAviso::formatear($problemasRegionStgr);
        if ($aviso !== '') {
            $result['aviso'] = $aviso;
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private static function respuestaSoloAvisoRegionStgr(RegionStgrConfigException $e, array $post): array
    {
        $pau = (string)($post['pau'] ?? '');
        $id_pau = (int)($post['id_pau'] ?? 0);
        $Qobj_pau = (string)($post['obj_pau'] ?? '');

        $problemasRegionStgr = [];
        RegionStgrAviso::registrar($problemasRegionStgr, $e);

        return [
            'aviso' => RegionStgrAviso::formatear($problemasRegionStgr),
            'top_data' => [
                'web_icons' => ConfigGlobal::getWeb_icons(),
                'alt_dossiers' => _('ver dossiers'),
                'txt_dossiers' => _('dossiers'),
                'nom_cabecera' => '',
                'go_dossiers_link_spec' => [
                    'path' => 'frontend/dossiers/controller/dossiers_ver.php',
                    'query' => ['pau' => $pau, 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau],
                ],
            ],
            'modo' => 'lista',
            'lista_a_filas' => [],
            'ficha_segmentos' => [],
        ];
    }
}
