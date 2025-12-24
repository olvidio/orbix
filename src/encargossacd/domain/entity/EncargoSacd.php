<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\EncargoModoId;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad encargos_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class EncargoSacd
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de EncargoSacd
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_enc de EncargoSacd
     *
     * @var int
     */
    private int $iid_enc;
    /**
     * Id_nom de EncargoSacd
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Modo de EncargoSacd
     *
     * @var EncargoModoId
     */
    private EncargoModoId $imodo;
    /**
     * F_ini de EncargoSacd
     *
     * @var DateTimeLocal
     */
    private DateTimeLocal $df_ini;
    /**
     * F_fin de EncargoSacd
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_fin = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoSacd
     */
    public function setAllAttributes(array $aDatos): EncargoSacd
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_enc', $aDatos)) {
            $this->setId_enc($aDatos['id_enc']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('modo', $aDatos)) {
            $this->setModo($aDatos['modo']);
        }
        if (array_key_exists('f_ini', $aDatos)) {
            $this->setF_ini($aDatos['f_ini']);
        }
        if (array_key_exists('f_fin', $aDatos)) {
            $this->setF_fin($aDatos['f_fin']);
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
     * @return int $iid_enc
     */
    public function getId_enc(): int
    {
        return $this->iid_enc;
    }

    /**
     *
     * @param int $iid_enc
     */
    public function setId_enc(int $iid_enc): void
    {
        $this->iid_enc = $iid_enc;
    }

    /**
     *
     * @return int $iid_nom
     */
    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int $iid_nom
     */
    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }
    /**
     *
     * @return int $imodo
     */
    /**
     * @deprecated Usar `getModoVo(): EncargoModoId` en su lugar.
     */
    public function getModo(): int
    {
        return $this->imodo->value();
    }
    /**
     *
     * @param int $imodo
     */
    /**
     * @deprecated Usar `setModoVo(EncargoModoId $vo): void` en su lugar.
     */
    public function setModo(int $imodo): void
    {
        $this->imodo = new EncargoModoId($imodo);
    }

    public function getModoVo(): EncargoModoId
    {
        return $this->imodo;
    }

    public function setModoVo(EncargoModoId $vo): void
    {
        $this->imodo = $vo;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_ini
     */
    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_ini ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_ini
     */
    public function setF_ini(DateTimeLocal|null $df_ini = null): void
    {
        $this->df_ini = $df_ini;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_fin
     */
    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_fin ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_fin
     */
    public function setF_fin(DateTimeLocal|null $df_fin = null): void
    {
        $this->df_fin = $df_fin;
    }
}