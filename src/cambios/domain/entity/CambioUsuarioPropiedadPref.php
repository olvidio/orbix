<?php

namespace src\cambios\domain\entity;

use src\cambios\domain\value_objects\OperadorPref;
use src\cambios\domain\value_objects\PropiedadNombre;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\entity\Ubi;
use function core\is_true;

class CambioUsuarioPropiedadPref
{
    use Hydratable;

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


    private int $id_item;

    private int $id_item_usuario_objeto;

    private PropiedadNombre $propiedad;

    private OperadorPref|null $operador = null;

    private ?string $valor = null;

    private ?bool $valor_old = null;

    private ?bool $valor_new = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_item_usuario_objeto(): int
    {
        return $this->id_item_usuario_objeto;
    }


    public function setId_item_usuario_objeto(int $id_item_usuario_objeto): void
    {
        $this->id_item_usuario_objeto = $id_item_usuario_objeto;
    }


    /**
     * @deprecated Usar `getPropiedadVo(): PropiedadNombre` en su lugar.
     */
    public function getPropiedad(): string
    {
        return $this->propiedad->value();
    }


    /**
     * @deprecated Usar `setPropiedadVo(PropiedadNombre $vo): void` en su lugar.
     */
    public function setPropiedad(string $propiedad): void
    {
        $this->propiedad = PropiedadNombre::fromNullableString($propiedad);
    }

    public function getPropiedadVo(): PropiedadNombre
    {
        return $this->propiedad;
    }

    public function setPropiedadVo(PropiedadNombre|string|null $texto): void
    {
        $this->propiedad = $texto instanceof PropiedadNombre
            ? $texto
            : PropiedadNombre::fromNullableString($texto);
    }

    /**
     * @return OperadorPref|null
     */
    public function getOperadorVo(): ?OperadorPref
    {
        return $this->operador;
    }


    public function setOperadorVo(OperadorPref|string|null $texto): void
    {
        $this->operador = $texto instanceof OperadorPref
            ? $texto
            : OperadorPref::fromNullableString($texto);
    }

    /**
     * @deprecated Usar `getOperadorVo(): ?OperadorPref` en su lugar.
     */
    public function getOperador(): ?string
    {
        return $this->operador?->value();
    }

    /**
     * @deprecated Usar `setOperadorVo(?OperadorPref $vo): void` en su lugar.
     */
    public function setOperador(?string $operador = null): void
    {
        $this->operador = OperadorPref::fromNullableString($operador);
    }


    public function getValor(): ?string
    {
        return $this->valor;
    }


    public function setValor(?string $valor = null): void
    {
        $this->valor = $valor;
    }


    public function isValor_old(): ?bool
    {
        return $this->valor_old;
    }


    public function setValor_old(?bool $valor_old = null): void
    {
        $this->valor_old = $valor_old;
    }


    public function isValor_new(): ?bool
    {
        return $this->valor_new;
    }


    public function setValor_new(?bool $valor_new = null): void
    {
        $this->valor_new = $valor_new;
    }
}