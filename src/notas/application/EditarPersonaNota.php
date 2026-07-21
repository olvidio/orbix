<?php

namespace src\notas\application;

use RuntimeException;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\DestinoNotaExterno;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\PersonaNotaPk;
use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Alta/edición/baja de notas de persona (modelo acta: docs/dev/notas_modelo_acta.md).
 *
 * La nota se escribe siempre en `e_notas_dl` de la DL examinadora. Personas de paso
 * / resto (`DestinoNotaExterno`): misma regla; la comunicación hacia fuera es
 * certificado documental (módulo certificados / PDF), nunca placeholder en notas.
 */
class EditarPersonaNota
{
    private string $msg_err = '';
    private int $id_nom;
    private int $id_asignatura;
    private int $id_nivel;
    private ?int $tipo_acta;
    private PersonaNota $personaNota;

    public function __construct(
        PersonaNota $oPersonaNota,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly DossierRepositoryInterface $dossierRepository,
        private readonly PersonaNotaDlRepositoryInterface $personaNotaDlRepository,
    ) {
        $this->personaNota = $oPersonaNota;
        $this->id_nom = $oPersonaNota->getId_nom();
        $this->id_nivel = $oPersonaNota->getIdNivelVo()->value();
        $this->id_asignatura = $oPersonaNota->getId_asignatura();
        $this->tipo_acta = $oPersonaNota->getTipo_acta();
    }

    public function getMsgErr(): string
    {
        return $this->msg_err;
    }

    /**
     * True si el alumno es de paso / resto (entidad externa): el expediente
     * académico de la nota vive en la DL examinadora; hacia fuera solo PDF.
     */
    public function esDestinoExterno(): bool
    {
        return DestinoNotaExterno::esExterno($this->id_nom, $this->nombreSchemaPersona());
    }

    public function eliminar(): void
    {
        // Tabla padre e_notas: no hace falta saber la hija.
        if (!empty($this->id_nom) && !empty($this->id_asignatura) && !empty($this->id_nivel) && !empty($this->tipo_acta)) {
            $PersonaNotaDBRepository = $this->personaNotaRepository;
            $oPersonaNotaDB = $PersonaNotaDBRepository->findById($this->id_nom, $this->id_nivel, $this->tipo_acta ?? TipoActa::FORMATO_ACTA);
            if ($oPersonaNotaDB === null) {
                return;
            }
            if ($PersonaNotaDBRepository->Eliminar($oPersonaNotaDB) === false) {
                $errores = $_SESSION['errores'] ?? [];
                $last = is_array($errores) ? end($errores) : false;
                $err = is_string($last) ? $last : '';
                throw new RuntimeException(sprintf(_("No se ha eliminado la Nota: %s"), $err));
            }
        }
    }

    /**
     * @return array{nota_real?: PersonaNota|null, destino_externo: bool}
     */
    public function nuevo(): array
    {
        $a_ReposPersonaNota = $this->getReposPersonaNota((array) $this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->crear_nueva_personaNota_para_cada_objeto_del_array($a_ReposPersonaNota);
    }

    /**
     * @param array<string, mixed> $a_ReposPersonaNota debe contener `repo_real` (DL examinadora)
     * @param string $esquema_region_stgr conservado por compatibilidad con callers de traslado (ignorado)
     * @return array{nota_real?: PersonaNota|null, destino_externo: bool}
     */
    public function crear_nueva_personaNota_para_cada_objeto_del_array(array $a_ReposPersonaNota, string $esquema_region_stgr = ''): array
    {
        unset($esquema_region_stgr);

        $rta = ['destino_externo' => $this->esDestinoExterno()];

        $id_nom = $this->personaNota->getId_nom();
        $id_nivel = $this->personaNota->getIdNivelVo()->value();
        $id_asignatura = $this->personaNota->getIdAsignaturaVo()->value();
        $id_situacion = $this->personaNota->getIdSituacionVo()->value();
        $acta = $this->personaNota->getActa();
        $oF_acta = $this->personaNota->getF_acta();
        $tipo_acta = $this->personaNota->getTipoActaVo()?->value();
        $preceptor = $this->personaNota->isPreceptor();
        $id_preceptor = $this->personaNota->getId_preceptor();
        $detalle = $this->personaNota->getDetalle();
        $id_activ = $this->personaNota->getId_activ();
        $nota_num = $this->personaNota->getNotaNumVo()?->value();
        $nota_max = $this->personaNota->getNotaMaxVo()?->value();

        if (!array_key_exists('repo_real', $a_ReposPersonaNota)) {
            return $rta;
        }

        /** @var PersonaNotaDlRepositoryInterface $PersonaNotaRepository */
        $PersonaNotaRepository = $a_ReposPersonaNota['repo_real'];

        $cPersonaNota = $PersonaNotaRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_nivel' => $id_nivel]);
        if (!empty($cPersonaNota)) {
            $oPersonaNota2 = $cPersonaNota[0];
            $err = sprintf(
                _("Ya existe esta nota. id_nom: %s, id_asignatura: %s, acta: %s, tipo_acta: %s"),
                $oPersonaNota2->getId_nom(),
                $oPersonaNota2->getId_asignatura(),
                $oPersonaNota2->getActa(),
                $oPersonaNota2->getTipo_acta()
            );
            throw new RuntimeException($err);
        }

        $oPersonaNotaDB = new PersonaNota();
        $oPersonaNotaDB->setId_nom($id_nom);
        $oPersonaNotaDB->setId_nivel($id_nivel);
        $oPersonaNotaDB->setId_asignatura($id_asignatura);
        $oPersonaNotaDB->setId_situacion($id_situacion);
        $oPersonaNotaDB->setF_acta($oF_acta);
        $oPersonaNotaDB->setTipo_acta($tipo_acta);
        if (!empty($acta)) {
            if ($tipo_acta === TipoActa::FORMATO_CERTIFICADO) {
                $oPersonaNotaDB->setActa($acta);
            }
            if ($tipo_acta === TipoActa::FORMATO_ACTA) {
                $oPersonaNotaDB->setActa(Acta::inventarActa($acta, $oF_acta));
            }
        } else {
            $oPersonaNotaDB->setActa(null);
        }
        $oPersonaNotaDB->setPreceptor($preceptor);
        $oPersonaNotaDB->setId_preceptor($id_preceptor);
        $oPersonaNotaDB->setDetalle($detalle);
        $oPersonaNotaDB->setEpocaVo($this->personaNota->getEpocaVo());
        $oPersonaNotaDB->setId_activ($id_activ);
        $oPersonaNotaDB->setNota_num($nota_num);
        $oPersonaNotaDB->setNota_max($nota_max);
        $PersonaNotaRepository->Guardar($oPersonaNotaDB);

        $rta['nota_real'] = $PersonaNotaRepository->findByPk(PersonaNotaPk::fromArray([
            'id_nom' => $id_nom,
            'id_nivel' => $id_nivel,
            'tipo_acta' => $tipo_acta ?? TipoActa::FORMATO_ACTA,
        ]));

        // Persona de paso / resto: sin dossier abierto. En H-Hv / M-Mv no aplica.
        if ($id_nom > 0 && !$rta['destino_externo'] && ConfigGlobal::usaTablaDossiersAbierto()) {
            $DosierRepository = $this->dossierRepository;
            $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $id_nom, 'id_tipo_dossier' => 1303]));
            if ($oDossier === null) {
                $oDossier = $DosierRepository->crearDossier(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $id_nom, 'id_tipo_dossier' => 1303]));
            }
            $oDossier->abrir();
            $DosierRepository->Guardar($oDossier);
        }

        return $rta;
    }

    /**
     * @return array{nota_real?: PersonaNota|null, destino_externo: bool}
     */
    public function editar(int $id_asignatura_real): array
    {
        $a_ObjetosPersonaNota = $this->getReposPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->editar_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota, $id_asignatura_real);
    }

    /**
     * @param array<string, mixed> $a_ObjetosPersonaNota
     * @return array{nota_real?: PersonaNota|null, destino_externo: bool}
     */
    public function editar_personaNota_para_cada_objeto_del_array(array $a_ObjetosPersonaNota, int $id_asignatura_real): array
    {
        $rta = ['destino_externo' => $this->esDestinoExterno()];

        $id_nom = $this->personaNota->getId_nom();
        $id_nivel = $this->personaNota->getIdNivelVo()->value();
        $id_asignatura = $this->personaNota->getIdAsignaturaVo()->value();
        $id_situacion = $this->personaNota->getIdSituacionVo()->value();
        $acta = $this->personaNota->getActa();
        $oF_acta = $this->personaNota->getF_acta();
        $tipo_acta = $this->personaNota->getTipoActaVo()?->value();
        $preceptor = $this->personaNota->isPreceptor();
        $id_preceptor = $this->personaNota->getId_preceptor();
        $detalle = $this->personaNota->getDetalle();
        $id_activ = $this->personaNota->getIdActivVo()?->value();
        $nota_num = $this->personaNota->getNotaNumVo()?->value();
        $nota_max = $this->personaNota->getNotaMaxVo()?->value();

        if (!array_key_exists('repo_real', $a_ObjetosPersonaNota)) {
            return $rta;
        }

        /** @var PersonaNotaDlRepositoryInterface $PersonaNotaRepository */
        $PersonaNotaRepository = $a_ObjetosPersonaNota['repo_real'];

        $oPersonaNotaDB = new PersonaNota();
        $oPersonaNotaDB->setId_nom($id_nom);
        $oPersonaNotaDB->setId_nivel($id_nivel);
        if (!empty($id_asignatura_real)) {
            $oPersonaNotaDB->setId_asignatura($id_asignatura_real);
        }
        $oPersonaNotaDB->setId_asignatura($id_asignatura);
        $oPersonaNotaDB->setId_situacion($id_situacion);
        $oPersonaNotaDB->setF_acta($oF_acta);
        $oPersonaNotaDB->setTipo_acta($tipo_acta);
        if (!empty($acta)) {
            if ($tipo_acta === TipoActa::FORMATO_CERTIFICADO) {
                $oPersonaNotaDB->setActa($acta);
            }
            if ($tipo_acta === TipoActa::FORMATO_ACTA) {
                $oPersonaNotaDB->setActa(Acta::inventarActa($acta, $oF_acta));
            }
        } else {
            $oPersonaNotaDB->setActa(null);
        }

        if (empty($preceptor)) {
            $oPersonaNotaDB->setPreceptor(false);
            $oPersonaNotaDB->setId_preceptor(null);
        } else {
            $oPersonaNotaDB->setPreceptor($preceptor);
            $oPersonaNotaDB->setId_preceptor($id_preceptor);
        }
        $oPersonaNotaDB->setDetalle($detalle);
        $oPersonaNotaDB->setEpocaVo($this->personaNota->getEpocaVo());
        $oPersonaNotaDB->setId_activ($id_activ);
        $oPersonaNotaDB->setNota_num($nota_num);
        $oPersonaNotaDB->setNota_max($nota_max);

        $PersonaNotaRepository->Guardar($oPersonaNotaDB);

        $rta['nota_real'] = $PersonaNotaRepository->findByPk(PersonaNotaPk::fromArray([
            'id_nom' => $id_nom,
            'id_nivel' => $id_nivel,
            'tipo_acta' => $tipo_acta ?? TipoActa::FORMATO_ACTA,
        ]));

        return $rta;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDatosRegionStgr(string $dele = ''): array
    {
        $gesDelegacion = $this->delegacionRepository;
        try {
            $a_mi_region_stgr = $gesDelegacion->mi_region_stgr($dele);
        } catch (\RuntimeException $e) {
            $msg = _("Problema con los datos en la tabla de delegaciones");
            $msg .= "\r\n";
            $msg .= $e->getMessage();
            die($msg);
        }

        return $a_mi_region_stgr;
    }

    /**
     * Destino de escritura: siempre `e_notas_dl` de la DL examinadora.
     * Paso/resto incluidos: sin `otra_region` ni placeholders de certificado.
     *
     * @param array<string, mixed> $a_mi_region_stgr
     * @return array{repo_real: PersonaNotaDlRepositoryInterface}
     */
    public function getReposPersonaNota(array $a_mi_region_stgr, int $id_schema_persona): array
    {
        unset($a_mi_region_stgr, $id_schema_persona);

        return ['repo_real' => $this->personaNotaDlRepository];
    }

    private function getId_schema_persona(): int
    {
        $oPersona = Persona::findPersonaEnGlobal($this->id_nom);
        if ($oPersona === null) {
            // Persona de paso u otro caso sin ficha global: no bloquea la escritura en DL examinadora.
            return 0;
        }

        return $oPersona->getId_schema();
    }

    private function nombreSchemaPersona(): ?string
    {
        $id_schema = $this->getId_schema_persona();
        if ($id_schema <= 0) {
            return DestinoNotaExterno::esExternoPorIdNom($this->id_nom) ? 'restov' : null;
        }
        $cSchemas = $this->dbSchemaRepository->getDbSchemas(['id' => $id_schema]);
        if ($cSchemas === []) {
            return null;
        }

        return $cSchemas[0]->getSchema();
    }
}
