<?php

namespace src\cambios\application;

use DateInvalidTimeZoneException;
use DateTimeZone;
use frontend\shared\web\Lista;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\shared\config\ConfigGlobal;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\ColaMailId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Caso de uso para encolar por e-mail los avisos pendientes de cada usuario.
 *
 * Debe ejecutarse en el servidor **interior** (crontab horario SV/SF), donde
 * hay acceso a las tablas de personas para resolver nombres en el texto.
 * Inserta filas en `cola_mails`; el envío real lo hace
 * `EnviarMailsEnCola` en el servidor exterior (DMZ).
 *
 * El use case:
 *   1. Dispara `Cambio::generarTabla()` para asegurar que no quedan cambios
 *      pendientes de anotar en otras dl, espera 60s (salvo tests).
 *   2. Itera los `CambioUsuario` con `aviso_tipo = TIPO_MAIL` no avisados.
 *   3. Para cada usuario construye una tabla HTML con sus cambios y encola
 *      un unico mail. Elimina los `CambioUsuario` despues de encolar.
 *
 * Deuda conocida:
 *   - La construccion del HTML del body usa `frontend\shared\web\Lista`;
 *     deberia moverse a una vista/template.
 */
class AvisosEncolarMails
{
    public const WRITED_BY = 'avisos_cambios';

    public function __construct(
        private CambioUsuarioRepositoryInterface $cambioUsuarioRepository,
        private UsuarioRepositoryInterface $usuarioRepository,
        private PreferenciaRepositoryInterface $preferenciaRepository,
        private CambioParaAvisoLookup $cambioParaAvisoLookup,
        private CambioAvisoTxtBuilder $cambioAvisoTxtBuilder,
        private ColaMailRepositoryInterface $colaMailRepository,
    ) {
    }

    /**
     * @return array{encolados: int, usuarios_sin_email: int, total_avisos: int}
     */
    public function execute(bool $dispararGenerarTabla = true): array
    {
        // Para asegurar que coge los cambios de otras dl que no tengan instalado
        // el modulo de cambios, hay que ejecutar el generarTabla() y esperar a
        // que acabe en background.
        if ($dispararGenerarTabla) {
            $oCambio = new Cambio();
            $oCambio->generarTabla();
            sleep(60);
        }

        $dele = ConfigGlobal::mi_dele();
        $delef = $dele . 'f';
        /** @var array<int, string> $aSecciones */
        $aSecciones = [1 => $dele, 2 => $delef];

        $aviso_tipo = AvisoTipoId::TIPO_MAIL;
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $aWhere = [
            '_ordre' => 'id_usuario,id_item_cambio',
            'aviso_tipo' => $aviso_tipo,
            'avisado' => 'false',
            'sfsv' => $mi_sfsv,
        ];
        $cCambiosUsuario = $this->cambioUsuarioRepository->getCambiosUsuario($aWhere);

        $encolados = 0;
        $sinEmail = 0;
        $i = 0;
        $id_usuario_anterior = 0;
        $email = '';
        /** @var array<int, array{1: string, 2: string, 3: string}> $a_datos */
        $a_datos = [];
        /** @var array<int, string> $a_id */
        $a_id = [];
        $DateTimeZone = new DateTimeZone('UTC');
        foreach ($cCambiosUsuario as $oCambioUsuario) {
            $id_usuario = $oCambioUsuario->getId_usuario();

            if ($id_usuario !== $id_usuario_anterior) {
                // Flush del usuario anterior (excepto en la primera iteracion).
                if ($id_usuario_anterior !== 0) {
                    if ($this->encolarMail($email, $a_datos, $a_id)) {
                        $encolados++;
                    } else {
                        $sinEmail++;
                    }
                    $a_datos = [];
                    $a_id = [];
                }
                $oMiUsuario = $this->usuarioRepository->findById($id_usuario);
                $email = $oMiUsuario?->getEmailAsString() ?? '';
                $id_usuario_anterior = $id_usuario;

                $oPreferencia = $this->preferenciaRepository->findById($id_usuario, 'zona_horaria');
                $zona_horaria = ($oPreferencia !== null) ? (string) $oPreferencia->getPreferencia() : '';
                if ($zona_horaria !== '') {
                    try {
                        $DateTimeZone = new DateTimeZone($zona_horaria);
                    } catch (DateInvalidTimeZoneException) {
                        $DateTimeZone = new DateTimeZone('UTC');
                    }
                } else {
                    $DateTimeZone = new DateTimeZone('UTC');
                }
            }
            if ($email === '') {
                continue;
            }

            $id_item_cmb = $oCambioUsuario->getId_item_cambio();
            $id_schema_cmb = $oCambioUsuario->getId_schema_cambio();
            $oCambioRow = $this->cambioParaAvisoLookup->find($id_schema_cmb, $id_item_cmb);
            if ($oCambioRow === null) {
                continue;
            }
            $quien_cambia = $oCambioRow->getQuien_cambia();
            $sfsv_quien_cambia = $oCambioRow->getSfsv_quien_cambia();
            $oTimestamp_cambio_GMT = $oCambioRow->getTimestamp_cambio();
            if (!$oTimestamp_cambio_GMT instanceof DateTimeLocal) {
                continue;
            }
            $timestamp_cambio = (clone $oTimestamp_cambio_GMT)->setTimezone($DateTimeZone)->getFromLocalHora();

            $aviso_txt = $this->cambioAvisoTxtBuilder->build($oCambioRow);
            if ($aviso_txt === false) {
                continue;
            }
            $i++;

            // Quien cambia
            if ($id_schema_cmb === 3000) {
                $quien = $oCambioRow->getDl_org() ?? '';
            } elseif ($sfsv_quien_cambia === $mi_sfsv && $quien_cambia !== null) {
                $oUsuarioCmb = $this->usuarioRepository->findById($quien_cambia);
                $quien = $oUsuarioCmb?->getUsuarioAsString() ?? '';
            } else {
                $quien = $aSecciones[$sfsv_quien_cambia ?? 0] ?? '';
            }

            $a_datos[$i] = [
                1 => $timestamp_cambio,
                2 => $quien,
                3 => $aviso_txt,
            ];
            $a_id[$i] = "$id_item_cmb,$id_usuario,$mi_sfsv,$aviso_tipo";
        }
        // El ultimo de la lista.
        if ($email !== '') {
            if ($this->encolarMail($email, $a_datos, $a_id)) {
                $encolados++;
            } else {
                $sinEmail++;
            }
        }

        return [
            'encolados' => $encolados,
            'usuarios_sin_email' => $sinEmail,
            'total_avisos' => $i,
        ];
    }

    /**
     * @param array<int, array{1: string, 2: string, 3: string}> $a_datos filas para la tabla del mail.
     * @param array<int, string> $a_id identificadores para borrar tras encolar.
     */
    private function encolarMail(string $email, array $a_datos, array $a_id): bool
    {
        if ($a_datos === [] || $email === '') {
            return false;
        }

        $a_cabeceras = [
            ucfirst(_("fecha cambio")),
            ucfirst(_("quien")),
            ucfirst(_("cambio")),
        ];
        $oTabla = new Lista();
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setDatos($a_datos);

        $asunto = _("Avisos de cambios en actividades");
        $cuerpo = '<html><head><title>' . _("Tabla de cambios en actividades") . '</title></head><body>';
        $cuerpo .= $oTabla->lista();
        $cuerpo .= '</body></html>';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: Aquinate <no-Reply@moneders.net>\r\n";
        $headers .= "Reply-To: no-Reply@moneders.net\r\n";
        $headers .= "Return-path: no-Reply@moneders.net\r\n";

        $oColaMail = new ColaMail();
        $oColaMail->setUuid_item(ColaMailId::random());
        $oColaMail->setMail_to($email);
        $oColaMail->setSubject((string) $asunto);
        $oColaMail->setMessage($cuerpo);
        $oColaMail->setHeaders($headers);
        $oColaMail->setWrited_by(self::WRITED_BY);

        if ($this->colaMailRepository->Guardar($oColaMail) === false) {
            return false;
        }

        $this->eliminarEnviado($a_id);
        return true;
    }

    /**
     * @param array<int, string> $a_id identificadores serializados
     *   ("id_item_cambio,id_usuario,sfsv,aviso_tipo").
     * @return list<string> mensajes de error acumulados, vacio si todo OK.
     */
    private function eliminarEnviado(array $a_id): array
    {
        $errores = [];
        foreach ($a_id as $id) {
            $ids = explode(',', $id);
            if (count($ids) < 4) {
                continue;
            }
            $aWhere = [
                'id_item_cambio' => (int) $ids[0],
                'id_usuario' => (int) $ids[1],
                'sfsv' => (int) $ids[2],
                'aviso_tipo' => (int) $ids[3],
            ];

            $cCambiosUsuario = $this->cambioUsuarioRepository->getCambiosUsuario($aWhere);
            foreach ($cCambiosUsuario as $oCambioUsuario) {
                if ($this->cambioUsuarioRepository->Eliminar($oCambioUsuario) === false) {
                    $errores[] = _("Hay un error, no se ha eliminado")
                        . ' (' . $this->cambioUsuarioRepository->getErrorTxt() . ')';
                }
            }
        }
        return $errores;
    }
}
