<?php

namespace src\asistentes\application;

use Psr\Container\ContainerInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\is_true;

/**
 * Dossier actividades de una persona (1301). Datos puros para el formulario;
 * la UI (HashFront, Desplegable) se compone en frontend.
 */
final class FormActividadesDeUnaPersonaData
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private AsistenteActividadService $asistenteActividadService,
        private ActividadRepositoryInterface $actividadRepository,
        private PersonaExRepositoryInterface $personaExRepository,
        private ResumenPlazasService $resumenPlazasService,
        private ContainerInterface $container,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function build(array $input): array
    {
        $Qid_nom = (int)($input['id_pau'] ?? 0);
        $obj_pau = (string)($input['obj_pau'] ?? '');
        $id_tipo_post = (string)($input['id_tipo'] ?? '');
        $que_dl = (string)($input['que_dl'] ?? '');

        $a_sel = (array)($input['sel'] ?? []);
        $id_activ_edit = 0;
        if (!empty($a_sel)) {
            $id_activ_edit = (int)strtok((string)$a_sel[0], '#');
        }

        if ($id_activ_edit !== 0) {
            $mod = 'editar';
            $oActividad = $this->actividadAllRepository->findById($id_activ_edit);
            $nom_activ = $oActividad->getNom_activ();

            $AsistenteRepositoryInterface = $this->asistenteActividadService->getRepoAsistente($Qid_nom, $id_activ_edit);
            $AsistenteRepository = $this->container->get($AsistenteRepositoryInterface);
            $oAsistente = $AsistenteRepository->findById($id_activ_edit, $Qid_nom);
            $obj = get_class($oAsistente);

            $id_activ_real = $id_activ_edit;
            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();
            $plaza = $oAsistente->getPlaza();
            $propietario = $oAsistente->getPropietario();

            if (ConfigGlobal::is_app_installed('actividadplazas') && !empty($propietario)) {
                strtok($propietario, '>');
                $child = (string)strtok('>');
                if ($obj_pau !== 'PersonaEx' && $child !== ConfigGlobal::mi_delef()) {
                    return [
                        'error' => sprintf(
                            _('los datos de asistencia los modifica el propietario de la plaza: %s'),
                            $child
                        ),
                    ];
                }
            }

            $actividades_opciones = null;
            $actividades_onchange = null;
        } else {
            $mod = 'nuevo';
            $id_activ_real = '';
            if ($id_tipo_post === '') {
                $id_tipo = '^' . ConfigGlobal::mi_sfsv();
            } else {
                $id_tipo = '^' . $id_tipo_post;
            }

            $condicion = 'AND status = ' . StatusId::ACTUAL;
            if ($que_dl !== '') {
                $que_dl_esc = str_replace("'", "''", $que_dl);
                $condicion .= " AND dl_org = '$que_dl_esc'";
            } else {
                $condicion .= " AND dl_org != '" . ConfigGlobal::mi_delef() . "'";
            }

            $actividades_opciones = $this->actividadRepository->getArrayActividadesDeTipo($id_tipo, $condicion);
            $actividades_onchange = ConfigGlobal::is_app_installed('actividadplazas') ? 'fnjs_cmb_propietario()' : null;

            $propio = 't';
            $falta = 'f';
            $est_ok = 'f';
            $observ = '';
            $plaza = PlazaId::PEDIDA;
            $propietario = '';
            $obj = 'AsistenteDl';
            $nom_activ = '';
        }

        $propio_chk = (!empty($propio) && is_true($propio)) ? 'checked' : '';
        $falta_chk = (!empty($falta) && is_true($falta)) ? 'checked' : '';
        $est_chk = (!empty($est_ok) && is_true($est_ok)) ? 'checked' : '';

        $plazas_installed = ConfigGlobal::is_app_installed('actividadplazas');
        $plaza_opciones = [];
        $propietario_opciones = [];
        $propietario_select_blanco = false;

        if ($plazas_installed) {
            $plaza_opciones = PlazaId::getArrayPosiblesPlazas();

            $dl_de_paso = false;
            if ($obj_pau === 'PersonaEx' && $Qid_nom !== 0) {
                $oPersona = $this->personaExRepository->findById($Qid_nom);
                $dl_de_paso = $oPersona->getDl();
            }
            if ($id_activ_edit !== 0) {
                $this->resumenPlazasService->setId_activ($id_activ_edit);
                $propietario_opciones = $this->resumenPlazasService->getPosiblesPropietariosOpciones($dl_de_paso);
                $propietario_select_blanco = true;
            } else {
                $propietario_opciones = [];
                $propietario_select_blanco = false;
            }
        }

        $camposForm = 'observ';
        if ($plazas_installed) {
            $camposForm .= '!plaza!propietario';
        }
        $a_camposHidden = [
            'pau' => 'p',
            'id_nom' => $Qid_nom,
            'obj_pau' => $obj_pau,
            'mod' => $mod,
        ];
        if ($id_activ_real !== '' && $id_activ_real !== 0) {
            $a_camposHidden['id_activ'] = $id_activ_real;
        } else {
            $camposForm .= '!id_activ';
        }

        $out = [
            'obj' => $obj,
            'id_nom' => $Qid_nom,
            'id_activ_real' => $id_activ_real,
            'nom_activ' => $nom_activ,
            'propio_chk' => $propio_chk,
            'falta_chk' => $falta_chk,
            'est_chk' => $est_chk,
            'observ' => $observ,
            'plazas_installed' => $plazas_installed,
            'hash_main' => [
                'campos_form' => $camposForm,
                'campos_no' => 'propio!falta!est_ok',
                'campos_hidden' => $a_camposHidden,
            ],
            'paths' => [
                'asistente_guardar' => 'src/asistentes/asistente_guardar',
                'form_self' => 'frontend/asistentes/controller/form_actividades_de_una_persona.php',
                'posibles_propietarios_data' => 'src/actividadplazas/posibles_propietarios_data',
            ],
            'plaza_opciones' => $plaza_opciones,
            'plaza_selected' => (string)$plaza,
            'propietario_opciones' => $propietario_opciones,
            'propietario_selected' => (string)$propietario,
            'propietario_select_blanco' => $propietario_select_blanco,
        ];

        if ($actividades_opciones !== null) {
            $out['actividades_opciones'] = $actividades_opciones;
            $out['actividades_onchange'] = $actividades_onchange;
        }

        if ($plazas_installed) {
            $out['ajax_propietarios'] = [
                'path' => 'src/actividadplazas/posibles_propietarios_data',
                'campos_form' => 'id_activ!id_nom',
            ];
        }

        return $out;
    }
}
