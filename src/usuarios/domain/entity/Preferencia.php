<?php

namespace src\usuarios\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;

class Preferencia
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private TipoPreferencia $tipo;

    private ?ValorPreferencia $preferencia = null;

    private int $id_usuario;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getTipoVo(): TipoPreferencia
    {
        return $this->tipo;
    }

    public function setTipoVo(TipoPreferencia|string $tipo): void
    {
        $this->tipo = $tipo instanceof TipoPreferencia
            ? $tipo
            : new TipoPreferencia($tipo);
    }


    public function getTipoAsString(): string
    {
        return $this->tipo->value();
    }


    public function getPreferenciaVo(): ?ValorPreferencia
    {
        return $this->preferencia;
    }


    public function setPreferenciaVo(ValorPreferencia|string|null $preferencia = null): void
    {
        if ($preferencia === null) {
            $this->preferencia = null;
            return;
        }

        $this->preferencia = $preferencia instanceof ValorPreferencia
            ? $preferencia
            : new ValorPreferencia($preferencia);
    }


    public function getPreferencia(): ?string
    {
        return $this->getPreferenciaAsString();
    }

    public function getPreferenciaAsString(): ?string
    {
        return $this->preferencia ? $this->preferencia->value() : null;
    }


    public function getId_usuario(): int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }
}
