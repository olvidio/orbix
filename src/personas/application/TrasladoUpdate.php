<?php

namespace src\personas\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\shared\config\ConfigGlobal;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaNax;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSSSC;
use src\personas\domain\entity\Traslado;
use src\personas\domain\Trasladar;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroRepositoryInterface;

/**
 * Aplica el traslado de centro y/o delegacion de una persona y asegura
 * que existe (y queda abierto) el dossier de traslados (tipo 1004).
 */
final class TrasladoUpdate
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private CentroRepositoryInterface $centroRepository,
        private TrasladoRepositoryInterface $trasladoRepository,
        private DossierRepositoryInterface $dossierRepository,
        private Trasladar $trasladar,
    ) {
    }

    /**
     * @param array<string,mixed> $input tipicamente `$_POST`.
     * @return string cadena vacia si ok, mensaje de error si falla.
     */
    public function execute(array $input): string
    {
        $id_pau = input_int($input, 'id_pau');
        $obj_pau = input_string($input, 'obj_pau');
        if (empty($id_pau) || empty($obj_pau)) {
            return _("Faltan id_pau u obj_pau");
        }

        try {
            $repoPersona = match ($obj_pau) {
                'PersonaN' => $this->personaRepositoryResolver->repositorio('PersonaN'),
                'PersonaAgd' => $this->personaRepositoryResolver->repositorio('PersonaAgd'),
                'PersonaNax' => $this->personaRepositoryResolver->repositorio('PersonaNax'),
                'PersonaS' => $this->personaRepositoryResolver->repositorio('PersonaS'),
                'PersonaSSSC' => $this->personaRepositoryResolver->repositorio('PersonaSSSC'),
                'PersonaEx' => $this->personaRepositoryResolver->repositorio('PersonaEx'),
                default => throw new \InvalidArgumentException("obj_pau '$obj_pau' no reconocido"),
            };
        } catch (\InvalidArgumentException) {
            return _("No existe la clase de la persona");
        }

        $oPersona = $repoPersona->findById($id_pau);
        if ($oPersona === null) {
            return _("No se encuentra la persona");
        }

        $error = '';

        // Cambio de centro.
        $new_ctr = input_string($input, 'new_ctr');
        $f_ctr = input_string($input, 'f_ctr');
        if (!empty($new_ctr) && !empty($f_ctr)) {
            $oF_ctr = DateTimeLocal::createFromLocal($f_ctr);
            $id_ctr_o = input_string($input, 'id_ctr_o');
            $ctr_o = input_string($input, 'ctr_o');

            $id_new_ctr = (int)strtok($new_ctr, "#");
            $oCentro = $this->centroRepository->findById($id_new_ctr);
            $nom_new_ctr = $oCentro?->getNombre_ubi() ?? '';

            if ($oPersona instanceof PersonaDl) {
                $oPersona->setId_ctr($id_new_ctr);
                if ($this->guardarPersona($repoPersona, $obj_pau, $oPersona) === false) {
                    $error .= "\n" . _("hay un error, no se ha guardado");
                }
            }

            $oTraslado = new Traslado();
            $oTraslado->setId_item($this->trasladoRepository->getNewId());
            $oTraslado->setId_nom($id_pau);
            $oTraslado->setF_traslado($oF_ctr);
            $oTraslado->setTipo_cmb('sede');
            $oTraslado->setId_ctr_origen($id_ctr_o === '' ? null : (int)$id_ctr_o);
            $oTraslado->setCtr_origen($ctr_o);
            $oTraslado->setId_ctr_destino($id_new_ctr);
            $oTraslado->setCtr_destino($nom_new_ctr);
            if ($this->trasladoRepository->Guardar($oTraslado) === false) {
                $error .= "\n" . _("hay un error, no se ha guardado");
            }
        }

        // Cambio de delegacion.
        $new_dl = input_string($input, 'new_dl');
        $f_dl = input_string($input, 'f_dl');
        if (!empty($new_dl) && !empty($f_dl)) {
            $old_dl = (string)($oPersona->getDl() ?? '');
            $situacion = SituacionCode::fromNullableString(input_string($input, 'situacion'));
            if ($situacion === null) {
                return _("Falta una situación válida");
            }
            $dl_form = input_string($input, 'dl');
            $reg_dl_org = empty($dl_form) ? '' : ConfigGlobal::mi_region() . '-' . $dl_form;
            $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';
            $reg_dl_org .= $sfsv_txt;
            $new_dl_sfsv = $new_dl . $sfsv_txt;

            $oTrasladoDl = $this->trasladar;
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
        $pk = DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $id_pau, 'id_tipo_dossier' => 1004]);
        $oDossier = $this->dossierRepository->findByPk($pk);
        if ($oDossier === null) {
            $oDossier = $this->dossierRepository->crearDossier($pk);
        }
        $oDossier->abrir();
        $this->dossierRepository->Guardar($oDossier);

        return ltrim($error, "\n");
    }

    /**
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface $repo
     */
    private function guardarPersona(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface $repo,
        string $obj_pau,
        PersonaDl|PersonaEx $persona,
    ): bool {
        return match ($obj_pau) {
            'PersonaN' => $repo instanceof PersonaNRepositoryInterface && $persona instanceof PersonaN
                ? $repo->Guardar($persona) : false,
            'PersonaAgd' => $repo instanceof PersonaAgdRepositoryInterface && $persona instanceof PersonaAgd
                ? $repo->Guardar($persona) : false,
            'PersonaNax' => $repo instanceof PersonaNaxRepositoryInterface && $persona instanceof PersonaNax
                ? $repo->Guardar($persona) : false,
            'PersonaS' => $repo instanceof PersonaSRepositoryInterface && $persona instanceof PersonaS
                ? $repo->Guardar($persona) : false,
            'PersonaSSSC' => $repo instanceof PersonaSSSCRepositoryInterface && $persona instanceof PersonaSSSC
                ? $repo->Guardar($persona) : false,
            'PersonaEx' => $repo instanceof PersonaExRepositoryInterface && $persona instanceof PersonaEx
                ? $repo->Guardar($persona) : false,
            default => false,
        };
    }
}
