<?php

namespace src\notas\application;

use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaDlRepository;
use src\shared\config\ConfigGlobal;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Copia todas las `PersonaNota` de una persona origen hacia una persona
 * destino. Utilizado por `personas_select.phtml` (pagina de traslado
 * de tessera entre numerarios / supernumerarios).
 *
 * Lectura vía padre `e_notas` (expediente agregado). Escritura en
 * `e_notas_dl` del esquema de cada nota (`id_schema`), no en el padre:
 * el trigger `e_notas_insert_trigger` exige `id_schema` y fallaría con
 * EXECUTE null.
 *
 * Devuelve una cadena con los errores (separados por `<br>`) o vacia
 * si todo ha ido bien.
 */
final class TesseraCopiar
{

    public function __construct(
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly PersonaNotaDlRepositoryInterface $personaNotaDlRepository,
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_nom_org = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom_org');
        $id_nom_dst = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom_dst');

        if ($id_nom_org === 0 || $id_nom_dst === 0) {
            return _("No se han recibido las personas de origen y destino");
        }

        $cPersonaOrgNotas = $this->personaNotaRepository->getPersonaNotas(['id_nom' => $id_nom_org]);

        $error = '';
        foreach ($cPersonaOrgNotas as $oPersonaNota) {
            if (!$oPersonaNota instanceof PersonaNota) {
                continue;
            }
            $oNueva = clone $oPersonaNota;
            $oNueva->setId_nom($id_nom_dst);
            $repo = $this->repoEscrituraParaNota($oPersonaNota);
            if ($repo === null) {
                $error .= '<br>' . sprintf(
                    _("no se ha guardado la nota (esquema no escribible para id_nivel %s)"),
                    (string) $oPersonaNota->getId_nivel()
                );
                continue;
            }
            if ($repo->Guardar($oNueva) === false) {
                $error .= '<br>' . _("no se ha guardado la nota");
            }
        }

        return $error;
    }

    /**
     * Destino de escritura: `e_notas_dl` del esquema de la nota origen
     * (modelo acta). Fallback a la DL de sesión si falta `id_schema`.
     * `restov`/`restof` no tienen adaptador de escritura → null.
     */
    private function repoEscrituraParaNota(PersonaNota $nota): ?PersonaNotaDlRepositoryInterface
    {
        $idSchema = $nota->getId_schema();
        if ($idSchema <= 0) {
            return $this->personaNotaDlRepository;
        }

        $cSchemas = $this->dbSchemaRepository->getDbSchemas(['id' => $idSchema]);
        if ($cSchemas === []) {
            return $this->personaNotaDlRepository;
        }

        $esquema = $cSchemas[0]->getSchema();
        if ($esquema === 'restov' || $esquema === 'restof') {
            return null;
        }

        if ($esquema === ConfigGlobal::mi_region_dl()) {
            return $this->personaNotaDlRepository;
        }

        try {
            return new PgPersonaNotaDlRepository($esquema);
        } catch (\Throwable) {
            return null;
        }
    }
}
