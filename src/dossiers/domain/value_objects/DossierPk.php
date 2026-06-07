<?php

namespace src\dossiers\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class DossierPk
{
    public function __construct(
        private int    $idTipoDossier,
        private int    $idPau,
        private string $tabla
    )
    {
        if ($idTipoDossier <= 0) {
            throw new \InvalidArgumentException('id_tipo_dossier debe ser > 0');
        }
        $tabla = trim($tabla);
        if ($tabla === '') {
            throw new \InvalidArgumentException('tabla no puede ser vacía');
        }
        if (!preg_match('/^[a-z]$/i', $tabla)) {
            // En el dominio actual la tabla se usa como código 1-char (p,a,...) en d_dossiers_abiertos
            // Permitimos solo una letra para evitar valores inesperados.
            throw new \InvalidArgumentException('tabla debe ser una sola letra');
        }
    }

    /**
     * @param array<string, mixed> $pk
     */
    public static function fromArray(array $pk): self
    {
        $tablaRaw = $pk['tabla'] ?? '';
        $tabla = $tablaRaw instanceof DossierTabla
            ? $tablaRaw->value()
            : (is_scalar($tablaRaw) ? (string) $tablaRaw : '');

        $idTipo = $pk['id_tipo_dossier'] ?? 0;
        $idPau = $pk['id_pau'] ?? 0;

        return new self(
            is_numeric($idTipo) ? (int) $idTipo : 0,
            is_numeric($idPau) ? (int) $idPau : 0,
            $tabla,
        );
    }

    public function idTipoDossier(): int
    {
        return $this->idTipoDossier;
    }

    public function idPau(): int
    {
        return $this->idPau;
    }

    public function tabla(): string
    {
        return $this->tabla;
    }

    public function equals(self $other): bool
    {
        return $this->idTipoDossier === $other->idTipoDossier()
            && $this->idPau === $other->idPau()
            && $this->tabla === $other->tabla();
    }

    public function __toString(): string
    {
        // Representación compacta util para logs o claves cache
        return $this->tabla . ':' . $this->idPau . ':' . $this->idTipoDossier;
    }
}
