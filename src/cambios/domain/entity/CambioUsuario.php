<?php

namespace src\cambios\domain\entity;

use src\cambios\domain\value_objects\AvisoTipoId;
use src\shared\domain\traits\Hydratable;
use function core\is_true;

class CambioUsuario
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_schema_cambio;

    private int $id_item_cambio;

    private int $id_usuario;

    private int $sfsv;

    private AvisoTipoId $aviso_tipo;

    private bool|null $avisado = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_schema_cambio(): int
    {
        return $this->id_schema_cambio;
    }


    public function setId_schema_cambio(int $id_schema_cambio): void
    {
        $this->id_schema_cambio = $id_schema_cambio;
    }


    public function getId_item_cambio(): int
    {
        return $this->id_item_cambio;
    }


    public function setId_item_cambio(int $id_item_cambio): void
    {
        $this->id_item_cambio = $id_item_cambio;
    }


    public function getId_usuario(): int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }


    public function getSfsv(): int
    {
        return $this->sfsv;
    }


    public function setSfsv(int $sfsv): void
    {
        $this->sfsv = $sfsv;
    }

    /**
     * @return AvisoTipoId
     */
    public function getAvisoTipoVo(): AvisoTipoId
    {
        return $this->aviso_tipo;
    }

    /**
     * @param AvisoTipoId $avisoTipoId
     */
    public function setAvisoTipoVo(AvisoTipoId $avisoTipoId): void
    {
        $this->aviso_tipo = $avisoTipoId;
    }

    /**
     * @deprecated Usar `getAvisoTipoVo(): AvisoTipoId` en su lugar.
     */
    public function getAviso_tipo(): int
    {
        return $this->aviso_tipo->value();
    }

    /**
     * @deprecated Usar `setAvisoTipoVo(AvisoTipoId $vo): void` en su lugar.
     */
    public function setAviso_tipo(int $aviso_tipo): void
    {
        $this->aviso_tipo = new AvisoTipoId($aviso_tipo);
    }


    public function isAvisado(): ?bool
    {
        return $this->avisado;
    }


    public function setAvisado(?bool $avisado = null): void
    {
        $this->avisado = $avisado;
    }
}