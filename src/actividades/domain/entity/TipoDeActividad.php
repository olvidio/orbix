<?php

namespace src\actividades\domain\entity;

use src\actividades\domain\value_objects\TipoActivNombre;
use src\shared\domain\traits\Hydratable;

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
    use Hydratable;

    /* OTROS MÉTODOS  ----------------------------------------------------------*/

    /**
     * Establece el valor del atributo iid_tipo_proceso_(sf/sv) de TipoDeActividad
     *
     */
    private function setId_tipo_proceso(?int $id_tipo_proceso, int $sfsv): void
    {
        if ($sfsv === 1) {
            $this->id_tipo_proceso_sv = $id_tipo_proceso;
        } else {
            $this->id_tipo_proceso_sf = $id_tipo_proceso;
        }
    }

    /**
     * Recupera el atributo iid_tipo_proceso_(sv/sf) de TipoDeActividad
     * Si el parametro isfsv no existe, se toma el del usuario.
     *
     */
    public function getId_tipo_proceso(int $sfsv): ?int
    {
        if ($sfsv === 1) {
            $id_tipo_proceso = $this->id_tipo_proceso_sv;
        } else {
            $id_tipo_proceso = $this->id_tipo_proceso_sf;
        }
        return $id_tipo_proceso;
    }

    /**
     * Establece el valor del atributo iid_tipo_proceso_ex_(sf/sv) de TipoDeActividad
     */
    public function setId_tipo_proceso_ex(?int $id_tipo_proceso_ex, int $sfsv): void
    {
        if ($sfsv === 1) {
            $this->id_tipo_proceso_ex_sv = $id_tipo_proceso_ex;
        } else {
            $this->id_tipo_proceso_ex_sf = $id_tipo_proceso_ex;
        }
    }

    /**
     * Recupera el atributo iid_tipo_proceso_ex_(sv/sf) de TipoDeActividad
     */
    public function getId_tipo_proceso_ex(int $sfsv): ?int
    {
        if ($sfsv === 1) {
            $id_tipo_proceso_ex = $this->id_tipo_proceso_ex_sv;
        } else {
            $id_tipo_proceso_ex = $this->id_tipo_proceso_ex_sf;
        }
        return $id_tipo_proceso_ex;
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_tipo_activ de TipoDeActividad
     *
     * @var int
     */
    private int $id_tipo_activ;
    /**
     * Nombre de TipoDeActividad
     *
     */
    private TipoActivNombre $nombre;
    /**
     * Id_tipo_proceso_sv de TipoDeActividad
     *
     * @var int|null
     */
    private ?int $id_tipo_proceso_sv = null;
    /**
     * Id_tipo_proceso_ex_sv de TipoDeActividad
     *
     * @var int|null
     */
    private ?int $id_tipo_proceso_ex_sv = null;
    /**
     * Id_tipo_proceso_sf de TipoDeActividad
     *
     * @var int|null
     */
    private ?int $id_tipo_proceso_sf = null;
    /**
     * Id_tipo_proceso_ex_sf de TipoDeActividad
     *
     * @var int|null
     */
    private ?int $id_tipo_proceso_ex_sf = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     *
     * @return int $id_tipo_activ
     */
    public function getId_tipo_activ(): int
    {
        return $this->id_tipo_activ;
    }

    /**
     *
     * @param int $id_tipo_activ
     */
    public function setId_tipo_activ(int $id_tipo_activ): void
    {
        $this->id_tipo_activ = $id_tipo_activ;
    }

    /**
     *
     * @deprecated use getNombreVo()
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     *
     * @deprecated use setNombreVo()
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = new TipoActivNombre($nombre);
    }


    public function getNombreVo(): TipoActivNombre
    {
        return $this->nombre;
    }


    public function setNombreVo(TipoActivNombre|string $texto): void
    {
        $this->nombre = $texto instanceof TipoActivNombre
            ? $texto
            : TipoActivNombre::fromNullableString($texto);
    }

    /**
     *
     * @return int|null $id_tipo_proceso_sv
     */
    public function getId_tipo_proceso_sv(): ?int
    {
        return $this->id_tipo_proceso_sv;
    }

    /**
     *
     * @param int|null $id_tipo_proceso_sv
     */
    public function setId_tipo_proceso_sv(?int $id_tipo_proceso_sv = null): void
    {
        $this->id_tipo_proceso_sv = $id_tipo_proceso_sv;
    }

    /**
     *
     * @return int|null $id_tipo_proceso_ex_sv
     */
    public function getId_tipo_proceso_ex_sv(): ?int
    {
        return $this->id_tipo_proceso_ex_sv;
    }

    /**
     *
     * @param int|null $id_tipo_proceso_ex_sv
     */
    public function setId_tipo_proceso_ex_sv(?int $id_tipo_proceso_ex_sv = null): void
    {
        $this->id_tipo_proceso_ex_sv = $id_tipo_proceso_ex_sv;
    }

    /**
     *
     * @return int|null $id_tipo_proceso_sf
     */
    public function getId_tipo_proceso_sf(): ?int
    {
        return $this->id_tipo_proceso_sf;
    }

    /**
     *
     * @param int|null $id_tipo_proceso_sf
     */
    public function setId_tipo_proceso_sf(?int $id_tipo_proceso_sf = null): void
    {
        $this->id_tipo_proceso_sf = $id_tipo_proceso_sf;
    }

    /**
     *
     * @return int|null $id_tipo_proceso_ex_sf
     */
    public function getId_tipo_proceso_ex_sf(): ?int
    {
        return $this->id_tipo_proceso_ex_sf;
    }

    /**
     *
     * @param int|null $id_tipo_proceso_ex_sf
     */
    public function setId_tipo_proceso_ex_sf(?int $id_tipo_proceso_ex_sf = null): void
    {
        $this->id_tipo_proceso_ex_sf = $id_tipo_proceso_ex_sf;
    }
}