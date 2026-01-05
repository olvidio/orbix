<?php

namespace src\cambios\domain\entity;

use src\cambios\domain\value_objects\AvisoTipoId;
use src\cambios\domain\value_objects\ObjetoNombre;
use src\cambios\domain\value_objects\PauId;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\value_objects\DelegacionCode;
use function core\is_true;

class CambioUsuarioObjetoPref
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item_usuario_objeto;

    private int $id_usuario;

    private DelegacionCode $dl_org;

    private string $id_tipo_activ_txt;

    private int $id_fase_ref;

    private bool $aviso_off;

    private bool $aviso_on;

    private bool $aviso_outdate;

    private ObjetoNombre $objeto;

    private AvisoTipoId $aviso_tipo;

    private PauId|null $csv_id_pau = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item_usuario_objeto(): int
    {
        return $this->id_item_usuario_objeto;
    }


    public function setId_item_usuario_objeto(int $id_item_usuario_objeto): void
    {
        $this->id_item_usuario_objeto = $id_item_usuario_objeto;
    }


    public function getId_usuario(): int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }


    /**
     * @deprecated Usar `getDlOrgVo(): DelegacionCode` en su lugar.
     */
    public function getDl_org(): string
    {
        return $this->dl_org->value();
    }


    /**
     * @deprecated Usar `setDlOrgVo(DelegacionCode $vo): void` en su lugar.
     */
    public function setDl_org(string $dl_org): void
    {
        $this->dl_org = new DelegacionCode($dl_org);
    }

    public function getDlOrgVo(): DelegacionCode
    {
        return $this->dl_org;
    }

    public function setDlOrgVo(DelegacionCode $vo): void
    {
        $this->dl_org = $vo;
    }


    public function getId_tipo_activ_txt(): string
    {
        return $this->id_tipo_activ_txt;
    }


    public function setId_tipo_activ_txt(string $id_tipo_activ_txt): void
    {
        $this->id_tipo_activ_txt = $id_tipo_activ_txt;
    }


    public function getId_fase_ref(): int
    {
        return $this->id_fase_ref;
    }


    public function setId_fase_ref(int $id_fase_ref): void
    {
        $this->id_fase_ref = $id_fase_ref;
    }


    public function isAviso_off(): bool
    {
        return $this->aviso_off;
    }


    public function setAviso_off(bool $aviso_off): void
    {
        $this->aviso_off = $aviso_off;
    }


    public function isAviso_on(): bool
    {
        return $this->aviso_on;
    }


    public function setAviso_on(bool $aviso_on): void
    {
        $this->aviso_on = $aviso_on;
    }


    public function isAviso_outdate(): bool
    {
        return $this->aviso_outdate;
    }


    public function setAviso_outdate(bool $aviso_outdate): void
    {
        $this->aviso_outdate = $aviso_outdate;
    }


    /**
     * @deprecated Usar `getObjetoVo(): ObjetoNombre` en su lugar.
     */
    public function getObjeto(): string
    {
        return $this->objeto->value();
    }


    /**
     * @deprecated Usar `setObjetoVo(ObjetoNombre $vo): void` en su lugar.
     */
    public function setObjeto(string $objeto): void
    {
        $this->objeto = new ObjetoNombre($objeto);
    }

    public function getObjetoVo(): ObjetoNombre
    {
        return $this->objeto;
    }

    public function setObjetoVo(ObjetoNombre $vo): void
    {
        $this->objeto = $vo;
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

    /**
     * @deprecated Usar `getIdPauVo(): ?PauId` en su lugar.
     */
    public function getCsv_id_pau(): ?string
    {
        return $this->csv_id_pau?->value();
    }

    /**
     * @deprecated Usar `setIdPauVo(?PauId $vo): void` en su lugar.
     */
    public function setCsv_id_pau(?string $csv_id_pau = null): void
    {
        $this->csv_id_pau = $csv_id_pau !== null ? new PauId($csv_id_pau) : null;
    }

    public function getCsvIdPauVo(): ?PauId
    {
        return $this->csv_id_pau;
    }

    public function setCsvIdPauVo(?PauId $vo): void
    {
        $this->csv_id_pau = $vo;
    }
}