<?php

namespace src\utils_database\domain\entity;

use src\utils_database\domain\value_objects\MapIdDl;
use src\utils_database\domain\value_objects\MapIdResto;
use src\utils_database\domain\value_objects\MapObjectCode;

/**
 * Clase que implementa la entidad map_id
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class MapId
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Objeto de MapId
     *
     * @var string
     */
    private string $sobjeto;
    /**
     * Id_resto de MapId
     *
     * @var int
     */
    private int $iid_resto;
    /**
     * Id_dl de MapId
     *
     * @var int
     */
    private int $iid_dl;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return MapId
     */
    public function setAllAttributes(array $aDatos): MapId
    {
        if (array_key_exists('objeto', $aDatos)) {
            $this->setObjetoVo(MapObjectCode::fromString($aDatos['objeto']));
        }
        if (array_key_exists('id_resto', $aDatos)) {
            $this->setIdRestoVo(new MapIdResto((int)$aDatos['id_resto']));
        }
        if (array_key_exists('id_dl', $aDatos)) {
            $this->setIdDlVo(new MapIdDl((int)$aDatos['id_dl']));
        }
        return $this;
    }

    /**
     * LEGACY
     * @return string $sobjeto
     */
    public function getObjeto(): string
    {
        return $this->sobjeto;
    }

    /**
     * LEGACY
     * @param string $sobjeto
     */
    public function setObjeto(string $sobjeto): void
    {
        $this->sobjeto = $sobjeto;
    }

    // Value Object API for objeto
    public function getObjetoVo(): MapObjectCode
    {
        return new MapObjectCode($this->sobjeto);
    }

    public function setObjetoVo(MapObjectCode $code): void
    {
        $this->sobjeto = $code->value();
    }

    /**
     * LEGACY
     * @return int $iid_resto
     */
    public function getId_resto(): int
    {
        return $this->iid_resto;
    }

    /**
     * LEGACY
     * @param int $iid_resto
     */
    public function setId_resto(int $iid_resto): void
    {
        $this->iid_resto = $iid_resto;
    }

    // Value Object API for id_resto
    public function getIdRestoVo(): MapIdResto
    {
        return new MapIdResto($this->iid_resto);
    }

    public function setIdRestoVo(MapIdResto $id): void
    {
        $this->iid_resto = $id->value();
    }

    /**
     * LEGACY
     * @return int $iid_dl
     */
    public function getId_dl(): int
    {
        return $this->iid_dl;
    }

    /**
     * LEGACY
     * @param int $iid_dl
     */
    public function setId_dl(int $iid_dl): void
    {
        $this->iid_dl = $iid_dl;
    }

    // Value Object API for id_dl
    public function getIdDlVo(): MapIdDl
    {
        return new MapIdDl($this->iid_dl);
    }

    public function setIdDlVo(MapIdDl $id): void
    {
        $this->iid_dl = $id->value();
    }
}