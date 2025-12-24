<?php

namespace src\cambios\domain\entity;

use src\cambios\domain\value_objects\AvisoTipoId;
use function core\is_true;

/**
 * Clase que implementa la entidad av_cambios_usuario_objeto_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class CambioUsuarioObjetoPref
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item_usuario_objeto de CambioUsuarioObjetoPref
     *
     * @var int
     */
    private int $iid_item_usuario_objeto;
    /**
     * Id_usuario de CambioUsuarioObjetoPref
     *
     * @var int
     */
    private int $iid_usuario;
    /**
     * Dl_org de CambioUsuarioObjetoPref
     *
     * @var string
     */
    private string $sdl_org;
    /**
     * Id_tipo_activ_txt de CambioUsuarioObjetoPref
     *
     * @var string
     */
    private string $sid_tipo_activ_txt;
    /**
     * Id_fase_ref de CambioUsuarioObjetoPref
     *
     * @var int
     */
    private int $iid_fase_ref;
    /**
     * Aviso_off de CambioUsuarioObjetoPref
     *
     * @var bool
     */
    private bool $baviso_off;
    /**
     * Aviso_on de CambioUsuarioObjetoPref
     *
     * @var bool
     */
    private bool $baviso_on;
    /**
     * Aviso_outdate de CambioUsuarioObjetoPref
     *
     * @var bool
     */
    private bool $baviso_outdate;
    /**
     * Objeto de CambioUsuarioObjetoPref
     *
     * @var string
     */
    private string $sobjeto;
    /**
     * Aviso_tipo de CambioUsuarioObjetoPref
     *
     * @var int
     */
    private int $iaviso_tipo;
    /**
     * Id_pau de CambioUsuarioObjetoPref
     *
     * @var string|null
     */
    private string|null $sid_pau = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return CambioUsuarioObjetoPref
     */
    public function setAllAttributes(array $aDatos): CambioUsuarioObjetoPref
    {
        if (array_key_exists('id_item_usuario_objeto', $aDatos)) {
            $this->setId_item_usuario_objeto($aDatos['id_item_usuario_objeto']);
        }
        if (array_key_exists('id_usuario', $aDatos)) {
            $this->setId_usuario($aDatos['id_usuario']);
        }
        if (array_key_exists('dl_org', $aDatos)) {
            $this->setDl_org($aDatos['dl_org']);
        }
        if (array_key_exists('id_tipo_activ_txt', $aDatos)) {
            $this->setId_tipo_activ_txt($aDatos['id_tipo_activ_txt']);
        }
        if (array_key_exists('id_fase_ref', $aDatos)) {
            $this->setId_fase_ref($aDatos['id_fase_ref']);
        }
        if (array_key_exists('aviso_off', $aDatos)) {
            $this->setAviso_off(is_true($aDatos['aviso_off']));
        }
        if (array_key_exists('aviso_on', $aDatos)) {
            $this->setAviso_on(is_true($aDatos['aviso_on']));
        }
        if (array_key_exists('aviso_outdate', $aDatos)) {
            $this->setAviso_outdate(is_true($aDatos['aviso_outdate']));
        }
        if (array_key_exists('objeto', $aDatos)) {
            $this->setObjeto($aDatos['objeto']);
        }
        if (array_key_exists('aviso_tipo', $aDatos)) {
            $this->setAviso_tipo($aDatos['aviso_tipo']);
        }
        if (array_key_exists('id_pau', $aDatos)) {
            $this->setId_pau($aDatos['id_pau']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_item_usuario_objeto
     */
    public function getId_item_usuario_objeto(): int
    {
        return $this->iid_item_usuario_objeto;
    }

    /**
     *
     * @param int $iid_item_usuario_objeto
     */
    public function setId_item_usuario_objeto(int $iid_item_usuario_objeto): void
    {
        $this->iid_item_usuario_objeto = $iid_item_usuario_objeto;
    }

    /**
     *
     * @return int $iid_usuario
     */
    public function getId_usuario(): int
    {
        return $this->iid_usuario;
    }

    /**
     *
     * @param int $iid_usuario
     */
    public function setId_usuario(int $iid_usuario): void
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     *
     * @return string $sdl_org
     */
    public function getDl_org(): string
    {
        return $this->sdl_org;
    }

    /**
     *
     * @param string $sdl_org
     */
    public function setDl_org(string $sdl_org): void
    {
        $this->sdl_org = $sdl_org;
    }

    /**
     *
     * @return string $sid_tipo_activ_txt
     */
    public function getId_tipo_activ_txt(): string
    {
        return $this->sid_tipo_activ_txt;
    }

    /**
     *
     * @param string $sid_tipo_activ_txt
     */
    public function setId_tipo_activ_txt(string $sid_tipo_activ_txt): void
    {
        $this->sid_tipo_activ_txt = $sid_tipo_activ_txt;
    }

    /**
     *
     * @return int $iid_fase_ref
     */
    public function getId_fase_ref(): int
    {
        return $this->iid_fase_ref;
    }

    /**
     *
     * @param int $iid_fase_ref
     */
    public function setId_fase_ref(int $iid_fase_ref): void
    {
        $this->iid_fase_ref = $iid_fase_ref;
    }

    /**
     *
     * @return bool $baviso_off
     */
    public function isAviso_off(): bool
    {
        return $this->baviso_off;
    }

    /**
     *
     * @param bool $baviso_off
     */
    public function setAviso_off(bool $baviso_off): void
    {
        $this->baviso_off = $baviso_off;
    }

    /**
     *
     * @return bool $baviso_on
     */
    public function isAviso_on(): bool
    {
        return $this->baviso_on;
    }

    /**
     *
     * @param bool $baviso_on
     */
    public function setAviso_on(bool $baviso_on): void
    {
        $this->baviso_on = $baviso_on;
    }

    /**
     *
     * @return bool $baviso_outdate
     */
    public function isAviso_outdate(): bool
    {
        return $this->baviso_outdate;
    }

    /**
     *
     * @param bool $baviso_outdate
     */
    public function setAviso_outdate(bool $baviso_outdate): void
    {
        $this->baviso_outdate = $baviso_outdate;
    }

    /**
     *
     * @return string $sobjeto
     */
    public function getObjeto(): string
    {
        return $this->sobjeto;
    }

    /**
     *
     * @param string $sobjeto
     */
    public function setObjeto(string $sobjeto): void
    {
        $this->sobjeto = $sobjeto;
    }

    /**
     * @return AvisoTipoId
     */
    public function getAvisoTipoVo(): AvisoTipoId
    {
        return new AvisoTipoId($this->iaviso_tipo);
    }

    /**
     * @param AvisoTipoId $avisoTipoId
     */
    public function setAvisoTipoVo(AvisoTipoId $avisoTipoId): void
    {
        $this->iaviso_tipo = $avisoTipoId->value();
    }

    /**
     * @deprecated usar getAvisoTipoVo()
     * @return int $iaviso_tipo
     */
    public function getAviso_tipo(): int
    {
        return $this->iaviso_tipo;
    }

    /**
     * @deprecated usar setAvisoTipoVo()
     * @param int $iaviso_tipo
     */
    public function setAviso_tipo(int $iaviso_tipo): void
    {
        $this->iaviso_tipo = $iaviso_tipo;
    }

    /**
     *
     * @return string|null $sid_pau
     */
    public function getId_pau(): ?string
    {
        return $this->sid_pau;
    }

    /**
     *
     * @param string|null $sid_pau
     */
    public function setId_pau(?string $sid_pau = null): void
    {
        $this->sid_pau = $sid_pau;
    }
}