<?php

namespace src\personas\application;

use src\shared\config\ConfigGlobal;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\Traslado;
use src\personas\domain\Trasladar;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroRepositoryInterface;

/**
 * Aplica el traslado de centro y/o delegacion de una persona y asegura
 * que existe (y queda abierto) el dossier de traslados (tipo 1004).
 *
 * Migrado desde `apps/personas/controller/traslado_update.php` (slice 5 de
 * la migracion del modulo `personas`).
 */
final class TrasladoUpdate
{
    /**
     * @param array<string,mixed> $input tipicamente `$_POST`.
     * @return string cadena vacia si ok, mensaje de error si falla.
     */
    public static function execute(array $input): string
    {
        $id_pau = (int)($input['id_pau'] ?? 0);
        $obj_pau = (string)($input['obj_pau'] ?? '');
        if (empty($id_pau) || empty($obj_pau)) {
            return _("Faltan id_pau u obj_pau");
        }

        $resolver = new PersonaRepositoryResolver();
        try {
            $repoPersona = $resolver->repositorio($obj_pau);
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $oPersona = $repoPersona->findById($id_pau);
        if ($oPersona === null) {
            return _("No se encuentra la persona");
        }

        $error = '';

        // Cambio de centro.
        $new_ctr = (string)($input['new_ctr'] ?? '');
        $f_ctr = (string)($input['f_ctr'] ?? '');
        if (!empty($new_ctr) && !empty($f_ctr)) {
            $oF_ctr = DateTimeLocal::createFromLocal($f_ctr);
            $id_ctr_o = (string)($input['id_ctr_o'] ?? '');
            $ctr_o = (string)($input['ctr_o'] ?? '');

            $id_new_ctr = (int)strtok($new_ctr, "#");
            $oCentro = $GLOBALS['container']->get(CentroRepositoryInterface::class)->findById($id_new_ctr);
            $nom_new_ctr = $oCentro?->getNombre_ubi() ?? '';

            $oPersona->setId_ctr($id_new_ctr);
            if ($repoPersona->Guardar($oPersona) === false) {
                $error .= "\n" . _("hay un error, no se ha guardado");
            }

            $TrasladoRepository = $GLOBALS['container']->get(TrasladoRepositoryInterface::class);
            $oTraslado = new Traslado();
            $oTraslado->setId_item($TrasladoRepository->getNewId());
            $oTraslado->setId_nom($id_pau);
            $oTraslado->setF_traslado($oF_ctr);
            $oTraslado->setTipo_cmb('sede');
            $oTraslado->setId_ctr_origen($id_ctr_o);
            $oTraslado->setCtr_origen($ctr_o);
            $oTraslado->setId_ctr_destino($id_new_ctr);
            $oTraslado->setCtr_destino($nom_new_ctr);
            if ($TrasladoRepository->Guardar($oTraslado) === false) {
                $error .= "\n" . _("hay un error, no se ha guardado");
            }
        }

        // Cambio de delegacion.
        $new_dl = (string)($input['new_dl'] ?? '');
        $f_dl = (string)($input['f_dl'] ?? '');
        if (!empty($new_dl) && !empty($f_dl)) {
            $old_dl = $oPersona->getDl();
            $situacion = SituacionCode::fromNullableString((string)($input['situacion'] ?? ''));
            $dl_form = (string)($input['dl'] ?? '');
            $reg_dl_org = empty($dl_form) ? '' : ConfigGlobal::mi_region() . '-' . $dl_form;
            $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';
            $reg_dl_org .= $sfsv_txt;
            $new_dl_sfsv = $new_dl . $sfsv_txt;

            $oTrasladoDl = new Trasladar();
            $oTrasladoDl->setId_nom($id_pau);
            $oTrasladoDl->setDl_persona($old_dl);
            $oTrasladoDl->setReg_dl_org($reg_dl_org);
            $oTrasladoDl->setReg_dl_dst($new_dl_sfsv);
            $oTrasladoDl->setF_traslado(DateTimeLocal::createFromLocal($f_dl));
            $oTrasladoDl->setSituacionVo($situacion);
            $oTrasladoDl->trasladar();
            $err_dl = $oTrasladoDl->getError();
            if (!empty($err_dl)) {
                $error .= "\n" . $err_dl;
            }
        }

        // Abrir (o crear) el dossier de traslados (tipo 1004).
        $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $pk = DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $id_pau, 'id_tipo_dossier' => 1004]);
        $oDossier = $DosierRepository->findByPk($pk);
        if ($oDossier === null) {
            $oDossier = $DosierRepository->crearDossier($pk);
        }
        $oDossier->abrir();
        $DosierRepository->Guardar($oDossier);

        return ltrim($error, "\n");
    }
}
