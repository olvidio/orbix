<?php

namespace src\cambios\domain\entity;

use src\cambios\domain\value_objects\OperadorPref;
use src\ubis\domain\entity\Ubi;
use function core\is_true;

/**
 * Clase que implementa la entidad av_cambios_usuario_propiedades_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class CambioUsuarioPropiedadPref
{
    /**
     * Retorna un texte per indicar el canvi que es fará.
     *
     * @return string sCondicio
     */
    public function getTextCambio():string
    {
        if (!is_true($this->isValor_new()) && !is_true($this->isValor_old())) return FALSE;
        $sText = _("si el");
        $sText .= ' ';
        if (is_true($this->isValor_new())) $sText .= _("nuevo valor");
        if (is_true($this->isValor_new()) && is_true($this->isValor_old())) $sText .= ' ' . _("o el") . ' ';
        if (is_true($this->isValor_old())) $sText .= _("valor actual");
        $sText .= ' ';
        $sText .= _("es");
        if ($this->getOperador() === '=') $sText .= ' = ' . _("a");
        if ($this->getOperador() === '>') $sText .= ' > ' . _("que");
        if ($this->getOperador() === '<') $sText .= ' < ' . _("que");
        if ($this->getOperador() === 'regexp') $sText .= ' regexp ' . _("a");

        //$sText .= ' '.$this->getValor();
        switch ($this->getPropiedad()) {
            case 'id_ubi':
                $aId_ubis = explode(',', $this->getValor());
                $sValor = '';
                $i = 0;
                foreach ($aId_ubis as $id_ubi) {
                    $i++;
                    $oUbi = Ubi::NewUbi($id_ubi);
                    if (!$oUbi) continue;
                    if ($i > 1) $sValor .= ' o ';
                    $sValor .= $oUbi->getNombre_ubi();
                }
                break;
            default:
                $sValor = $this->getValor();
        }
        $sText .= ' ' . $sValor;
        return $sText;
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de CambioUsuarioPropiedadPref
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_item_usuario_objeto de CambioUsuarioPropiedadPref
     *
     * @var int
     */
    private int $iid_item_usuario_objeto;
    /**
     * Propiedad de CambioUsuarioPropiedadPref
     *
     * @var string
     */
    private string $spropiedad;
    /**
     * Operador de CambioUsuarioPropiedadPref
     *
     * @var string|null
     */
    private string|null $soperador = null;
    /**
     * Valor de CambioUsuarioPropiedadPref
     *
     * @var string|null
     */
    private string|null $svalor = null;
    /**
     * Valor_old de CambioUsuarioPropiedadPref
     *
     * @var bool|null
     */
    private bool|null $bvalor_old = null;
    /**
     * Valor_new de CambioUsuarioPropiedadPref
     *
     * @var bool|null
     */
    private bool|null $bvalor_new = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return CambioUsuarioPropiedadPref
     */
    public function setAllAttributes(array $aDatos): CambioUsuarioPropiedadPref
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_item_usuario_objeto', $aDatos)) {
            $this->setId_item_usuario_objeto($aDatos['id_item_usuario_objeto']);
        }
        if (array_key_exists('propiedad', $aDatos)) {
            $this->setPropiedad($aDatos['propiedad']);
        }
        if (array_key_exists('operador', $aDatos)) {
            $this->setOperador($aDatos['operador']);
        }
        if (array_key_exists('valor', $aDatos)) {
            $this->setValor($aDatos['valor']);
        }
        if (array_key_exists('valor_old', $aDatos)) {
            $this->setValor_old(is_true($aDatos['valor_old']));
        }
        if (array_key_exists('valor_new', $aDatos)) {
            $this->setValor_new(is_true($aDatos['valor_new']));
        }
        return $this;
    }

    /**
     *
     * @return int $iid_item
     */
    public function getId_item(): int
    {
        return $this->iid_item;
    }

    /**
     *
     * @param int $iid_item
     */
    public function setId_item(int $iid_item): void
    {
        $this->iid_item = $iid_item;
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
     * @return string $spropiedad
     */
    public function getPropiedad(): string
    {
        return $this->spropiedad;
    }

    /**
     *
     * @param string $spropiedad
     */
    public function setPropiedad(string $spropiedad): void
    {
        $this->spropiedad = $spropiedad;
    }

    /**
     * @return OperadorPref
     */
    public function getOperadorVo(): OperadorPref
    {
        return new OperadorPref($this->soperador);
    }

    /**
     * @param OperadorPref $operador
     */
    public function setOperadorVo(OperadorPref $operador): void
    {
        $this->soperador = $operador->value();
    }

    /**
     * @deprecated usar getOperadorVo()
     * @return string|null $soperador
     */
    public function getOperador(): ?string
    {
        return $this->soperador;
    }

    /**
     * @deprecated usar setOperadorVo()
     * @param string|null $soperador
     */
    public function setOperador(?string $soperador = null): void
    {
        $this->soperador = $soperador;
    }

    /**
     *
     * @return string|null $svalor
     */
    public function getValor(): ?string
    {
        return $this->svalor;
    }

    /**
     *
     * @param string|null $svalor
     */
    public function setValor(?string $svalor = null): void
    {
        $this->svalor = $svalor;
    }

    /**
     *
     * @return bool|null $bvalor_old
     */
    public function isValor_old(): ?bool
    {
        return $this->bvalor_old;
    }

    /**
     *
     * @param bool|null $bvalor_old
     */
    public function setValor_old(?bool $bvalor_old = null): void
    {
        $this->bvalor_old = $bvalor_old;
    }

    /**
     *
     * @return bool|null $bvalor_new
     */
    public function isValor_new(): ?bool
    {
        return $this->bvalor_new;
    }

    /**
     *
     * @param bool|null $bvalor_new
     */
    public function setValor_new(?bool $bvalor_new = null): void
    {
        $this->bvalor_new = $bvalor_new;
    }
}