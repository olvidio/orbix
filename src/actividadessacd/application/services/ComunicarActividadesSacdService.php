<?php

namespace src\actividadessacd\application\services;

use src\personas\domain\entity\PersonaPub;
use src\shared\config\ConfigGlobal;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaSacd;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\ColaMailId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\domain\value_objects\Uuid;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\permisos\domain\PermisosActividades;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Servicio que construye la estructura de comunicacion de actividades a
 * los sacd y, en un segundo paso opcional, encola los mails en la tabla
 * `cola_mails` para que los envie el servidor exterior.
 *
 * Sucesor de `actividadessacd\model\ComunicarActividadesSacd`. Correcciones:
 *  - `envairMails()` renombrado a `enviarMails()` (typo legacy).
 *  - `enviarMails()` devuelve texto de error en lugar de `exit(...)`.
 */
final class ComunicarActividadesSacdService
{
    /** @var list<PersonaSacd|PersonaEx|null> */
    private array $cPersonas = [];
    private string $inicioIso = '';
    private string $finIso = '';
    private string $propuesta = '';
    private bool $soloCargos = false;
    private bool $quitarInactivos = false;

    public function __construct(
        private CargoRepositoryInterface $cargoRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private ActividadesSacdHelper $actividadesSacdHelper,
        private ConfigSchemaRepositoryInterface $configSchemaRepository,
        private UsuarioRepositoryInterface $usuarioRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private TelecoPersonaService $telecoPersonaService,
        private ColaMailRepositoryInterface $colaMailRepository,
    ) {
    }

    /**
     * @param array<int, PersonaSacd|PersonaEx|null> $cPersonas
     */
    public function setPersonas(array $cPersonas): void
    {
        $this->cPersonas = array_values($cPersonas);
    }

    public function setInicioIso(string $inicioIso): void
    {
        $this->inicioIso = $inicioIso;
    }

    public function setFinIso(string $finIso): void
    {
        $this->finIso = $finIso;
    }

    public function setPropuesta(string $propuesta): void
    {
        $this->propuesta = $propuesta;
    }

    public function setSoloCargos(bool $soloCargos): void
    {
        $this->soloCargos = $soloCargos;
    }

    public function setQuitarInactivos(bool $quitarInactivos): void
    {
        $this->quitarInactivos = $quitarInactivos;
    }

    /**
     * Estructura: `id_nom => ['nom_ap', 'txt' => [clave=>texto], 'actividades' => [...]]`.
     * Si `quitarInactivos=true` y una persona no tiene actividades, se
     * excluye del array de salida.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getArrayComunicacion(): array
    {
        $aIdCargos_sacd = $this->cargoRepository->getArrayCargos('sacd');

        $oHelper = $this->actividadesSacdHelper;
        $array_actividades = [];

        $aWhereAct = [
            'f_ini' => "'" . $this->finIso . "'",
            'f_fin' => "'" . $this->inicioIso . "'",
            'status' => '2',
        ];
        $aOperadorAct = [
            'f_ini' => '<=',
            'f_fin' => '>=',
        ];

        $ActividadAllRepository = $this->actividadAllRepository;
        $CentroEncargadoRepository = $this->centroEncargadoRepository;
        $ActividadCargoRepository = $this->actividadCargoRepository;

        foreach ($this->cPersonas as $oPersona) {
            if (!($oPersona instanceof PersonaPub || $oPersona instanceof PersonaSacd || $oPersona instanceof PersonaEx)) {
                continue;
            }
            $id_nom = (int)$oPersona->getId_nom();
            $nom_ap = $oPersona->getPrefApellidosNombre();
            $idioma = $oPersona->getIdiomaPreferidoVo()?->value();

            $array_actividades[$id_nom]['nom_ap'] = $nom_ap;
            foreach (['com_sacd', 't_propio', 't_f_ini', 't_f_fin', 't_nombre_ubi',
                         't_sfsv', 't_actividad', 't_asistentes', 't_encargado',
                         't_observ', 't_nom_tipo'] as $clave) {
                $array_actividades[$id_nom]['txt'][$clave] = $oHelper->getTraduccion($clave, $idioma);
            }

            $aWhere = ['id_nom' => $id_nom];
            $aOperador = [];

            if ($this->soloCargos) {
                $cAsistentes = $ActividadCargoRepository->getCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
            } else {
                $cAsistentes = $ActividadCargoRepository->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
            }

            $ord_activ = [];
            foreach ($cAsistentes as $aAsistente) {
                $id_activ = $this->mixedToInt($aAsistente['id_activ'] ?? 0);
                $propio = FuncTablasSupport::isTrue($aAsistente['propio'] ?? false) ?? false;
                $id_cargo = isset($aAsistente['id_cargo']) && $aAsistente['id_cargo'] !== ''
                    ? $this->mixedToInt($aAsistente['id_cargo'])
                    : null;

                $oPermSesion = $_SESSION['oPermActividades'] ?? null;
                if ($oPermSesion instanceof PermisosActividades) {
                    $oPermSesion->setId_activ($id_activ);
                    if (!FuncTablasSupport::isTrue($this->propuesta) && ConfigGlobal::is_app_installed('procesos')) {
                        $permiso_ver = $oPermSesion->havePermisoSacd($id_cargo, $propio);
                    } else {
                        $permiso_ver = true;
                    }
                } else {
                    $permiso_ver = true;
                }
                if (!FuncTablasSupport::isTrue($permiso_ver)) {
                    continue;
                }

                $oActividad = $ActividadAllRepository->findById($id_activ);
                if ($oActividad === null) {
                    continue;
                }
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $id_ubi = $oActividad->getId_ubi();
                $lugar_esp = $oActividad->getLugar_esp();
                $oF_ini = $oActividad->getF_ini();
                $oF_fin = $oActividad->getF_fin();
                if ($oF_ini === null || $oF_fin === null) {
                    continue;
                }
                $h_ini = $oActividad->getH_ini();
                $h_fin = $oActividad->getH_fin();
                $observ = (string)$oActividad->getObserv();

                $f_ini = $oF_ini->formatRoman();
                $f_fin = $oF_fin->formatRoman();
                if ($h_ini instanceof TimeLocal) {
                    $f_ini .= ' (' . $h_ini->format('H:i') . ')';
                }
                if ($h_fin instanceof TimeLocal) {
                    $f_fin .= ' (' . $h_fin->format('H:i') . ')';
                }

                $oTipoActiv = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActiv->getSfsvText();
                $sasistentes = $oTipoActiv->getAsistentesText();
                $sactividad = $oTipoActiv->getActividadText();
                $snom_tipo = $oTipoActiv->getNom_tipoText();

                if (empty($lugar_esp)) {
                    $oCasa = Ubi::NewUbi((int)$id_ubi);
                    $nombre_ubi = $oCasa?->getNombre_ubi() ?? '?';
                } else {
                    $nombre_ubi = $lugar_esp;
                }

                $ctrs = '';
                foreach ($CentroEncargadoRepository->getCentrosEncargadosActividad($id_activ) as $oCentro) {
                    if ($ctrs !== '') {
                        $ctrs .= ', ';
                    }
                    $ctrs .= $oCentro->getNombre_ubi();
                }

                $cargo = '';
                if ($id_cargo !== null && !array_key_exists($id_cargo, $aIdCargos_sacd)) {
                    $cargo = 'te carrec';
                }
                $array_act = [
                    'propio' => $propio,
                    'f_ini' => $f_ini,
                    'f_fin' => $f_fin,
                    'nombre_ubi' => $nombre_ubi,
                    'id_activ' => $id_activ,
                    'sfsv' => $ssfsv,
                    'asistentes' => $sasistentes,
                    'actividad' => $sactividad,
                    'nom_tipo' => $snom_tipo,
                    'observ' => $observ,
                    'cargo' => $cargo,
                    'encargado' => $ctrs,
                ];
                // clave de orden: Ymd. Si hay duplicados, sumamos 1 para
                // mantener a todos.
                $f_ord = (int)$oF_ini->format('Ymd');
                while (array_key_exists($f_ord, $ord_activ)) {
                    $f_ord++;
                }
                $ord_activ[$f_ord] = $array_act;
            }

            if (!empty($ord_activ)) {
                ksort($ord_activ);
                $array_actividades[$id_nom]['actividades'] = array_values($ord_activ);
            } else {
                $array_actividades[$id_nom]['actividades'] = [];
                if ($this->quitarInactivos) {
                    unset($array_actividades[$id_nom]);
                }
            }
        }
        return $array_actividades;
    }

    /**
     * Encola mails en `cola_mails` (uno para el sacd + copia al jefe de
     * calendario; otro para el ctr del sacd, si tiene mail). Devuelve
     * texto de error vacio si todo OK, o descriptivo si falta algun
     * dato esencial (jefe de calendario, mail del jefe...).
     */
    /**
     * @param array<int, array<string, mixed>> $array_actividades
     */
    public function enviarMails(array $array_actividades): string
    {
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $oHelper = $this->actividadesSacdHelper;
        $poblacion = $oHelper->getLugar_dl();
        $lugar_fecha = "$poblacion, $hoy_local";

        $asunto = _("atención actividades");
        $mi_dele = ConfigGlobal::mi_dele();

        $oConfigSchema = $this->configSchemaRepository->findById('jefe_calendario');
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            return _("falta el definir el jefe de calendario");
        }
        $a_jefes_calendario = explode(',', $valor);
        $jefe_calendario = $a_jefes_calendario[0];

        $cUsuarios = $this->usuarioRepository->getUsuarios(['usuario' => $jefe_calendario]);
        if (empty($cUsuarios)) {
            return _("No hay un mail (jefe calendario) para enviar los errores. No se procesan los mails.");
        }
        $oUsuarioJefe = $cUsuarios[0];
        $e_mail_jefe = $oUsuarioJefe->getEmailAsString();
        if (empty($e_mail_jefe)) {
            return _("No hay un mail (jefe calendario) para enviar los errores. No se procesan los mails.");
        }

        $PersonaDlRepository = $this->personaDlRepository;
        $CentroDlRepository = $this->centroDlRepository;
        $telecoService = $this->telecoPersonaService;
        $ColaMailRepository = $this->colaMailRepository;

        foreach ($array_actividades as $id_nom => $vector) {
            $oPersona = $PersonaDlRepository->findById((int)$id_nom);
            $nom_ap = $this->mixedToString($vector['nom_ap'] ?? '');

            $txtBlock = $vector['txt'] ?? [];
            if (!is_array($txtBlock)) {
                continue;
            }
            $propio = $this->mixedToString($txtBlock['t_propio'] ?? '');
            $f_ini = $this->mixedToString($txtBlock['t_f_ini'] ?? '');
            $f_fin = $this->mixedToString($txtBlock['t_f_fin'] ?? '');
            $nombre_ubi = $this->mixedToString($txtBlock['t_nombre_ubi'] ?? '');
            $sfsv = $this->mixedToString($txtBlock['t_sfsv'] ?? '');
            $actividad = $this->mixedToString($txtBlock['t_actividad'] ?? '');
            $asistentes = $this->mixedToString($txtBlock['t_asistentes'] ?? '');
            $encargado = $this->mixedToString($txtBlock['t_encargado'] ?? '');
            $observ = $this->mixedToString($txtBlock['t_observ'] ?? '');
            $nom_tipo = $this->mixedToString($txtBlock['t_nom_tipo'] ?? '');
            $txt = $this->mixedToString($txtBlock['com_sacd'] ?? '');
            $a_actividades = $vector['actividades'] ?? [];
            if (!is_array($a_actividades) || empty($a_actividades)) {
                continue;
            }

            $e_mail_sacd = $telecoService->getEmailPrincipalOPrimero((int)$id_nom);
            $idioma = $oPersona?->getIdioma_preferido() ?? '';

            $id_ctr = $oPersona?->getId_ctr();
            $e_mail_ctr = '';
            if (!empty($id_ctr)) {
                $oCentroDl = $CentroDlRepository->findById($id_ctr);
                $e_mail_ctr = $oCentroDl?->emailPrincipalOPrimero() ?? '';
            }

            $email = $e_mail_jefe;
            $email .= empty($e_mail_sacd) ? '' : ", $e_mail_sacd";

            $body_cabecera = "<div style='clear: both; display: block; width: 25cm; page-break-after: always; font-family: sans-serif; font-size: 12pt;'>
                <br><!-- si no pongo esta linea, no me imprime el nombre (a partir de la 2ª página -->
                  <div style='display: block;width: 25cm;'>";
            $body_cabecera .= "<div style = 'display: block; float: left; text-align: left; width: 10cm;'>$nom_ap</div>";
            $body_cabecera .= "<div style='display: block; float: right; text-align: right; width: 10cm'>vc-$mi_dele</div>";
            $body_cabecera .= "</div>";
            $body_cabecera .= "<div style = 'display: block; padding-left: 15mm; padding-right: 10mm; padding-top: 10mm; padding-bottom: 10mm;'>$txt</div>";
            $body_cabecera .= " <!-- Actividades -->
                <table  style='border: 1px solid #000; border-collapse: collapse; margin: 0; margin-bottom: 1em; padding: 6px;'>
                    <tr>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$f_ini</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$f_fin</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$nombre_ubi</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$sfsv</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$actividad</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$asistentes</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$encargado</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$observ</td>
                        <td style='text-align: center; font-weight: bold; padding: 1rem; outline: 1px solid black;'>$nom_tipo</td>
                    </tr>";
            $body_sacd = '';
            $body_ctr = '';
            foreach ($a_actividades as $act) {
                if (!is_array($act)) {
                    continue;
                }
                $actPropio = FuncTablasSupport::isTrue($act['propio'] ?? false);
                $actFIni = $this->mixedToString($act['f_ini'] ?? '');
                $actFFin = $this->mixedToString($act['f_fin'] ?? '');
                $actNombreUbi = $this->mixedToString($act['nombre_ubi'] ?? '');
                $actSfsv = $this->mixedToString($act['sfsv'] ?? '');
                $actActividad = $this->mixedToString($act['actividad'] ?? '');
                $actAsistentes = $this->mixedToString($act['asistentes'] ?? '');
                $actEncargado = $this->mixedToString($act['encargado'] ?? '');
                $actObserv = $this->mixedToString($act['observ'] ?? '');
                $actNomTipo = $this->mixedToString($act['nom_tipo'] ?? '');
                $actCargo = $this->mixedToString($act['cargo'] ?? '');

                $marca = $actPropio ? '*' : '';
                $cargo_observ = $actCargo !== '' ? ($actCargo . '. ' . $actObserv) : $actObserv;

                $body_sacd .= "<tr>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>$marca $actFIni</td>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>$actFFin</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actNombreUbi</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actSfsv</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actActividad</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actAsistentes</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actEncargado</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$cargo_observ</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actNomTipo</td>
                    </tr>";

                $body_ctr .= "<tr>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>$marca $actFIni</td>
                        <td style='text-align: left; padding: 1rem; outline: 1px solid black;'>$actFFin</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actNombreUbi</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actSfsv</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actActividad</td>
                        <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actAsistentes</td>
                        ";
                if ($actSfsv === 'sf') {
                    $body_ctr .= "<td style='text-align: center; padding: 1rem; outline: 1px solid black;'></td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'></td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'></td>
                        </tr>";
                } else {
                    $body_ctr .= "<td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actEncargado</td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$cargo_observ</td>
                            <td style='text-align: center; padding: 1rem; outline: 1px solid black;'>$actNomTipo</td>
                        </tr>";
                }
            }
            $body_pie = "</table>";
            $body_pie .= "<div style = 'display: block;width: 25cm;'>
                        <div style = 'display: block; float: left; text-align: left;width: 10cm'>*) $propio</div>
                        <div style='display: block; float: right; text-align: right;width: 10cm'>$lugar_fecha</div>
                    </div>";
            $body_pie .= "</div>";

            $body_sacd = $body_cabecera . $body_sacd . $body_pie;
            $body_ctr = $body_cabecera . $body_ctr . $body_pie;

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "From: Aquinate <no-Reply@moneders.net>\r\n";
            $headers .= "Reply-To: $e_mail_jefe\r\n";
            $headers .= "Return-path: $e_mail_jefe\r\n";

            $write_by = basename(__FILE__);

            $cuerpo = "<html lang=\"$idioma\"><body>$body_sacd</body></html>";
            $ColaMailId = new ColaMailId(Uuid::random());
            $oColaMail = new ColaMail();
            $oColaMail->setUuid_item($ColaMailId);
            $oColaMail->setMail_to($email);
            $oColaMail->setHeaders($headers);
            $oColaMail->setMessage($cuerpo);
            $oColaMail->setSubject($asunto);
            $oColaMail->setWrited_by($write_by);
            $ColaMailRepository->Guardar($oColaMail);

            if (!empty($e_mail_ctr)) {
                $cuerpo = "<html lang=\"$idioma\"><body>$body_ctr</body></html>";
                $ColaMailId = new ColaMailId(Uuid::random());
                $oColaMail = new ColaMail();
                $oColaMail->setUuid_item($ColaMailId);
                $oColaMail->setMail_to($e_mail_ctr);
                $oColaMail->setHeaders($headers);
                $oColaMail->setMessage($cuerpo);
                $oColaMail->setSubject($asunto);
                $oColaMail->setWrited_by($write_by);
                $ColaMailRepository->Guardar($oColaMail);
            }
        }
        return '';
    }

    private function mixedToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }
        if (is_bool($value)) {
            return $value ? 't' : 'f';
        }
        return '';
    }

    private function mixedToInt(mixed $value): int
    {
        return is_numeric($value) ? (int)$value : 0;
    }
}
