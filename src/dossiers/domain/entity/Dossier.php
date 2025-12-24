<?php

namespace src\dossiers\domain\entity;

use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad d_dossiers_abiertos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class Dossier
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Tabla de Dossier
     *
     * @var string
     */
    private string $stabla;
    /**
     * Id_pau de Dossier
     *
     * @var int
     */
    private int $iid_pau;
    /**
     * Id_tipo_dossier de Dossier
     *
     * @var int
     */
    private int $iid_tipo_dossier;
    /**
     * F_ini de Dossier
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_ini = null;
    /**
     * F_camb_dossier de Dossier
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_camb_dossier = null;
    /**
     * Status_dossier de Dossier
     *
     * @var bool
     */
    private bool $bstatus_dossier;
    /**
     * F_status de Dossier
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_status = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function abrir(): void
    {
        $oHoy = new DateTimeLocal();
        $this->setF_status($oHoy);
        $this->setStatus_dossier('t');
    }

    public function cerrar(): void
    {
        $oHoy = new DateTimeLocal();
        $this->setF_status($oHoy);
        $this->setStatus_dossier('f');
    }

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Dossier
     */
    public function setAllAttributes(array $aDatos): Dossier
    {
        if (array_key_exists('tabla', $aDatos)) {
            $this->setTabla($aDatos['tabla']);
        }
        if (array_key_exists('id_pau', $aDatos)) {
            $this->setId_pau($aDatos['id_pau']);
        }
        if (array_key_exists('id_tipo_dossier', $aDatos)) {
            $this->setId_tipo_dossier($aDatos['id_tipo_dossier']);
        }
        if (array_key_exists('f_ini', $aDatos)) {
            $this->setF_ini($aDatos['f_ini']);
        }
        if (array_key_exists('f_camb_dossier', $aDatos)) {
            $this->setF_camb_dossier($aDatos['f_camb_dossier']);
        }
        if (array_key_exists('status_dossier', $aDatos)) {
            $this->setStatus_dossier(is_true($aDatos['status_dossier']));
        }
        if (array_key_exists('f_status', $aDatos)) {
            $this->setF_status($aDatos['f_status']);
        }
        return $this;
    }

    /**
     *
     * @return string $stabla
     */
    public function getTabla(): string
    {
        return $this->stabla;
    }

    /**
     *
     * @param string $stabla
     */
    public function setTabla(string $stabla): void
    {
        $this->stabla = $stabla;
    }

    /**
     *
     * @return int $iid_pau
     */
    public function getId_pau(): int
    {
        return $this->iid_pau;
    }

    /**
     *
     * @param int $iid_pau
     */
    public function setId_pau(int $iid_pau): void
    {
        $this->iid_pau = $iid_pau;
    }

    /**
     *
     * @return int $iid_tipo_dossier
     */
    public function getId_tipo_dossier(): int
    {
        return $this->iid_tipo_dossier;
    }

    /**
     *
     * @param int $iid_tipo_dossier
     */
    public function setId_tipo_dossier(int $iid_tipo_dossier): void
    {
        $this->iid_tipo_dossier = $iid_tipo_dossier;
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
     * @return DateTimeLocal|NullDateTimeLocal|null $df_camb_dossier
     */
    public function getF_camb_dossier(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_camb_dossier ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_camb_dossier
     */
    public function setF_camb_dossier(DateTimeLocal|null $df_camb_dossier = null): void
    {
        $this->df_camb_dossier = $df_camb_dossier;
    }

    /**
     *
     * @return bool $bstatus_dossier
     */
    public function isStatus_dossier(): bool
    {
        return $this->bstatus_dossier;
    }

    /**
     *
     * @param bool $bstatus_dossier
     */
    public function setStatus_dossier(bool $bstatus_dossier): void
    {
        $this->bstatus_dossier = $bstatus_dossier;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_status
     */
    public function getF_status(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_status ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_status
     */
    public function setF_status(DateTimeLocal|null $df_status = null): void
    {
        $this->df_status = $df_status;
    }
}