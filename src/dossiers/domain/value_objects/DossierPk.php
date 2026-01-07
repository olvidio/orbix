<?php

namespace src\dossiers\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class DossierPk
{
    public function __construct(
        private int $idTipoDossier,
        private int $idPau,
        private string $tabla
    ) {
        if ($idTipoDossier <= 0) {
            throw new \InvalidArgumentException('id_tipo_dossier debe ser > 0');
        }
        // puede ser negativo para los ex
        if (!is_numeric($idPau)) {
            throw new \InvalidArgumentException('id_pau debe ser > 0');
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

    public static function fromArray(array $pk): self
    {
        return new self((int)$pk['id_tipo_dossier'], (int)$pk['id_pau'], (string)$pk['tabla']);
    }

    public function idTipoDossier(): int { return $this->idTipoDossier; }
    public function idPau(): int { return $this->idPau; }
    public function tabla(): string { return $this->tabla; }

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
