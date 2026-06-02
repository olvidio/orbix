<?php

namespace src\asistentes\application;

use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Tabla de peticiones de plaza por actividad (`tabla_peticiones.php`).
 * HTML de tabla, enlaces firmados y metadatos AJAX: {@see \frontend\asistentes\helpers\TablaPeticionesRender}.
 */
final class TablaPeticionesData
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private AsistenteActividadService $asistenteActividadService,
        private DelegacionRepositoryInterface $delegacionRepository,
        private PlazaPeticionRepositoryInterface $plazaPeticionRepository,
        private ActividadPlazasRepositoryInterface $actividadPlazasRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
    ) {
    }

    /**
     * Cada fila tiene `[2]` como string vacío, o
     * `['peticiones_parts' => list<array{t: 'p', s: string}|array{t: 'm', s: string, h: array}>]`.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function build(array $input): array
    {
        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $id_activ_old = (int)strtok((string)$a_sel[0], '#');
            $nom_activ = (string)strtok('#');
        } else {
            $id_activ_old = (int)($input['id_activ_old'] ?? 0);
            $oActividad = $this->actividadAllRepository->findById($id_activ_old);
            $nom_activ = $oActividad->getNom_activ();
        }

        $Qid_sel = null;
        $Qscroll_id = null;
        if (isset($input['stack'])) {
            $stack = filter_var($input['stack'], FILTER_SANITIZE_NUMBER_INT);
            if ($stack !== 0) {
                if (array_key_exists('restored_id_sel', $input)) {
                    $Qid_sel = $input['restored_id_sel'];
                }
                if (array_key_exists('restored_scroll_id', $input)) {
                    $Qscroll_id = $input['restored_scroll_id'];
                }
            }
        }

        $a_cabeceras = [_('nombre'),
            _('peticiones (libres/concedidas)'),
        ];

        $a_botones = [];

        $cAsistentes = $this->asistenteActividadService->getAsistentesDeActividad($id_activ_old);

        $oActividad = $this->actividadAllRepository->findById($id_activ_old);
        $id_tipo_activ = $oActividad->getId_tipo_activ();

        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $sactividad = $oTipoActividad->getActividadText();

        $mi_dele = ConfigGlobal::mi_delef();
        $cDelegaciones = $this->delegacionRepository->getDelegaciones(['dl' => $mi_dele]);
        $oDelegacion = $cDelegaciones[0];
        $id_dl = $oDelegacion->getIdDlVo()->value();

        $a_valores = [];
        $i = 0;
        foreach ($cAsistentes as $oAsistente) {
            $i++;
            $id_nom = $oAsistente->getId_nom();
            $aWhere = ['id_nom' => $id_nom, 'tipo' => $sactividad, '_ordre' => 'orden'];
            $aOperador = ['tipo' => '~'];
            $cPlazasPeticion = $this->plazaPeticionRepository->getPlazasPeticion($aWhere, $aOperador);
            $parts = [];
            foreach ($cPlazasPeticion as $oPlazaPeticion) {
                $id_activ = $oPlazaPeticion->getId_activ();
                if (empty($id_activ)) {
                    continue;
                }
                $oActividadPosible = $this->actividadAllRepository->findById($id_activ);
                $nom_activ_i = $oActividadPosible->getNom_activ();
                $dl_org = $oActividad->getDl_org();

                if (ConfigGlobal::is_app_installed('actividadplazas')) {
                    $concedidas = 0;
                    $cActividadPlazas = $this->actividadPlazasRepository->getActividadesPlazas(['id_dl' => $id_dl, 'id_activ' => $id_activ]);
                    foreach ($cActividadPlazas as $oActividadPlazas) {
                        $dl_tabla = $oActividadPlazas->getDl_tabla();
                        if ($dl_org === $dl_tabla) {
                            $concedidas = $oActividadPlazas->getPlazas();
                        }
                    }
                    $ocupadas = $this->asistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dele);
                    if ($ocupadas < 0) {
                        $libres = '-';
                    } else {
                        $libres = $concedidas - $ocupadas;
                    }
                    if (!empty($concedidas)) {
                        $nom_activ_i .= " ($libres/$concedidas)";
                    }
                }

                if ($parts !== []) {
                    $parts[] = ['t' => 'p', 's' => ', '];
                }
                if ($id_activ !== $id_activ_old) {
                    $parts[] = [
                        't' => 'm',
                        's' => $nom_activ_i,
                        'h' => [
                            'mod' => 'mover',
                            'id_nom' => $id_nom,
                            'id_activ_old' => $id_activ_old,
                            'id_activ' => $id_activ,
                            'plaza' => PlazaId::ASIGNADA,
                        ],
                    ];
                } else {
                    $parts[] = ['t' => 'p', 's' => $nom_activ_i];
                }
            }

            $oPersona = $this->personaDlRepository->findById($id_nom);
            $nom_ap = $oPersona?->getApellidosNombre();

            $a_valores[$i][1] = $nom_ap;
            $a_valores[$i][2] = $parts === [] ? '' : ['peticiones_parts' => $parts];
        }

        if ($a_valores !== []) {
            if (isset($Qid_sel) && !empty($Qid_sel)) {
                $a_valores['select'] = $Qid_sel;
            }
            if (isset($Qscroll_id) && !empty($Qscroll_id)) {
                $a_valores['scroll_id'] = $Qscroll_id;
            }
        }

        return [
            'nom_activ' => $nom_activ,
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
            'paths' => [
                'asistente_guardar' => 'src/asistentes/asistente_guardar',
            ],
        ];
    }
}
