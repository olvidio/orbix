<?php

namespace src\asistentes\application;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\Hash;

/**
 * Tabla de peticiones de plaza por actividad (`tabla_peticiones.php`).
 */
final class TablaPeticionesData
{
    /**
     * @param array<string, mixed> $input
     * @return array{nom_activ: string, tabla_html: string, url_guardar_ajax: string, url_guardar_ajax_json: string}
     */
    public static function build(array $input): array
    {
        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $id_activ_old = (int)strtok($a_sel[0], '#');
            $nom_activ = (string)strtok('#');
        } else {
            $id_activ_old = (int)($input['id_activ_old'] ?? 0);
            $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
            $oActividad = $ActividadAllRepository->findById($id_activ_old);
            $nom_activ = $oActividad->getNom_activ();
        }

        $Qid_sel = null;
        $Qscroll_id = null;
        if (isset($input['stack'])) {
            $stack = filter_var($input['stack'], FILTER_SANITIZE_NUMBER_INT);
            if ($stack !== '' && $stack !== false) {
                $oPosicion2 = new Posicion();
                if ($oPosicion2->goStack((int)$stack)) {
                    $Qid_sel = $oPosicion2->getParametro('id_sel');
                    $Qscroll_id = $oPosicion2->getParametro('scroll_id');
                    $oPosicion2->olvidar((int)$stack);
                }
            }
        }

        $a_cabeceras = [_('nombre'),
            _('peticiones (libres/concedidas)'),
        ];

        $a_botones = [];

        $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $cAsistentes = $AsistenteActividadService->getAsistentesDeActividad($id_activ_old);

        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($id_activ_old);
        $id_tipo_activ = $oActividad->getId_tipo_activ();

        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $sactividad = $oTipoActividad->getActividadText();

        $mi_dele = ConfigGlobal::mi_delef();
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $mi_dele]);
        $oDelegacion = $cDelegaciones[0];
        $id_dl = $oDelegacion->getIdDlVo()->value();

        $a_valores = [];
        $i = 0;
        $PlazaPeticionRepository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
        $ActividadPlazasRepository = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        foreach ($cAsistentes as $oAsistente) {
            $i++;
            $id_nom = $oAsistente->getId_nom();
            $aWhere = ['id_nom' => $id_nom, 'tipo' => $sactividad, '_ordre' => 'orden'];
            $aOperador = ['tipo' => '~'];
            $cPlazasPeticion = $PlazaPeticionRepository->getPlazasPeticion($aWhere, $aOperador);
            $posibles_activ = '';
            foreach ($cPlazasPeticion as $oPlazaPeticion) {
                $id_activ = $oPlazaPeticion->getId_activ();
                $nom_activ_i = '';
                if (!empty($id_activ)) {
                    $oActividadPosible = $ActividadAllRepository->findById($id_activ);
                    $nom_activ_i = $oActividadPosible->getNom_activ();
                    $dl_org = $oActividad->getDl_org();

                    $txt_plazas = '';
                    if (ConfigGlobal::is_app_installed('actividadplazas')) {
                        $concedidas = 0;
                        $cActividadPlazas = $ActividadPlazasRepository->getActividadesPlazas(['id_dl' => $id_dl, 'id_activ' => $id_activ]);
                        foreach ($cActividadPlazas as $oActividadPlazas) {
                            $dl_tabla = $oActividadPlazas->getDl_tabla();
                            if ($dl_org === $dl_tabla) {
                                $concedidas = $oActividadPlazas->getPlazas();
                            }
                        }
                        $ocupadas = $AsistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dele);
                        if ($ocupadas < 0) {
                            $libres = '-';
                        } else {
                            $libres = $concedidas - $ocupadas;
                        }
                        if (!empty($concedidas)) {
                            $txt_plazas = " ($libres/$concedidas)";
                        }
                        $nom_activ_i .= $txt_plazas;
                    }
                    if ($id_activ !== $id_activ_old) {
                        $aCamposHidden = ['mod' => 'mover',
                            'id_nom' => $id_nom,
                            'id_activ_old' => $id_activ_old,
                            'id_activ' => $id_activ,
                            'plaza' => PlazaId::ASIGNADA,
                        ];

                        $oHash = new Hash();
                        $oHash->setUrl(AppUrlConfig::getApiBaseUrl() . '/src/asistentes/asistente_guardar');
                        $oHash->setArrayCamposHidden($aCamposHidden);
                        $param_mover = $oHash->getParamAjax();

                        $nom_activ_i = '<span class="link" onClick="fnjs_cambiar_actividad(\'' . $param_mover . '\')">' . $nom_activ_i . '</span>';
                    }

                    $posibles_activ .= empty($posibles_activ) ? '' : ', ';
                    $posibles_activ .= $nom_activ_i;
                }
            }
            $oPersona = $PersonaDlRepository->findById($id_nom);
            $nom_ap = $oPersona?->getApellidosNombre();

            $a_valores[$i][1] = $nom_ap;
            $a_valores[$i][2] = $posibles_activ;
        }

        if (!empty($a_valores)) {
            if (isset($Qid_sel) && !empty($Qid_sel)) {
                $a_valores['select'] = $Qid_sel;
            }
            if (isset($Qscroll_id) && !empty($Qscroll_id)) {
                $a_valores['scroll_id'] = $Qscroll_id;
            }
        }

        $oTabla = new Lista();
        $oTabla->setId_tabla('tabla_peticiones');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setBotones($a_botones);
        $oTabla->setDatos($a_valores);

        $url_guardar_ajax = AppUrlConfig::getApiBaseUrl() . '/src/asistentes/asistente_guardar';

        return [
            'nom_activ' => $nom_activ,
            'tabla_html' => $oTabla->mostrar_tabla_html(),
            'url_guardar_ajax' => $url_guardar_ajax,
            'url_guardar_ajax_json' => json_encode($url_guardar_ajax, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
        ];
    }
}
