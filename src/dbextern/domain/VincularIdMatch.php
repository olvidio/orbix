<?php

namespace src\dbextern\domain;

use RuntimeException;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\IdMatchPersona;

/**
 * Enlaza BDU (`id_listas`) con Aquinate (`id_orbix`) respetando la unicidad de ambos.
 *
 * Si `id_orbix` ya está unido a un `id_listas` que ya no existe en la BDU, reasigna el match.
 */
class VincularIdMatch
{
    public function __construct(
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private PersonaBDURepositoryInterface $personaBDURepository,
    ) {
    }

    /**
     * @return string Texto de error (vacío si se ha vinculado)
     */
    public function vincular(int $id_listas, int $id_orbix, string $tipo_persona): string
    {
        $matchPorListas = $this->primerMatch(['id_listas' => $id_listas]);
        $matchPorOrbix = $this->primerMatch(['id_orbix' => $id_orbix]);

        if ($matchPorListas !== null && $matchPorListas->getId_orbix() === $id_orbix) {
            return '';
        }
        if ($matchPorOrbix !== null && $matchPorOrbix->getId_listas() === $id_listas) {
            return '';
        }

        if ($matchPorOrbix !== null) {
            $idListasViejo = $matchPorOrbix->getId_listas();
            if ($this->esMatchVigente($matchPorOrbix)) {
                return sprintf(
                    _("esta persona de aquinate ya está unida a la BDU (id=%s)"),
                    $idListasViejo
                );
            }
            $errorEliminar = $this->eliminar($matchPorOrbix);
            if ($errorEliminar !== '') {
                return $errorEliminar;
            }
        }

        if ($matchPorListas !== null) {
            $matchPorListas->setId_orbix($id_orbix);
            $matchPorListas->setId_tabla($tipo_persona);

            return $this->guardar($matchPorListas);
        }

        $nuevo = new IdMatchPersona();
        $nuevo->setId_listas($id_listas);
        $nuevo->setId_orbix($id_orbix);
        $nuevo->setId_tabla($tipo_persona);

        return $this->guardar($nuevo);
    }

    public function esMatchVigente(IdMatchPersona $match): bool
    {
        $personaBDU = $this->personaBDURepository->findById($match->getId_listas());

        return $personaBDU !== null && $personaBDU->getApenom() !== '';
    }

    /**
     * @param array<string, mixed> $where
     */
    private function primerMatch(array $where): ?IdMatchPersona
    {
        $matches = $this->idMatchRepository->getIdMatchPersonas($where);

        return $matches[0] ?? null;
    }

    private function guardar(IdMatchPersona $match): string
    {
        try {
            if ($this->idMatchRepository->Guardar($match) === false) {
                return $this->errorGuardar($this->idMatchRepository->getErrorTxt());
            }
        } catch (RuntimeException $e) {
            return $this->errorGuardar($e->getMessage());
        }

        return '';
    }

    private function eliminar(IdMatchPersona $match): string
    {
        try {
            if ($this->idMatchRepository->Eliminar($match) === false) {
                return _("hay un error, no se ha eliminado") . "\n" . $this->idMatchRepository->getErrorTxt();
            }
        } catch (RuntimeException $e) {
            return _("hay un error, no se ha eliminado") . "\n" . $e->getMessage();
        }

        return '';
    }

    private function errorGuardar(string $detalle): string
    {
        $msg = _("hay un error, no se ha guardado");
        if ($detalle !== '') {
            $msg .= "\n" . $detalle;
        }

        return $msg;
    }
}
