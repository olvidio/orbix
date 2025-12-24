<?php

namespace src\actividades\domain\entity;

/**
 * Clase que implementa la entidad a_tipos_actividad
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class TipoDeActividad
{
    /* OTROS MÉTODOS  ----------------------------------------------------------*/

    /**
     * Establece el valor del atributo iid_tipo_proceso_(sf/sv) de TipoDeActividad
     *
     */
    public function setId_tipo_proceso(?int $iid_tipo_proceso, $isfsv)
    {
        if ($isfsv === 1) {
            $this->iid_tipo_proceso_sv = $iid_tipo_proceso;
        } else {
            $this->iid_tipo_proceso_sf = $iid_tipo_proceso;
        }
    }

    /**
     * Recupera el atributo iid_tipo_proceso_(sv/sf) de TipoDeActividad
     * Si el parametro isfsv no existe, se toma el del usuario.
     *
     */
    public function getId_tipo_proceso($isfsv): ?int
    {
        if ($isfsv === 1) {
            $id_tipo_proceso = $this->iid_tipo_proceso_sv;
        } else {
            $id_tipo_proceso = $this->iid_tipo_proceso_sf;
        }
        return $id_tipo_proceso;
    }

    /**
     * Establece el valor del atributo iid_tipo_proceso_ex_(sf/sv) de TipoDeActividad
     */
    public function setId_tipo_proceso_ex(?int $iid_tipo_proceso_ex, $isfsv)
    {
        if ($isfsv === 1) {
            $this->iid_tipo_proceso_ex_sv = $iid_tipo_proceso_ex;
        } else {
            $this->iid_tipo_proceso_ex_sf = $iid_tipo_proceso_ex;
        }
    }

    /**
     * Recupera el atributo iid_tipo_proceso_ex_(sv/sf) de TipoDeActividad
     */
    public function getId_tipo_proceso_ex($isfsv): ?int
    {
        if ($isfsv === 1) {
            $id_tipo_proceso_ex = $this->iid_tipo_proceso_ex_sv;
        } else {
            $id_tipo_proceso_ex = $this->iid_tipo_proceso_ex_sf;
        }
        return $id_tipo_proceso_ex;
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_tipo_activ de TipoDeActividad
     *
     * @var int
     */
    private int $iid_tipo_activ;
    /**
     * Nombre de TipoDeActividad
     *
     * @var string
     */
    private string $snombre;
    /**
     * Id_tipo_proceso_sv de TipoDeActividad
     *
     * @var int|null
     */
    private int|null $iid_tipo_proceso_sv = null;
    /**
     * Id_tipo_proceso_ex_sv de TipoDeActividad
     *
     * @var int|null
     */
    private int|null $iid_tipo_proceso_ex_sv = null;
    /**
     * Id_tipo_proceso_sf de TipoDeActividad
     *
     * @var int|null
     */
    private int|null $iid_tipo_proceso_sf = null;
    /**
     * Id_tipo_proceso_ex_sf de TipoDeActividad
     *
     * @var int|null
     */
    private int|null $iid_tipo_proceso_ex_sf = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TipoDeActividad
     */
    public function setAllAttributes(array $aDatos): TipoDeActividad
    {
        if (array_key_exists('id_tipo_activ', $aDatos)) {
            $this->setId_tipo_activ($aDatos['id_tipo_activ']);
        }
        if (array_key_exists('nombre', $aDatos)) {
            $this->setNombre($aDatos['nombre']);
        }
        if (array_key_exists('id_tipo_proceso_sv', $aDatos)) {
            $this->setId_tipo_proceso_sv($aDatos['id_tipo_proceso_sv']);
        }
        if (array_key_exists('id_tipo_proceso_ex_sv', $aDatos)) {
            $this->setId_tipo_proceso_ex_sv($aDatos['id_tipo_proceso_ex_sv']);
        }
        if (array_key_exists('id_tipo_proceso_sf', $aDatos)) {
            $this->setId_tipo_proceso_sf($aDatos['id_tipo_proceso_sf']);
        }
        if (array_key_exists('id_tipo_proceso_ex_sf', $aDatos)) {
            $this->setId_tipo_proceso_ex_sf($aDatos['id_tipo_proceso_ex_sf']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_tipo_activ
     */
    public function getId_tipo_activ(): int
    {
        return $this->iid_tipo_activ;
    }

    /**
     *
     * @param int $iid_tipo_activ
     */
    public function setId_tipo_activ(int $iid_tipo_activ): void
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    /**
     *
     * @return string $snombre
     */
    public function getNombre(): string
    {
        return $this->snombre;
    }

    /**
     *
     * @param string $snombre
     */
    public function setNombre(string $snombre): void
    {
        $this->snombre = $snombre;
    }

    /**
     *
     * @return int|null $iid_tipo_proceso_sv
     */
    public function getId_tipo_proceso_sv(): ?int
    {
        return $this->iid_tipo_proceso_sv;
    }

    /**
     *
     * @param int|null $iid_tipo_proceso_sv
     */
    public function setId_tipo_proceso_sv(?int $iid_tipo_proceso_sv = null): void
    {
        $this->iid_tipo_proceso_sv = $iid_tipo_proceso_sv;
    }

    /**
     *
     * @return int|null $iid_tipo_proceso_ex_sv
     */
    public function getId_tipo_proceso_ex_sv(): ?int
    {
        return $this->iid_tipo_proceso_ex_sv;
    }

    /**
     *
     * @param int|null $iid_tipo_proceso_ex_sv
     */
    public function setId_tipo_proceso_ex_sv(?int $iid_tipo_proceso_ex_sv = null): void
    {
        $this->iid_tipo_proceso_ex_sv = $iid_tipo_proceso_ex_sv;
    }

    /**
     *
     * @return int|null $iid_tipo_proceso_sf
     */
    public function getId_tipo_proceso_sf(): ?int
    {
        return $this->iid_tipo_proceso_sf;
    }

    /**
     *
     * @param int|null $iid_tipo_proceso_sf
     */
    public function setId_tipo_proceso_sf(?int $iid_tipo_proceso_sf = null): void
    {
        $this->iid_tipo_proceso_sf = $iid_tipo_proceso_sf;
    }

    /**
     *
     * @return int|null $iid_tipo_proceso_ex_sf
     */
    public function getId_tipo_proceso_ex_sf(): ?int
    {
        return $this->iid_tipo_proceso_ex_sf;
    }

    /**
     *
     * @param int|null $iid_tipo_proceso_ex_sf
     */
    public function setId_tipo_proceso_ex_sf(?int $iid_tipo_proceso_ex_sf = null): void
    {
        $this->iid_tipo_proceso_ex_sf = $iid_tipo_proceso_ex_sf;
    }
}