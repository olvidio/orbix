<?php

namespace src\asistentes\application;

use frontend\shared\web\Desplegable;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\shared\config\ConfigGlobal;
use frontend\shared\security\HashFront;
use function src\shared\domain\helpers\is_true;

/**
 * Dossier actividades de una persona (1301).
 */
final class FormActividadesDeUnaPersonaData
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        $Qid_nom = (int)($input['id_pau'] ?? 0);
        $obj_pau = (string)($input['obj_pau'] ?? '');
        $id_tipo_post = (string)($input['id_tipo'] ?? '');
        $que_dl = (string)($input['que_dl'] ?? '');

        $a_sel = (array)($input['sel'] ?? []);
        $id_activ = '';
        if (!empty($a_sel)) {
            $id_activ = (int)strtok($a_sel[0], '#');
        }

        $despl_actividades_html = '';
        $desplegable_plaza_html = '';
        $desplegable_propietarios_html = '';
        $h1 = '';
        $url_ajax = '';
        $nom_activ = '';

        if (!empty($id_activ)) {
            $mod = 'editar';
            $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
            $oActividad = $ActividadAllRepository->findById($id_activ);
            $nom_activ = $oActividad->getNom_activ();

            $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
            $AsistenteRepositoryInterface = $AsistenteActividadService->getRepoAsistente($Qid_nom, $id_activ);
            $AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
            $oAsistente = $AsistenteRepository->findById($id_activ, $Qid_nom);
            $obj = get_class($oAsistente);

            $id_activ_real = $id_activ;
            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();
            $plaza = $oAsistente->getPlaza();
            $propietario = $oAsistente->getPropietario();

            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                if (!empty($propietario)) {
                    strtok($propietario, '>');
                    $child = (string)strtok('>');
                    if ($obj_pau !== 'PersonaEx' && $child !== ConfigGlobal::mi_delef()) {
                        exit(sprintf(_('los datos de asistencia los modifica el propietario de la plaza: %s'), $child));
                    }
                }
            }
        } else {
            $mod = 'nuevo';
            $id_activ_real = '';
            if (empty($id_tipo_post)) {
                $mi_sfsv = ConfigGlobal::mi_sfsv();
                $id_tipo = '^' . $mi_sfsv;
            } else {
                $id_tipo = '^' . $id_tipo_post;
            }

            $condicion = 'AND status = ' . StatusId::ACTUAL;
            if (!empty($que_dl)) {
                $condicion .= " AND dl_org = '$que_dl'";
            } else {
                $condicion .= " AND dl_org != '" . ConfigGlobal::mi_delef() . "'";
            }

            $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
            $oOpciones = $ActividadRepository->getArrayActividadesDeTipo($id_tipo, $condicion);
            $oDesplActividades = new Desplegable();
            $oDesplActividades->setOpciones($oOpciones);
            $oDesplActividades->setNombre('id_activ');

            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $oDesplActividades->setAction('fnjs_cmb_propietario()');
            }
            $despl_actividades_html = $oDesplActividades->desplegable();

            $propio = 't';
            $falta = 'f';
            $est_ok = 'f';
            $observ = '';
            $plaza = PlazaId::PEDIDA;
            $propietario = '';
            $obj = 'AsistenteDl';
        }
        $propio_chk = (!empty($propio) && is_true($propio)) ? 'checked' : '';
        $falta_chk = (!empty($falta) && is_true($falta)) ? 'checked' : '';
        $est_chk = (!empty($est_ok) && is_true($est_ok)) ? 'checked' : '';

        $plazas_installed = ConfigGlobal::is_app_installed('actividadplazas');
        if ($plazas_installed) {
            $aOpciones = PlazaId::getArrayPosiblesPlazas();
            $oDesplegablePlaza = new Desplegable();
            $oDesplegablePlaza->setNombre('plaza');
            $oDesplegablePlaza->setOpciones($aOpciones);
            $oDesplegablePlaza->setOpcion_sel((string)$plaza);
            $desplegable_plaza_html = $oDesplegablePlaza->desplegable();

            $dl_de_paso = false;
            if ($obj_pau === 'PersonaEx' && !empty($Qid_nom)) {
                $PersonaExRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
                $oPersona = $PersonaExRepository->findById($Qid_nom);
                $dl_de_paso = $oPersona->getDl();
            }
            $gesActividadPlazas = $GLOBALS['container']->get(ResumenPlazasService::class);
            if (!empty($id_activ)) {
                $gesActividadPlazas->setId_activ($id_activ);
                $oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
                $oDesplPosiblesPropietarios->setNombre('propietario');
                $oDesplPosiblesPropietarios->setOpcion_sel($propietario);
                $desplegable_propietarios_html = $oDesplPosiblesPropietarios->desplegable();
            } else {
                $oDesplPosiblesPropietarios = new Desplegable('propietario', [], '');
                $desplegable_propietarios_html = $oDesplPosiblesPropietarios->desplegable();
            }

            $url_ajax = rtrim(ConfigGlobal::getWeb(), '/') . '/src/actividadplazas/posibles_propietarios_data';
            $oHash1 = new HashFront();
            $oHash1->setUrl($url_ajax);
            $oHash1->setCamposForm('id_activ!id_nom');
            $h1 = $oHash1->linkSinValParams();
        }

        $oHash = new HashFront();
        $camposForm = 'observ';
        if ($plazas_installed) {
            $camposForm .= '!plaza!propietario';
        }
        $oHash->setCamposNo('propio!falta!est_ok');
        $a_camposHidden = [
            'pau' => 'p',
            'id_nom' => $Qid_nom,
            'obj_pau' => $obj_pau,
            'mod' => $mod,
        ];
        if (!empty($id_activ_real)) {
            $a_camposHidden['id_activ'] = $id_activ_real;
        } else {
            $camposForm .= '!id_activ';
        }
        $oHash->setCamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_guardar = $web . '/src/asistentes/asistente_guardar';
        $url_self = $web . '/frontend/asistentes/controller/form_actividades_de_una_persona.php';

        return [
            'obj' => $obj,
            'h1' => $h1,
            'url_ajax' => $url_ajax,
            'url_guardar' => $url_guardar,
            'url_self' => $url_self,
            'id_nom' => $Qid_nom,
            'id_activ_real' => $id_activ_real,
            'nom_activ' => $nom_activ,
            'despl_actividades_html' => $despl_actividades_html,
            'propio_chk' => $propio_chk,
            'falta_chk' => $falta_chk,
            'est_chk' => $est_chk,
            'observ' => $observ,
            'plazas_installed' => $plazas_installed,
            'hash_campos_html' => $oHash->getCamposHtml(),
            'desplegable_plaza_html' => $desplegable_plaza_html,
            'desplegable_propietarios_html' => $desplegable_propietarios_html,
        ];
    }
}
