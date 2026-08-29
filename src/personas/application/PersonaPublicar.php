<?php

declare(strict_types=1);

namespace src\personas\application;

use src\personas\application\services\SincronizarCpSacd;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;

/**
 * Publica una persona para una o varias DL destino (caso B, con TTL por defecto).
 */
final class PersonaPublicar
{
    public function __construct(
        private PersonaAllRepositoryInterface $personaAllRepository,
        private SincronizarCpSacd $sincronizarCpSacd,
    ) {
    }

    /**
     * @param list<string>|string $dls
     * @return string vacío si OK; mensaje de error si no
     */
    public function execute(int $id_nom, int $id_schema, array|string $dls): string
    {
        if ($id_nom <= 0 || $id_schema < 1) {
            return _('Datos de persona no válidos');
        }

        $lista = is_array($dls) ? $dls : [$dls];
        $lista = array_values(array_filter(array_map(
            static fn(mixed $v): string => is_scalar($v) ? trim((string) $v) : '',
            $lista,
        ), static fn(string $v): bool => $v !== ''));

        if ($lista === []) {
            return _('Debe indicar al menos una delegación destino');
        }

        $propia = \src\shared\config\ConfigGlobal::mi_dele();
        $hasta = PersonaPublicacion::fechaHastaDefault();
        foreach ($lista as $dlRaw) {
            $dl = PersonaPublicacion::normalizarDl($dlRaw);
            if ($dl === PersonaPublicacion::DL_TODAS) {
                if (!$this->personaAllRepository->marcarPublicadoPara($id_nom, $id_schema, $dl, null)) {
                    return _('No se ha podido publicar la persona');
                }
                continue;
            }
            if ($propia !== '' && $dl === $propia) {
                return _('No se puede publicar hacia la propia delegación');
            }
            if (!$this->personaAllRepository->marcarPublicadoPara($id_nom, $id_schema, $dl, $hasta)) {
                return _('No se ha podido publicar la persona');
            }
        }

        $this->propagarACpSacd($id_nom);

        return '';
    }

    /**
     * `publicado_para` se escribe con un UPDATE directo sobre `global.personas`,
     * sin pasar por el `Guardar()` de ningún repositorio, así que la copia
     * `cp_sacd` no se entera. Se propaga aquí; si la persona no está en la copia
     * (no es sacd) el UPDATE no toca ninguna fila.
     */
    private function propagarACpSacd(int $id_nom): void
    {
        try {
            $persona = $this->personaAllRepository->getPersonaByIdNom($id_nom);
            if ($persona === null) {
                return;
            }
            $json = PersonaPublicacion::toDatabaseValue($persona->getPublicado_para());
            $this->sincronizarCpSacd->sincronizarPublicadoPara($id_nom, $json);
        } catch (\Throwable) {
            // La publicación ya está hecha; la reconciliación periódica arregla la copia.
        }
    }
}
