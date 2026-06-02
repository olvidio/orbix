<?php

namespace src\asistentes\application;

use Psr\Container\ContainerInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\dossiers\application\PermDossier;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\Ubi;
use src\actividades\domain\entity\TiposActividades;
use function src\shared\domain\helpers\is_true;

/**
 * Widget del dossier `3101` (codigo `asistentes_a_una_actividad`):
 * tabla con los asistentes de una actividad (cargos primero, resto despues).
 *
 * El HTML lo renderiza {@see \frontend\asistentes\helpers\SelectAsistentesAUnaActividadRender}
 * desde {@see self::getSegmentData()} (sin `frontend\` en `src/`).
 *
 * Sucesor de `apps/asistentes/model/Select3101.php`. Instanciado dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 */
class Select_asistentes_a_una_actividad
{
    private const ID_TIPO_DOSSIER = 3101;

    private function getContainer(): ContainerInterface
    {
        /** @var ContainerInterface $container */
        $container = $GLOBALS['container'];

        return $container;
    }

    private $a_ref_perm;
    private $msg_err;
    private $a_valores;
    private $a_asistentes;
    private $mi_dele;
    private $plazas_txt;
    private $plazas_totales;
    private $id_tipo_activ;
    private $dl_org;
    private $id_ubi;
    private $num;
    private $aListaCargos;
    private $publicado;
    private $leyenda_html;
    private $resumen_plazas;
    private $resumen_plazas2;
    private $aLinks_dl;
    private $a_plazas_resumen;
    private $a_plazas_conseguidas;

    private $txt_eliminar;
    private $bloque;

    private string $queSel = '';
    private $id_dossier;
    private $pau;
    private $obj_pau;
    private $id_pau;
    private $permiso;

    private $Qid_sel;
    private $Qscroll_id;
    private mixed $status;

    private function incrementa(&$var): void
    {
        if (empty($var)) {
            $var = 1;
        } else {
            $var++;
        }
    }

    private function getBotones(): array
    {
        $a_botones = [];
        if (ConfigGlobal::is_app_installed('asistentes') && ConfigGlobal::mi_ambito() !== 'rstgr') {
            $a_botones[] = ['txt' => _("modificar asistencia"), 'click' => "fnjs_modificar(this.form)"];
            $a_botones[] = ['txt' => _("cambiar actividad"), 'click' => "fnjs_mover(this.form,$this->id_pau)"];
            $a_botones[] = ['txt' => _("borrar asistencia"), 'click' => "fnjs_borrar(this.form)"];
            $a_botones[] = ['txt' => _("transferir a históricos"), 'click' => "fnjs_transferir(this.form)"];
        }
        if (ConfigGlobal::is_app_installed('actividadcargos') && ConfigGlobal::mi_ambito() !== 'rstgr') {
            $a_botones[] = ['txt' => _("añadir cargo"), 'click' => "fnjs_add_cargo(this.form)"];
            $a_botones[] = ['txt' => _("modificar cargo"), 'click' => "fnjs_mod_cargo(this.form)"];
            $a_botones[] = ['txt' => _("quitar cargo"), 'click' => "fnjs_borrar_cargo(this.form)"];
        }
        if (ConfigGlobal::is_app_installed('actividadestudios')) {
            $a_botones[] = ['txt' => _("plan estudios"), 'click' => "fnjs_matriculas(this.form,\"#frm_matriculas\")"];
            $a_botones[] = ['txt' => _("E43"), 'click' => "fnjs_e43(this.form)"];
        }
        return $a_botones;
    }

    private function getCabeceras(): array
    {
        return [
            ['name' => _("num"), 'width' => 40],
            ['name' => _("nombre y apellidos"), 'width' => 300],
            ['name' => _("dl"), 'width' => 4],
            ['name' => _("propio"), 'width' => 40],
            ['name' => _("est. ok"), 'width' => 40],
            ['name' => _("falta"), 'width' => 40],
            ['name' => _("observaciones actividad"), 'width' => 150],
            ['name' => _("sacd."), 'width' => 10],
            ['name' => _("telf."), 'width' => 80],
            ['name' => _("mails"), 'width' => 100],
            ['name' => _("nombre"), 'width' => 30],
            ['name' => _("apellidos"), 'width' => 30],
            ['name' => _("ctr"), 'width' => 30],
        ];
    }

    private function getDatosActividad(): void
    {
        /** @var ActividadAllRepositoryInterface $ActividadAllRepository */
        $ActividadAllRepository = $this->getContainer()->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($this->id_pau);
        if ($oActividad === null) {
            $this->msg_err = sprintf(_('No se ha encontrado la actividad con id: %s'), $this->id_pau);

            return;
        }
        $this->id_tipo_activ = $oActividad->getId_tipo_activ();
        $this->dl_org = $oActividad->getDl_org();
        $this->plazas_totales = $oActividad->getPlazas();
        $this->id_ubi = $oActividad->getId_ubi();
        $this->publicado = $oActividad->isPublicado();
    }

    private function getTituloPlazas(): void
    {
        if (empty($this->plazas_totales)) {
            $oUbi = Ubi::NewUbi($this->id_ubi);
            if ($oUbi instanceof Casa) {
                $plazas_max = $oUbi->getPlazas();
                $plazas_min = $oUbi->getPlazas_min();
            } elseif ($oUbi instanceof CentroDl) {
                $plazas_max = $oUbi->getPlazas();
                $plazas_min = '';
            } else {
                $plazas_max = '';
                $plazas_min = '';
            }
            $plazas_txt = _("plazas casa (max - min)") . ": ";
            $plazas_txt .= !empty($plazas_max) ? $plazas_max : '?';
            $plazas_txt .= !empty($plazas_min) ? ' - ' . $plazas_min : '';
        } else {
            $plazas_txt = _("plazas actividad") . ": ";
            $plazas_txt .= !empty($this->plazas_totales) ? $this->plazas_totales : '?';
        }
        $this->plazas_txt = $plazas_txt;
    }

    private function contarPlazas(): void
    {
        /** @var ResumenPlazasService $gesActividadPlazasR */
        $gesActividadPlazasR = $this->getContainer()->get(ResumenPlazasService::class);
        $gesActividadPlazasR->setId_activ($this->id_pau);
        $this->a_plazas_conseguidas = [];
        $this->a_plazas_resumen = $gesActividadPlazasR->getResumen();
    }

    public function getCargos(): void
    {
        /** @var ActividadAllRepositoryInterface $ActividadAllRepository */
        $ActividadAllRepository = $this->getContainer()->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($this->id_pau);
        if ($oActividad === null) {
            $this->msg_err = sprintf(_('No se ha encontrado la actividad con id: %s'), $this->id_pau);

            return;
        }
        $dl_org = $oActividad->getDl_org();
        $dl_propia = (ConfigGlobal::mi_delef() === $dl_org);
        $oPermDossier = new PermDossier();
        $this->a_ref_perm = $oPermDossier->perm_pers_activ($this->id_tipo_activ,$dl_propia);

        /** @var AsistenteRepositoryInterface $AsistenteRepository */
        $AsistenteRepository = $this->getContainer()->get(AsistenteRepositoryInterface::class);
        $c = 0;
        $num = 0;
        $a_valores = [];
        $this->aListaCargos = [];
        /** @var ActividadCargoRepositoryInterface $ActividadCargoRepository */
        $ActividadCargoRepository = $this->getContainer()->get(ActividadCargoRepositoryInterface::class);
        $cCargosEnActividad = $ActividadCargoRepository->getActividadCargos(['id_activ' => $this->id_pau]);
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        /** @var CargoRepositoryInterface $CargoRepository */
        $CargoRepository = $this->getContainer()->get(CargoRepositoryInterface::class);
        /** @var PersonaFinderService $PersonaFinderService */
        $PersonaFinderService = $this->getContainer()->get(PersonaFinderService::class);
        /** @var XPermisos $oPerm */
        $oPerm = $_SESSION['oPerm'];
        foreach ($cCargosEnActividad as $oActividadCargo) {
            $c++;
            $num++;
            $id_schema = $oActividadCargo->getId_schema();
            $id_item_cargo = $oActividadCargo->getId_item();
            $id_nom = $oActividadCargo->getId_nom();
            $this->aListaCargos[] = $id_nom;
            $id_cargo = $oActividadCargo->getId_cargo();
            $oCargo = $CargoRepository->findById($id_cargo);
            if ($oCargo === null) {
                continue;
            }
            $tipo_cargo = $oCargo->getTipoCargoVo()?->value();
            $cargo = $oCargo->getCargoVo()->value();
            if ($tipo_cargo === 'sacd' && $mi_sfsv == 2) {
                continue;
            }

            if ($this->dl_org !== $this->mi_dele) {
                $oPersona = $PersonaFinderService->findPersonaEnDl($id_nom);
                if ($oPersona === null) {
                    continue;
                }
            } else {
                $oPersona = $PersonaFinderService->findPersonaEnGlobal($id_nom);
            }

            if ($oPersona === null) {
                $this->msg_err .= "<br>";
                $this->msg_err .= sprintf(_("%s. En %s linea %s"), $oPersona, __FILE__, __LINE__);
                continue;
            }

            $nom = $oPersona->getPrefApellidosNombre();
            $nombre = $oPersona->getNom();
            $apellidos = $oPersona->getApellidos();
            $sacd = ($oPersona->isSacd()) ? _("sí") : '';
            /** @var TelecoPersonaService $telecoService */
            $telecoService = $this->getContainer()->get(TelecoPersonaService::class);
            $telfs = '';
            $telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, "telf", " / ", "*", false);
            $telfs_movil = $telecoService->getTelecosPorTipo($id_nom, "móvil", " / ", "*", false);
            if (!empty($telfs_fijo) && !empty($telfs_movil)) {
                $telfs = $telfs_fijo . " / " . $telfs_movil;
            } else {
                $telfs .= $telfs_fijo ?? '';
                $telfs .= $telfs_movil ?? '';
            }
            $mails = $telecoService->getTelecosPorTipo($id_nom, "e-mail", " / ", "*", false);

            $observ_cargo = $oActividadCargo->getObserv();
            $dl_asistente = $oPersona->getDl();
            $ctr_dl = $oPersona->getCentro_o_dl();
            if ($id_tabla = $oPersona->getId_tabla()) {
                $a_act = $this->a_ref_perm[$id_tabla];
                $this->permiso = $a_act["perm"] ? 3 : 1;
            } else {
                $this->permiso = 3;
            }

            $plaza = PlazaId::PEDIDA;
            $aWhere = ['id_activ' => $this->id_pau, 'id_nom' => $id_nom];
            $aOperador = ['id_activ' => '=', 'id_nom' => '='];
            if (!empty($id_nom) && $cAsistente = $AsistenteRepository->getAsistentes($aWhere, $aOperador)) {
                if (is_array($cAsistente) && count($cAsistente) > 1) {
                    $tabla = '';
                    foreach ($cAsistente as $Asistente) {
                        $tabla .= "<li>" . $Asistente->getNomTabla() . "</li>";
                    }
                    $msg_err = _("ERROR: más de un asistente con el mismo id_nom") . "<br>";
                    $msg_err .= "<br>$nom(" . $oPersona->getId_tabla() . ")<br><br>";
                    $msg_err .= _("En las tablas") . ":<ul>$tabla</ul>";
                    throw new \RuntimeException($msg_err);
                }
                $oAsistente = $cAsistente[0];
                $propio = $oAsistente->isPropio();
                $falta = $oAsistente->isFalta();
                $est_ok = $oAsistente->isEst_ok();
                $observ = $oAsistente->getObserv();
                $plaza = empty($oAsistente->getPlaza()) ? PlazaId::PEDIDA : $oAsistente->getPlaza();

                if (ConfigGlobal::is_app_installed('actividadplazas')) {
                    $propietario = $oAsistente->getPropietario() ?? '';
                    $padre = strtok($propietario, '>');
                    $child = strtok('>');
                    $dl = $child;
                    if ($padre != $this->mi_dele) {
                        if ($plaza > PlazaId::DENEGADA) {
                            $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                            if (!empty($child) && $child != $padre) {
                                $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                            }
                        } else {
                            if (!empty($child) && $child == $this->mi_dele) {
                                $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                            } elseif (!empty($padre)) {
                                continue;
                            }
                        }
                    } else {
                        if ($child != $this->mi_dele) {
                            if ($plaza < PlazaId::ASIGNADA) {
                                continue;
                            } else {
                                $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                                $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                            }
                        } else {
                            $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                        }
                    }
                }

                $chk_propio = is_true($propio) ? _("sí") : _("no");
                $chk_falta = is_true($falta) ? _("sí") : _("no");
                $chk_est_ok = is_true($est_ok) ? _("sí") : _("no");
                $asis = "t";

                $oTipoActiv = new TiposActividades($this->id_tipo_activ);
                $sasistentes = $oTipoActiv->getAsistentesText();
                if (($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'))
                    && ($sasistentes === 's' || $sasistentes === 'sg')) {
                    $eliminar = 2;
                } else {
                    $eliminar = 1;
                }

                $a_valores[$c]['sel'] = $this->permiso == 3 ? "$id_nom#$id_item_cargo#$eliminar#$id_schema" : "";
                $a_valores[$c][4] = $chk_propio;
                $a_valores[$c][5] = $chk_est_ok;
                $a_valores[$c][6] = $chk_falta;
            } else {
                $a_valores[$c][4] = ['span' => 3, 'valor' => _("no asiste")];
                $observ = '';
                $num--;
                $asis = "f";
            }

            $a_valores[$c]['clase'] = !empty($plaza) ? 'plaza' . $plaza : 'plaza1';
            $a_valores[$c][1] = $cargo;
            $a_valores[$c][2] = "$nom  ($ctr_dl)";
            $a_valores[$c][3] = $dl_asistente;
            $a_valores[$c][7] = "$observ_cargo $observ";
            $a_valores[$c][8] = "$sacd";
            $a_valores[$c][9] = "$telfs";
            $a_valores[$c][10] = "$mails";
            $a_valores[$c][11] = "$nombre";
            $a_valores[$c][12] = "$apellidos";
            $a_valores[$c][13] = "$ctr_dl";
        }

        $this->num = $num;
        $this->a_valores = $a_valores;
    }

    public function getAsistentes(): void
    {
        /** @var AsistenteRepositoryInterface $AsistenteRepository */
        $AsistenteRepository = $this->getContainer()->get(AsistenteRepositoryInterface::class);
        $this->a_asistentes = [];
        $cAsistentes = $AsistenteRepository->getAsistentes(['id_activ' => $this->id_pau]);
        /** @var PersonaFinderService $PersonaFinderService */
        $PersonaFinderService = $this->getContainer()->get(PersonaFinderService::class);
        foreach ($cAsistentes as $oAsistente) {
            $this->num++;
            $id_nom = $oAsistente->getId_nom();
            if (in_array($id_nom, $this->aListaCargos)) {
                $this->num--;
                continue;
            }

            if ($this->dl_org !== $this->mi_dele) {
                $oPersona = $PersonaFinderService->findPersonaEnDl($id_nom);
                if ($oPersona === null) {
                    continue;
                }
            } else {
                $oPersona = $PersonaFinderService->findPersonaEnGlobal($id_nom);
            }

            if ($oPersona === null) {
                $this->msg_err .= "<br>";
                $this->msg_err .= sprintf(_("%s. En %s linea %s"), $oPersona, __FILE__, __LINE__);
                continue;
            }
            $obj_pau = $oPersona->getClassName();
            $nom = $oPersona->getPrefApellidosNombre();
            $nombre = $oPersona->getNom();
            $apellidos = $oPersona->getApellidos();
            $sacd = ($oPersona->isSacd()) ? _("sí") : '';
            $dl_asistente = $oPersona->getDl();
            $ctr_dl = $oPersona->getCentro_o_dl();
            /** @var TelecoPersonaService $telecoService */
            $telecoService = $this->getContainer()->get(TelecoPersonaService::class);
            $telfs = '';
            $telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, "telf", " / ", "*", false);
            $telfs_movil = $telecoService->getTelecosPorTipo($id_nom, "móvil", " / ", "*", false);
            if (!empty($telfs_fijo) && !empty($telfs_movil)) {
                $telfs = $telfs_fijo . " / " . $telfs_movil;
            } else {
                $telfs .= $telfs_fijo ?? '';
                $telfs .= $telfs_movil ?? '';
            }
            $mails = $telecoService->getTelecosPorTipo($id_nom, "e-mail", " / ", "*", false);

            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();
            $plaza = 2;

            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $plaza = empty($oAsistente->getPlaza()) ? PlazaId::PEDIDA : $oAsistente->getPlaza();
                $propietario = $oAsistente->getPropietario();
                if ($propietario === null || $propietario === 'xxx') {
                    $this->msg_err .= _("ALERTA: asistente sin propiedad en la plaza") . ":<br>";
                    $this->msg_err .= "$nom(" . $oPersona->getId_tabla() . ")<br>";
                    $propietario = '';
                }
                $padre = strtok($propietario, '>');
                $child = strtok('>');
                $dl = $child;
                if ($obj_pau === 'PersonaEx' && $this->mi_dele != $this->dl_org) {
                    if ($oAsistente->getDl_responsable() != $this->mi_dele) {
                        continue;
                    }
                }
                if ($padre != $this->mi_dele) {
                    if ($plaza > PlazaId::DENEGADA) {
                        $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                        if (!empty($child) && $child != $padre) {
                            $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                        }
                    } else {
                        if (!empty($child) && $child == $this->mi_dele) {
                            $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                        } elseif (!empty($padre)) {
                            continue;
                        }
                    }
                } else {
                    if ($child != $this->mi_dele) {
                        if ($plaza < PlazaId::ASIGNADA) {
                            continue;
                        } else {
                            $this->incrementa($this->a_plazas_conseguidas[$child][$padre]['ocupadas'][$dl][$plaza]);
                            $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                        }
                    } else {
                        $this->incrementa($this->a_plazas_resumen[$padre][$dl]['ocupadas'][$plaza]);
                    }
                }
            }

            $chk_propio = is_true($propio) ? _("sí") : _("no");
            $chk_falta = is_true($falta) ? _("sí") : _("no");
            $chk_est_ok = is_true($est_ok) ? _("sí") : _("no");

            $a_val = [];
            $a_val['sel'] = $this->permiso == 3 ? "$id_nom" : "";
            $a_val['clase'] = !empty($plaza) ? 'plaza' . $plaza : 'plaza1';
            $a_val[2] = "$nom  ($ctr_dl)";
            $a_val[3] = $dl_asistente;
            $a_val[4] = $chk_propio;
            $a_val[5] = $chk_est_ok;
            $a_val[6] = $chk_falta;
            $a_val[7] = $observ;
            $a_val[8] = $sacd;
            $a_val[9] = "$telfs";
            $a_val[10] = "$mails";
            $a_val[11] = "$nombre";
            $a_val[12] = "$apellidos";
            $a_val[13] = "$ctr_dl";

            $this->a_asistentes[$nom] = $a_val;
        }
        uksort($this->a_asistentes, "src\shared\domain\helpers\strsinacentocmp");
    }

    public function getLeyenda(): void
    {
        $leyenda_html = '';
        $resumen_plazas = '';
        $resumen_plazas2 = '';
        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            $explicacion1 = _("plaza que contabiliza pero que las otras delegaciones no ven. Podría explicarse como una plaza que se desea pero no se puede conceder porque no hay sitio");
            $explicacion2 = _("como la plaza pedida, pero cuando ya se ha solicitado a la otra delegación que nos conceda ese plaza. Implica que por nuestra parte nos parece correcto que vaya pero necesitamos confirmación de que hay sitio");
            $explicacion4 = _("plaza ocupada en toda regla. Las delegaciones organizadoras ven a los nuestros. Si somos nosotros los organizadores, podemos ocupar más plazas de las previstas. Si son de otra delegación, no debería poder pasar a asignada si no hay plazas");
            $explicacion5 = _("como la anterior pero con el plus de que se ha comunicado al interesado y no hay cambio");

            $leyenda_html = '<p class="contenido">';
            $leyenda_html .= _("para selección múltiple: 'Ctrl+Click' o bien 'Mays+Click'");
            $leyenda_html .= "<br><style>
				.box {
				display: inline;
				height: 1em;
				line-height: 3;
				padding: 0.3em;
				border-style: outset;
				cursor: pointer;
				}
				</style>
				";
            $aOpciones = PlazaId::getArrayPosiblesPlazas();
            foreach ($aOpciones as $plaza => $plaza_txt) {
                $expl = "explicacion$plaza";
                $explicacion = $$expl;
                $leyenda_html .= "<div class='box plaza$plaza' onCLick=fnjs_cmb_plaza('#frm_asistentes_a_una_actividad','$plaza') title=\"$explicacion\">$plaza_txt</div>  ";
            }
            $leyenda_html .= "</p>";
            if ($this->publicado === true) {
                if (array_key_exists($this->mi_dele, $this->a_plazas_resumen)) {
                    foreach ($this->a_plazas_resumen as $padre => $aa) {
                        if ($padre === 'total') {
                            continue;
                        }
                        if ($padre != $this->mi_dele && $this->mi_dele != $this->dl_org) {
                            continue;
                        }
                        $a_conseguidas = empty($aa['conseguidas']) ? [] : $aa['conseguidas'];
                        $total_cedidas = empty($aa['total_cedidas']) ? 0 : $aa['total_cedidas'];
                        $total_disponibles = empty($aa['total_disponibles']) ? 0 : $aa['total_disponibles'];
                        $a_cedidas = empty($aa['cedidas']) ? [] : $aa['cedidas'];
                        $json_cedidas = (count($a_cedidas) > 0) ? json_encode($a_cedidas) : '';
                        $a_ocupadas = empty($aa['ocupadas']) ? [] : $aa['ocupadas'];
                        $total = $total_disponibles;

                        $decidir = 0;
                        $espera = 0;
                        $ocupadas = 0;
                        $continuacion = false;
                        $resumen_plazas .= "$padre: ";
                        $ocupadas_calendario = empty($a_ocupadas[$padre]) ? 0 : $a_ocupadas[$padre];
                        if ($ocupadas_calendario > 0) {
                            $resumen_plazas .= "$ocupadas_calendario($padre)";
                        }
                        $ocupadas += $ocupadas_calendario;

                        $i = 0;
                        $ocupadas_dl = 0;
                        foreach ($a_cedidas as $dl2 => $numCedidas) {
                            $i++;
                            if (!empty($ocupadas_dl)) {
                                $resumen_plazas .= ($continuacion) ? ' + ' : '';
                                $resumen_plazas .= "$ocupadas_dl($dl2)";
                            }
                            $this->a_plazas_resumen[$padre]['cedidas'][$dl2] = ['ocupadas' => $ocupadas_dl];
                            if ($this->mi_dele != $dl2 && $dl2 != $this->dl_org) {
                                $pl = empty($numCedidas) ? 0 : $numCedidas;
                                if (!array_key_exists($dl2, $this->a_plazas_resumen)) {
                                    for ($i = $ocupadas_dl + 1; $i <= $pl; $i++) {
                                        $nom = "$dl2----$i";
                                        $a_val = [];
                                        $a_val['sel'] = '';
                                        $a_val['clase'] = 'plaza4';
                                        $a_val[2] = $nom;
                                        $a_val[3] = '';
                                        $a_val[4] = '';
                                        $a_val[5] = '';
                                        $a_val[6] = '';
                                        $this->a_asistentes[$nom] = $a_val;
                                    }
                                }
                            }
                        }
                        $ocupadas += $ocupadas_dl;
                        if (!empty($a_conseguidas)) {
                            $ocupadas_otra = 0;
                            foreach ($a_conseguidas as $dl => $pl) {
                                $pl_ocu = $a_ocupadas[$dl];
                                $resumen_plazas .= ($continuacion) ? ' + ' : '';
                                $txt = sprintf(_("(de las %s cedidas por %s)"), $pl, $dl);
                                $resumen_plazas .= $pl_ocu . " " . $txt;
                                $ocupadas_otra += $pl_ocu;
                                $continuacion = true;
                            }
                            $ocupadas += $ocupadas_otra;
                        }

                        if (!empty($json_cedidas)) {
                            $resumen_plazas .= " [" . _("cedidas") . "=$total_cedidas $json_cedidas ]";
                        }
                        $resumen_plazas .= "  => " . _("ocupadas") . "=$ocupadas/($total_disponibles)";
                        $libres = $total_disponibles - $ocupadas;
                        if ($libres < 0) {
                            $resumen_plazas .= "<span style='background-color: red'> libres= $libres</span>";
                        } else {
                            $resumen_plazas .= " libres=$libres";
                        }
                        if ($this->mi_dele == $padre) {
                            if (!empty($espera)) {
                                $resumen_plazas .= " " . sprintf(_("(%s en espera)"), $espera);
                            }
                            if (!empty($decidir)) {
                                $resumen_plazas .= " " . sprintf(_("(%s por decidir)"), $decidir);
                            }
                        }
                        $resumen_plazas .= ";<br>";
                        if ($this->mi_dele != $padre && $padre != $this->dl_org) {
                            $ocu_relleno = $total - $libres;
                            for ($i = $ocu_relleno + 1; $i <= $total; $i++) {
                                $nom = "$padre-$i";
                                $a_val = [];
                                $a_val['sel'] = '';
                                $a_val['clase'] = 'plaza4';
                                $a_val[2] = $nom;
                                $a_val[3] = '';
                                $a_val[4] = '';
                                $a_val[5] = '';
                                $a_val[6] = '';
                                $this->a_asistentes[$nom] = $a_val;
                            }
                        }
                    }
                } elseif (array_key_exists($this->mi_dele, $this->a_plazas_conseguidas)) {
                    $a_dl_plazas = $this->a_plazas_conseguidas[$this->mi_dele];
                    $resumen_plazas2 = "$this->mi_dele: ";
                    $p = 0;
                    foreach ($a_dl_plazas as $dl2 => $pla) {
                        $plazas = empty($pla['ocupadas']) ? [] : $pla['ocupadas'];
                        $pla['cedidas'] = empty($pla['cedidas']) ? '?' : $pla['cedidas'];
                        foreach ($plazas as $dl => $pl) {
                            $p++;
                            $decidir = 0;
                            $espera = 0;
                            $ocupadas_dl = 0;
                            foreach ($pl as $plaza => $num) {
                                if ($plaza == PlazaId::PEDIDA) {
                                    $decidir += $num;
                                }
                                if ($plaza == PlazaId::EN_ESPERA) {
                                    $espera += $num;
                                }
                                if ($plaza > PlazaId::DENEGADA) {
                                    $ocupadas_dl += $num;
                                }
                            }
                            $txt = sprintf(_("(de las %s cedidas por %s)"), $pla['cedidas'], $dl2);
                            $resumen_plazas2 .= ($p > 1) ? ' + ' : '';
                            $resumen_plazas2 .= $ocupadas_dl . " " . $txt;
                            if (!empty($espera)) {
                                $resumen_plazas2 .= " " . sprintf(_("(%s en espera)"), $espera);
                            }
                            if (!empty($decidir)) {
                                $resumen_plazas2 .= " " . sprintf(_("(%s por decidir)"), $decidir);
                            }
                        }
                    }
                    $resumen_plazas2 .= ";<br>";
                }
            }
        }
        $this->leyenda_html = $leyenda_html;
        $this->resumen_plazas = $resumen_plazas;
        $this->resumen_plazas2 = $resumen_plazas2;
    }

    public function getValores()
    {
        return $this->a_valores;
    }

    public function getTabla(): void
    {
        if (ConfigGlobal::is_app_installed('actividadcargos')) {
            $this->getCargos();
            $c = count($this->a_valores);
        } else {
            $c = 0;
            $this->num = 0;
            $this->a_valores = [];
        }

        $this->getAsistentes();
        $this->getLeyenda();

        $n = $c;
        foreach ($this->a_asistentes as $val) {
            $c++;
            $val[1] = "-";
            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                if ($val['clase'] === 'plaza4' || $val['clase'] === 'plaza5') {
                    $n++;
                    $val[1] = "$n.-";
                }
            } else {
                $n++;
                $val[1] = "$n.-";
            }
            $this->a_valores[$c] = $val;
        }
        if (!empty($this->a_valores)) {
            if (!empty($this->Qid_sel)) {
                $this->a_valores['select'] = $this->Qid_sel;
            }
            if (!empty($this->Qscroll_id)) {
                $this->a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getSegmentData(): array
    {
        $this->msg_err = '';
        $this->txt_eliminar = _("¿Está seguro que desea borrar a esta persona de esta actividad?");
        $this->getTituloPlazas();

        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            $this->contarPlazas();
        }
        $this->getTabla();
        $this->setLinksInsert();

        return [
            'segment_tipo' => 'select_asistentes_a_una_actividad',
            'id_pau' => $this->id_pau,
            'plazas_txt' => $this->plazas_txt ?? '',
            'resumen_plazas' => $this->resumen_plazas ?? '',
            'resumen_plazas2' => $this->resumen_plazas2 ?? '',
            'leyenda_html' => $this->leyenda_html ?? '',
            'msg_err' => (string) ($this->msg_err ?? ''),
            'plazas_installed' => ConfigGlobal::is_app_installed('actividadplazas'),
            'wrapper' => [
                'txt_eliminar' => (string) $this->txt_eliminar,
                'bloque' => (string) ($this->bloque ?? ''),
                'url_form_relative' => DossierTipoPublicUrls::relativeFormController(self::ID_TIPO_DOSSIER),
                'url_form_cargos_relative' => DossierTipoPublicUrls::relativeFormController(3102),
                'url_mover_path' => 'frontend/asistentes/controller/asistente_mover.php',
                'url_plaza_asignar_path' => 'src/asistentes/asistente_plaza_asignar',
                'url_eliminar_path' => 'src/asistentes/asistente_eliminar',
                'url_cargo_eliminar_path' => 'src/actividadcargos/cargo_eliminar',
            ],
            'hash_main' => [
                'campos_form' => '',
                'campos_no' => 'sel!scroll_id!mod!que!refresh',
                'campos_hidden' => [
                    'pau' => $this->pau,
                    'id_pau' => $this->id_pau,
                    'obj_pau' => $this->obj_pau,
                    'id_dossier' => $this->id_dossier,
                    'queSel' => $this->queSel,
                    'permiso' => 3,
                ],
            ],
            'hash_matriculas' => [
                'campos_form' => '',
                'campos_no' => 'sel!scroll_id!mod',
                'campos_hidden' => [
                    'queSel' => 'matriculas',
                    'pau' => 'p',
                    'obj_pau' => 'Persona',
                    'id_dossier' => 1303,
                    'permiso' => 3,
                    'id_activ' => $this->id_pau,
                ],
            ],
            'ajax_hash_mover' => [
                'path' => 'frontend/asistentes/controller/asistente_mover.php',
                'campos_form' => 'id_pau!id_activ',
            ],
            'ajax_hash_plaza' => [
                'path' => 'src/asistentes/asistente_plaza_asignar',
                'campos_form' => 'plaza!lista_json!id_activ',
            ],
            'tabla' => [
                'id_tabla' => 'select_asistentes_a_una_actividad',
                'cabeceras' => $this->getCabeceras(),
                'botones' => $this->getBotones(),
                'valores' => $this->getValores(),
            ],
            'links_dl_specs' => $this->aLinks_dl,
        ];
    }

    public function setLinksInsert(): void
    {
        $this->aLinks_dl = [];
        $ref_perm = $this->a_ref_perm;
        if (empty($ref_perm) || $this->permiso < 2 || ConfigGlobal::mi_ambito() === 'rstgr') {
            return;
        }
        $mi_dele = ConfigGlobal::mi_delef();
        reset($ref_perm);
        foreach ($ref_perm as $val) {
            if (empty($val["perm"])) {
                continue;
            }
            $obj_pau = $val["obj"];
            $nom = $val["nom"];
            $aQuery = [
                'mod' => 'nuevo',
                'que_dl' => $mi_dele,
                'pau' => $this->pau,
                'obj_pau' => $obj_pau,
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            $this->aLinks_dl[$nom] = DossierTipoPublicUrls::formControllerLinkSpec(self::ID_TIPO_DOSSIER, $aQuery);
        }
    }

    public function getId_dossier() { return $this->id_dossier; }
    public function getPau() { return $this->pau; }
    public function getObj_pau() { return $this->obj_pau; }
    public function getId_pau() { return $this->id_pau; }
    public function getPermiso() { return $this->permiso; }
    public function getStatus() { return $this->status; }

    public function setId_dossier($Qid_dossier): void { $this->id_dossier = $Qid_dossier; }
    public function setPau($Qpau): void { $this->pau = $Qpau; }
    public function setObj_pau($Qobj_pau): void { $this->obj_pau = $Qobj_pau; }
    public function setId_pau($Qid_pau): void
    {
        $this->id_pau = $Qid_pau;
        $this->mi_dele = ConfigGlobal::mi_delef();
        $this->getDatosActividad();
    }
    public function setPermiso($Qpermiso): void { $this->permiso = $Qpermiso; }
    public function setStatus($Qstatus): void { $this->status = $Qstatus; }
    public function setQid_sel($Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id($Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque($bloque): void { $this->bloque = $bloque; }
    public function setQueSel($queSel): void { $this->queSel = (string) $queSel; }
}
