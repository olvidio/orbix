<?php

namespace src\actividadplazas\domain\entity;

use stdClass;

/**
 * Clase que implementa la entidad da_plazas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class ActividadPlazas
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_activ de ActividadPlazas
     *
     * @var int
     */
    private int $iid_activ;
    /**
     * Id_dl de ActividadPlazas
     *
     * @var int
     */
    private int $iid_dl;
    /**
     * Plazas de ActividadPlazas
     *
     * @var int|null
     */
    private int|null $iplazas = null;
    /**
     * Cl de ActividadPlazas
     *
     * @var string|null
     */
    private string|null $scl = null;
    /**
     * Dl_tabla de ActividadPlazas
     *
     * @var string
     */
    private string $sdl_tabla;
    /**
     * Cedidas de ActividadPlazas
     *
     * @var array|stdClass|null
     */
    private array|stdClass|null $cedidas = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ActividadPlazas
     */
    public function setAllAttributes(array $aDatos): ActividadPlazas
    {
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('id_dl', $aDatos)) {
            $this->setId_dl($aDatos['id_dl']);
        }
        if (array_key_exists('plazas', $aDatos)) {
            $this->setPlazas($aDatos['plazas']);
        }
        if (array_key_exists('cl', $aDatos)) {
            $this->setCl($aDatos['cl']);
        }
        if (array_key_exists('dl_tabla', $aDatos)) {
            $this->setDl_tabla($aDatos['dl_tabla']);
        }
        if (array_key_exists('cedidas', $aDatos)) {
            $this->setCedidas($aDatos['cedidas']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_activ
     */
    public function getId_activ(): int
    {
        return $this->iid_activ;
    }

    /**
     *
     * @param int $iid_activ
     */
    public function setId_activ(int $iid_activ): void
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     *
     * @return int $iid_dl
     */
    public function getId_dl(): int
    {
        return $this->iid_dl;
    }

    /**
     *
     * @param int $iid_dl
     */
    public function setId_dl(int $iid_dl): void
    {
        $this->iid_dl = $iid_dl;
    }

    /**
     *
     * @return int|null $iplazas
     */
    public function getPlazas(): ?int
    {
        return $this->iplazas;
    }

    /**
     *
     * @param int|null $iplazas
     */
    public function setPlazas(?int $iplazas = null): void
    {
        $this->iplazas = $iplazas;
    }

    /**
     *
     * @return string|null $scl
     */
    public function getCl(): ?string
    {
        return $this->scl;
    }

    /**
     *
     * @param string|null $scl
     */
    public function setCl(?string $scl = null): void
    {
        $this->scl = $scl;
    }

    /**
     *
     * @return string $sdl_tabla
     */
    public function getDl_tabla(): string
    {
        return $this->sdl_tabla;
    }

    /**
     *
     * @param string $sdl_tabla
     */
    public function setDl_tabla(string $sdl_tabla): void
    {
        $this->sdl_tabla = $sdl_tabla;
    }

    /**
     *
     * @return array|stdClass|null $cedidas
     */
    public function getCedidas(): array|stdClass|null
    {
        return $this->cedidas;
    }

    /**
     *
     * @param stdClass|array|null $cedidas
     */
    public function setCedidas(stdClass|array|null $cedidas = null): void
    {
        $this->cedidas = $cedidas;
    }
}