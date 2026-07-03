<?php

namespace src\dossiers\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\dossiers\application\support\DossierFichaSelectRunner;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\shared\domain\DatosInfoRepo;
use src\shared\domain\DatosTablaRepo;
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\RegionStgrAviso;
use src\ubis\domain\RegionStgrConfigException;

/**
 * Cuerpo de dossiers_ver: datos de cabecera + lista o ficha.
 */
class DossiersVerPantallaData
{
    public function __construct(
        private TipoDossierRepositoryInterface $tipoDossierRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private UbiRepositoryResolver $ubiRepositoryResolver,
        private DossiersListaFichasData $dossiersListaFichasData,
        private DossierTipoFileSuffixResolver $suffixResolver,
        private DossierFichaSelectRunner $fichaSelectRunner,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public function build(array $post): array
    {
        try {
            return $this->buildInternal($post);
        } catch (RegionStgrConfigException $e) {
            return $this->respuestaSoloAvisoRegionStgr($e, $post);
        }
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function buildInternal(array $post): array
    {
        $problemasRegionStgr = [];
        $Qrefresh = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'refresh');
        $a_sel = isset($post['sel']) && is_array($post['sel']) ? $post['sel'] : [];
        if ($a_sel === []) {
            $a_sel = [];
        }
        $Qmod = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'mod');
        if ($a_sel !== [] && ($Qmod === 'eliminar' || $Qmod === 'nuevo')) {
            $a_sel = [];
        }

        $Qid_sel = '';
        $Qscroll_id = $this->scrollIdFromPost($post);
        if (array_key_exists('restored_id_sel', $post)) {
            $Qid_sel = $this->idSelScalar($post['restored_id_sel']);
        }
        if (array_key_exists('restored_scroll_id', $post)) {
            $restoredScroll = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'restored_scroll_id');
            if ($restoredScroll > 0) {
                $Qscroll_id = $restoredScroll;
            }
        }
        $stack = '';
        if (isset($post['stack']) && \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'stack') !== '') {
            $stack = (string) filter_var(\src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'stack'), FILTER_SANITIZE_NUMBER_INT);
        }
        if ($Qid_sel === '' && $a_sel !== []) {
            $first = $a_sel[0] ?? '';
            $Qid_sel = is_scalar($first) ? (string) $first : '';
        }
        $Qid_sel = $this->idSelFromPost($post, $Qid_sel);

        $Qid_pau = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_pau');
        $pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'pau');
        $Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'obj_pau');
        $Qid_dossier = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'id_dossier');
        $Qpermiso = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'permiso');
        $QqueSel = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'queSel') !== ''
            ? \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'queSel')
            : \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'que');
        $Qclase_info_encoded = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'clase_info');

        if ($Qid_dossier === '' && $Qclase_info_encoded !== '') {
            $obj = urldecode($Qclase_info_encoded);
            if (!class_exists($obj)) {
                return ['error' => 'clase_info invalida', 'ficha_segmentos' => []];
            }
            $oInfoClase = DatosInfoRepoResolver::resolve($obj);
            if (method_exists($oInfoClase, 'setObj_pau')) {
                $oInfoClase->setObj_pau($Qobj_pau);
            }
            if (method_exists($oInfoClase, 'getId_dossier')) {
                $Qid_dossier = (string) $oInfoClase->getId_dossier();
            }
            $pauFromInfo = $oInfoClase->getPau();
            if ($pauFromInfo !== null) {
                $pau = $pauFromInfo;
            }
        }

        if ($Qrefresh > 0) {
            $id_pau = $Qid_pau;
        } elseif ($a_sel !== []) {
            $firstSel = $a_sel[0] ?? '';
            $id_pau = (int) strtok(is_scalar($firstSel) ? (string) $firstSel : '', '#');
        } else {
            $id_pau = $Qid_pau;
        }

        $Qid_activ = 0;
        $Qmodo_curso = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'modo_curso');

        switch ($QqueSel) {
            case 'activ':
                $pau = 'p';
                $Qpermiso = '3';
                break;
            case 'matriculas':
                $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_activ');
                $pau = 'p';
                $Qpermiso = '3';
                if ($Qmod === 'sel_es_asistente' && $a_sel !== []) {
                    $firstSel = $a_sel[0] ?? '';
                    $id_pau = (int) strtok(is_scalar($firstSel) ? (string) $firstSel : '', '#');
                }
                break;
            case 'asis':
                $pau = 'a';
                $Qpermiso = '3';
                $Qid_dossier = '3101';
                break;
            case 'asig':
                $pau = 'a';
                $Qpermiso = '3';
                $Qid_dossier = '3005';
                break;
            case 'carg':
                $pau = 'a';
                $Qpermiso = '3';
                $Qid_dossier = '3102';
                break;
            default:
                break;
        }

        $goDossiersLinkSpec = [
            'path' => 'frontend/dossiers/controller/dossiers_ver.php',
            'query' => ['pau' => $pau, 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau],
        ];

        $goHomeLinkSpec = null;
        $nom_cabecera = '';
        switch ($pau) {
            case 'p':
                if ($Qobj_pau === '' || $Qobj_pau === 'Persona') {
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
                    $repo = $this->personaRepositoryResolver->repositorio($Qobj_pau);
                    $oPersona = $repo->findById($id_pau);
                    if ($oPersona === null) {
                        return [
                            'error' => "<br>No encuentro a nadie con id_nom: $id_pau en  " . __FILE__ . ': line (Persona lookup)',
                            'ficha_segmentos' => [],
                        ];
                    }
                }
                $nom_cabecera = $oPersona->getNombreApellidos();
                $goHomeLinkSpec = [
                    'path' => 'frontend/personas/controller/home_persona.php',
                    'query' => ['id_nom' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            case 'u':
                $repo = $this->ubiRepositoryResolver->getRepository($Qobj_pau);
                $oUbi = $repo->findById($id_pau);
                if ($oUbi === null) {
                    return ['error' => 'ubi no encontrada', 'ficha_segmentos' => []];
                }
                $nom_cabecera = $oUbi->getNombre_ubi();
                $goHomeLinkSpec = [
                    'path' => 'frontend/ubis/controller/home_ubis.php',
                    'query' => ['id_ubi' => $id_pau, 'obj_pau' => $Qobj_pau],
                ];
                break;
            case 'a':
                $oActividad = $this->actividadAllRepository->findById($id_pau);
                if ($oActividad === null) {
                    return ['error' => 'actividad no encontrada', 'ficha_segmentos' => []];
                }
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
            'alt_dossiers' => _('ver dossiers'),
            'txt_dossiers' => _('dossiers'),
            'nom_cabecera' => $nom_cabecera,
            'go_dossiers_link_spec' => $goDossiersLinkSpec,
            'go_home_link_spec' => $goHomeLinkSpec,
        ];

        if ($Qid_dossier === '') {
            $lista = $this->dossiersListaFichasData->build($pau, $id_pau, $Qobj_pau);
            return $this->withAvisoRegionStgr([
                'top_data' => $top_data,
                'modo' => 'lista',
                'lista_a_filas' => $lista['a_filas'],
                'ficha_segmentos' => [],
            ], $problemasRegionStgr);
        }

        $fichaSegmentos = [];
        $id_dossier = strtok($Qid_dossier, 'y');
        while ($id_dossier !== false) {
            $nom_bloque = 'ficha' . $id_dossier;
            $bloque = '#ficha' . $id_dossier;
            $oTipoDossier = $this->tipoDossierRepository->findById((int) $id_dossier);
            if ($oTipoDossier === null) {
                $id_dossier = strtok('y');
                continue;
            }
            $nameClaseSelect = $this->suffixResolver->resolveSelectClassFqcn($oTipoDossier);

            if ($nameClaseSelect !== null && class_exists($nameClaseSelect)) {
                $segment = $this->fichaSelectRunner->buildSelectSegment(
                    $nameClaseSelect,
                    $nom_bloque,
                    $id_dossier,
                    $pau,
                    $Qobj_pau,
                    $id_pau,
                    $Qpermiso,
                    $bloque,
                    $QqueSel,
                    $stack,
                    $Qid_sel,
                    $Qscroll_id,
                    $Qid_activ,
                    $Qmodo_curso,
                    $post,
                );
                if ($segment !== null) {
                    $fichaSegmentos[] = $segment;
                }
            } else {
                $clase_info = DossierVerDatosTablaInfoClassResolver::tryResolveFullyQualifiedClassName($oTipoDossier);
                if ($clase_info === null || !class_exists($clase_info)) {
                    $id_dossier = strtok('y');
                    continue;
                }
                $resolved = $this->fichaSelectRunner->resolveDatosInfo($clase_info, $id_pau, $Qobj_pau);
                $oInfoClase = $resolved['info'];
                $Qclase_info_enc = $resolved['clase_info_encoded'];
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
                if ((int) $id_dossier === 1004) {
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
                    'script_ctx' => [
                        'bloque' => $bloque,
                        'action_form' => $oDatosTabla->getAction_form(),
                        'action_update' => $oDatosTabla->getAction_update(),
                        'eliminar_txt' => $oInfoClase->getTxtEliminar(),
                    ],
                    'action_tabla_link_spec' => $actionTablaLinkSpec,
                    'hash' => [
                        'campos_form' => 'mod',
                        'campos_no' => 'sel!mod!scroll_id!refresh!id_sel',
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
            $id_dossier = strtok('y');
        }

        if ($fichaSegmentos === []) {
            return $this->withAvisoRegionStgr([
                'error' => sprintf(
                    'El dossier %s no está disponible (sin widget ni datos configurados en d_tipos_dossiers).',
                    $Qid_dossier,
                ),
                'top_data' => $top_data,
                'ficha_segmentos' => [],
            ], $problemasRegionStgr);
        }

        return $this->withAvisoRegionStgr([
            'top_data' => $top_data,
            'modo' => 'ficha',
            'ficha_segmentos' => $fichaSegmentos,
        ], $problemasRegionStgr);
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @return array<string, mixed>
     */
    private function withAvisoRegionStgr(array $result, array $problemasRegionStgr): array
    {
        $aviso = RegionStgrAviso::formatear($problemasRegionStgr);
        if ($aviso !== '') {
            $result['aviso'] = $aviso;
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $post
     */
    private function idSelFromPost(array $post, string $current): string
    {
        if ($current !== '') {
            return $current;
        }

        return $this->idSelScalar($post['id_sel'] ?? null);
    }

    private function idSelScalar(mixed $raw): string
    {
        if (is_string($raw) && $raw !== '') {
            return $raw;
        }
        if (is_int($raw) || is_float($raw)) {
            return (string) $raw;
        }
        if (is_array($raw) && $raw !== []) {
            $first = $raw[0] ?? '';

            return is_scalar($first) ? (string) $first : '';
        }

        return '';
    }

    /**
     * @param array<string, mixed> $post
     */
    private function scrollIdFromPost(array $post): int
    {
        $direct = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'scroll_id');
        if ($direct > 0) {
            return $direct;
        }

        foreach ($post as $key => $value) {
            if (!str_starts_with($key, 'scroll_id_')) {
                continue;
            }
            if (!is_scalar($value)) {
                continue;
            }
            $n = (int) $value;
            if ($n > 0) {
                return $n;
            }
        }

        return 0;
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function respuestaSoloAvisoRegionStgr(RegionStgrConfigException $e, array $post): array
    {
        $pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'pau');
        $id_pau = \src\shared\domain\helpers\FuncTablasSupport::inputInt($post, 'id_pau');
        $Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($post, 'obj_pau');

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
