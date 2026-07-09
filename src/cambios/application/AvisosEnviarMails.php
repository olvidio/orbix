<?php

namespace src\cambios\application;

use DateInvalidTimeZoneException;
use DateTimeZone;
use frontend\shared\web\Lista;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Caso de uso para enviar por e-mail los avisos pendientes de cada usuario.
 *
 * Llamado desde el driver CLI
 * `src/cambios/infrastructure/cli/avisos_generar_mails.php` (se ejecuta por
 * crontab desde el servidor exterior, que es el que tiene acceso al MTA).
 *
 * El use case:
 *   1. Dispara `Cambio::generarTabla()` para asegurar que no quedan cambios
 *      pendientes de anotar en otras dl, espera 60s.
 *   2. Itera los `CambioUsuario` con `aviso_tipo = TIPO_MAIL` no avisados.
 *   3. Para cada usuario construye una tabla HTML con sus cambios y envia
 *      un unico mail. Elimina los `CambioUsuario` despues de enviar.
 *
 * Deuda conocida (fuera de scope de este refactor):
 *   - Sigue usando `mail()` directamente en vez de un `MailerInterface`.
 *   - La construccion del HTML del body usa `frontend\shared\web\Lista` (componente UI);
 *     deberia moverse a una vista/template.
 *   - `sleep(60)` bloquea el proceso en tests; aceptable porque la unica
 *     via de invocacion es cron.
 */
class AvisosEnviarMails
{
    public function __construct(
        private CambioUsuarioRepositoryInterface $cambioUsuarioRepository,
        private UsuarioRepositoryInterface       $usuarioRepository,
        private PreferenciaRepositoryInterface   $preferenciaRepository,
        private CambioParaAvisoLookup            $cambioParaAvisoLookup,
        private CambioAvisoTxtBuilder            $cambioAvisoTxtBuilder,
    )
    {
    }

    /**
     * @return array{enviados: int, usuarios_sin_email: int, total_avisos: int}
     */
    public function execute(): array
    {
        // Para asegurar que coge los cambios de otras dl que no tengan instalado
        // el modulo de cambios, hay que ejecutar el generarTabla() y esperar a
        // que acabe en background.
        $oCambio = new Cambio();
        $oCambio->generarTabla();
        sleep(60);

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

        $enviados = 0;
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
                    if ($this->enviarMail($email, $a_datos, $a_id)) {
                        $enviados++;
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
                $zona_horaria = ($oPreferencia !== null) ? (string)$oPreferencia->getPreferencia() : '';
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
            if ($this->enviarMail($email, $a_datos, $a_id)) {
                $enviados++;
            } else {
                $sinEmail++;
            }
        }

        return [
            'enviados' => $enviados,
            'usuarios_sin_email' => $sinEmail,
            'total_avisos' => $i,
        ];
    }

    /**
     * @param array<int, array{1: string, 2: string, 3: string}> $a_datos filas para la tabla del mail.
     * @param array<int, string> $a_id identificadores para borrar tras envio.
     */
    private function enviarMail(string $email, array $a_datos, array $a_id): bool
    {
        if ($a_datos === [] || $email === '') {
            $this->eliminarEnviado($a_id);
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

        mail($email, $asunto, $cuerpo, $headers);
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
                'id_item_cambio' => (int)$ids[0],
                'id_usuario' => (int)$ids[1],
                'sfsv' => (int)$ids[2],
                'aviso_tipo' => (int)$ids[3],
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
