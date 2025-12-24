<?php

namespace src\actividades\domain\entity;

use ReflectionClass;
use src\actividades\domain\value_objects\ActividadDescText;
use src\actividades\domain\value_objects\ActividadNomText;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\RepeticionId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\shared\domain\value_objects\Dinero;
use src\ubis\domain\value_objects\DelegacionCode;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use web\NullTimeLocal;
use web\TimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad a_actividades_all
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class ActividadAll
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $iid_auto; // para las clases Dl y Ex, al hacer insert
    private int $iid_activ;
    private ActividadTipoId $iid_tipo_activ;
    private ?DelegacionCode $sdl_org = null;
    private ActividadNomText $snom_activ;
    private ?int $iid_ubi = null;
    private ?ActividadDescText $sdesc_activ = null;
    private DateTimeLocal $df_ini;
    private ?TimeLocal $th_ini = null;
    private DateTimeLocal $df_fin;
    private ?TimeLocal $th_fin = null;
    private ?int $itipo_horario = null;
    private ?Dinero $iprecio = null;
    private int|null $inum_asistentes = null;
    private StatusId $istatus;
    private string|null $sobserv = null;
    private ?NivelStgrId $inivel_stgr = null;
    private string|null $sobserv_material = null;
    private string|null $slugar_esp = null;
    private ?TarifaId $itarifa = null;
    private ?RepeticionId $iid_repeticion = null;
    private ?bool $bpublicado = false;
    private ?IdTablaCode $sid_tabla = null;
    private int|null $iplazas = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getClassName()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ActividadAll
     */
    public function setAllAttributes(array $aDatos): ActividadAll
    {
        if (array_key_exists('id_auto', $aDatos)) {
            $this->setId_auto($aDatos['id_auto']);
        }
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('id_tipo_activ', $aDatos)) {
            $valor = $aDatos['id_tipo_activ'];
            if ($valor instanceof ActividadTipoId) {
                $this->setTipoActividadVo($valor);
            } else {
                $this->setId_tipo_activ($valor);
            }
        }
        if (array_key_exists('dl_org', $aDatos)) {
            $valor = $aDatos['dl_org'];
            if ($valor instanceof DelegacionCode || $valor === null) {
                $this->setDlOrgVo($valor);
            } else {
                $this->setDl_org($valor);
            }
        }
        if (array_key_exists('nom_activ', $aDatos)) {
            $valor = $aDatos['nom_activ'];
            if ($valor instanceof ActividadNomText) {
                $this->setNomActivVo($valor);
            } else {
                $this->setNom_activ($valor);
            }
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_ubi($aDatos['id_ubi']);
        }
        if (array_key_exists('desc_activ', $aDatos)) {
            $valor = $aDatos['desc_activ'];
            if ($valor instanceof ActividadDescText || $valor === null) {
                $this->setDescActivVo($valor);
            } else {
                $this->setDesc_activ($valor);
            }
        }
        if (array_key_exists('f_ini', $aDatos)) {
            $this->setF_ini($aDatos['f_ini']);
        }
        if (array_key_exists('h_ini', $aDatos)) {
            $this->setH_ini($aDatos['h_ini']);
        }
        if (array_key_exists('f_fin', $aDatos)) {
            $this->setF_fin($aDatos['f_fin']);
        }
        if (array_key_exists('h_fin', $aDatos)) {
            $this->setH_fin($aDatos['h_fin']);
        }
        if (array_key_exists('tipo_horario', $aDatos)) {
            $this->setTipo_horario($aDatos['tipo_horario']);
        }
        if (array_key_exists('precio', $aDatos)) {
            $valor = $aDatos['precio'];
            if ($valor instanceof Dinero || $valor === null) {
                $this->setPrecioVo($valor);
            } else {
                $this->setPrecio($valor);
            }
        }
        if (array_key_exists('num_asistentes', $aDatos)) {
            $this->setNum_asistentes($aDatos['num_asistentes']);
        }
        if (array_key_exists('status', $aDatos)) {
            $valor = $aDatos['status'];
            if ($valor instanceof StatusId) {
                $this->setStatusVo($valor);
            } else {
                $this->setStatus($valor);
            }
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('nivel_stgr', $aDatos)) {
            $valor = $aDatos['nivel_stgr'];
            if ($valor instanceof NivelStgrId || $valor === null) {
                $this->setNivelStgrVo($valor);
            } else {
                $this->setNivel_stgr($valor);
            }
        }
        if (array_key_exists('observ_material', $aDatos)) {
            $this->setObserv_material($aDatos['observ_material']);
        }
        if (array_key_exists('lugar_esp', $aDatos)) {
            $this->setLugar_esp($aDatos['lugar_esp']);
        }
        if (array_key_exists('tarifa', $aDatos)) {
            $valor = $aDatos['tarifa'];
            if ($valor instanceof TarifaId || $valor === null) {
                $this->setTarifaVo($valor);
            } else {
                $this->setTarifa($valor);
            }
        }
        if (array_key_exists('id_repeticion', $aDatos)) {
            $this->setId_repeticion($aDatos['id_repeticion']);
        }
        if (array_key_exists('publicado', $aDatos)) {
            $this->setPublicado(is_true($aDatos['publicado']));
        }
        if (array_key_exists('id_tabla', $aDatos)) {
            $valor = $aDatos['id_tabla'];
            if ($valor instanceof IdTablaCode || $valor === null) {
                $this->setIdTablaVo($valor);
            } else {
                $this->setId_tabla($valor);
            }
        }
        if (array_key_exists('plazas', $aDatos)) {
            $this->setPlazas($aDatos['plazas']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_auto
     */
    public function getId_auto(): int
    {
        return $this->iid_auto;
    }

    /**
     *
     * @param int $iid_auto
     */
    public function setId_auto(int $iid_auto): void
    {
        $this->iid_auto = $iid_auto;
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
     * @return int $iid_tipo_activ
     */
    /**
     * @deprecated Usar `getTipoActividadVo(): ActividadTipoId` en su lugar.
     */
    public function getId_tipo_activ(): int
    {
        return $this->iid_tipo_activ->value();
    }

    /**
     *
     * @param int $iid_tipo_activ
     */
    /**
     * @deprecated Usar `setTipoActividadVo(ActividadTipoId $vo): void` en su lugar.
     */
    public function setId_tipo_activ(int $iid_tipo_activ): void
    {
        $this->iid_tipo_activ = new ActividadTipoId($iid_tipo_activ);
    }

    public function getTipoActividadVo(): ActividadTipoId
    {
        return $this->iid_tipo_activ;
    }

    public function setTipoActividadVo(ActividadTipoId $vo): void
    {
        $this->iid_tipo_activ = $vo;
    }

    /**
     *
     * @return string|null $sdl_org
     */
    /**
     * @deprecated Usar `getDlOrgVo(): ?DelegacionCode` en su lugar.
     */
    public function getDl_org(): ?string
    {
        return $this->sdl_org?->value();
    }

    /**
     *
     * @param string|null $sdl_org
     */
    /**
     * @deprecated Usar `setDlOrgVo(?DelegacionCode $codigo = null): void` en su lugar.
     */
    public function setDl_org(?string $sdl_org = null): void
    {
        $this->sdl_org = new DelegacionCode($sdl_org);
    }

    public function getDlOrgVo(): ?DelegacionCode
    {
        return $this->sdl_org;
    }

    public function setDlOrgVo(?DelegacionCode $codigo = null): void
    {
        $this->sdl_org = $codigo?->value();
    }

    /**
     *
     * @return string $snom_activ
     */
    /**
     * @deprecated Usar `getNomActivVo(): ActividadNomText` en su lugar.
     */
    public function getNom_activ(): string
    {
        return $this->snom_activ;
    }

    /**
     *
     * @param string $snom_activ
     */
    /**
     * @deprecated Usar `setNomActivVo(ActividadNomText $vo): void` en su lugar.
     */
    public function setNom_activ(string $snom_activ): void
    {
        $this->snom_activ = new ActividadNomText($snom_activ);
    }

    public function getNomActivVo(): ActividadNomText
    {
        return $this->snom_activ;
    }

    public function setNomActivVo(ActividadNomText $vo): void
    {
        $this->snom_activ = $vo;
    }

    /**
     *
     * @return int|null $iid_ubi
     */
    public function getId_ubi(): ?int
    {
        return $this->iid_ubi;
    }

    /**
     *
     * @param int|null $iid_ubi
     */
    public function setId_ubi(?int $iid_ubi = null): void
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     *
     * @return string|null $sdesc_activ
     */
    /**
     * @deprecated Usar `getDescActivVo(): ?ActividadDescText` en su lugar.
     */
    public function getDesc_activ(): ?string
    {
        return $this->sdesc_activ?->value();
    }

    /**
     *
     * @param string|null $sdesc_activ
     */
    /**
     * @deprecated Usar `setDescActivVo(?ActividadDescText $vo = null): void` en su lugar.
     */
    public function setDesc_activ(?string $sdesc_activ = null): void
    {
        $this->sdesc_activ = ActividadDescText::fromNullableString($sdesc_activ);
    }

    public function getDescActivVo(): ?ActividadDescText
    {
        return $this->sdesc_activ;
    }

    public function setDescActivVo(?ActividadDescText $vo = null): void
    {
        $this->sdesc_activ = $vo?->value();
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
     * @return TimeLocal|NullTimeLocal|null $th_ini
     */
    public function getH_ini(): TimeLocal|NullTimeLocal|null
    {
        return $this->th_ini;
    }

    /**
     *
     * @param TimeLocal|null $th_ini
     */
    public function setH_ini(TimeLocal|null $th_ini = null): void
    {
        $this->th_ini = $th_ini;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_fin
     */
    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_fin;
    }

    /**
     *
     * @param DateTimeLocal|null $df_fin
     */
    public function setF_fin(DateTimeLocal|null $df_fin = null): void
    {
        $this->df_fin = $df_fin;
    }

    /**
     *
     * @return TimeLocal|NullTimeLocal|null $th_fin
     */
    public function getH_fin(): TimeLocal|NullTimeLocal|null
    {
        return $this->th_fin;
    }

    /**
     *
     * @param TimeLocal|null $th_fin
     */
    public function setH_fin(TimeLocal|null $th_fin = null): void
    {
        $this->th_fin = $th_fin;
    }

    /**
     *
     * @return int|null $itipo_horario
     */
    public function getTipo_horario(): ?int
    {
        return $this->itipo_horario;
    }

    /**
     *
     * @param int|null $itipo_horario
     */
    public function setTipo_horario(?int $itipo_horario = null): void
    {
        $this->itipo_horario = $itipo_horario;
    }

    /**
     *
     * @return float|null $iprecio
     */
    /**
     * @deprecated Usar `getPrecioVo(): ?Dinero` en su lugar.
     */
    public function getPrecio(): ?float
    {
        return $this->iprecio?->asFloat();
    }

    /**
     *
     * @param float|null $iprecio
     */
    /**
     * @deprecated Usar `setPrecioVo(?Dinero $vo = null): void` en su lugar.
     */
    public function setPrecio(?float $iprecio = null): void
    {
        $this->iprecio = Dinero::fromNullable($iprecio);
    }

    public function getPrecioVo(): ?Dinero
    {
        return $this->iprecio;
    }

    public function setPrecioVo(?Dinero $vo = null): void
    {
        $this->iprecio = $vo;
    }

    /**
     *
     * @return int|null $inum_asistentes
     */
    public function getNum_asistentes(): ?int
    {
        return $this->inum_asistentes;
    }

    /**
     *
     * @param int|null $inum_asistentes
     */
    public function setNum_asistentes(?int $inum_asistentes = null): void
    {
        $this->inum_asistentes = $inum_asistentes;
    }

    /**
     *
     * @return int $istatus
     */
    /**
     * @deprecated Usar `getStatusVo(): StatusId` en su lugar.
     */
    public function getStatus(): int
    {
        return $this->istatus->value();
    }

    /**
     *
     * @param int $istatus
     */
    /**
     * @deprecated Usar `setStatusVo(StatusId $vo): void` en su lugar.
     */
    public function setStatus(int $istatus): void
    {
        $this->istatus = new StatusId($istatus);
    }

    public function getStatusVo(): StatusId
    {
        return $this->istatus;
    }

    public function setStatusVo(StatusId $vo): void
    {
        $this->istatus = $vo;
    }

    /**
     *
     * @return string|null $sobserv
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }

    /**
     *
     * @return int|null $inivel_stgr
     */
    /**
     * @deprecated Usar `getNivelStgrVo(): ?NivelStgrId` en su lugar.
     */
    public function getNivel_stgr(): ?int
    {
        return $this->inivel_stgr?->value();
    }

    /**
     *
     * @param int|null $inivel_stgr
     */
    /**
     * @deprecated Usar `setNivelStgrVo(?NivelStgrId $vo = null): void` en su lugar.
     */
    public function setNivel_stgr(?int $inivel_stgr = null): void
    {
        $this->inivel_stgr = new NivelStgrId($inivel_stgr);
    }

    public function getNivelStgrVo(): ?NivelStgrId
    {
        return $this->inivel_stgr;
    }

    public function setNivelStgrVo(?NivelStgrId $vo = null): void
    {
        $this->inivel_stgr = $vo?->value();
    }

    /**
     *
     * @return string|null $sobserv_material
     */
    public function getObserv_material(): ?string
    {
        return $this->sobserv_material;
    }

    /**
     *
     * @param string|null $sobserv_material
     */
    public function setObserv_material(?string $sobserv_material = null): void
    {
        $this->sobserv_material = $sobserv_material;
    }

    /**
     *
     * @return string|null $slugar_esp
     */
    public function getLugar_esp(): ?string
    {
        return $this->slugar_esp;
    }

    /**
     *
     * @param string|null $slugar_esp
     */
    public function setLugar_esp(?string $slugar_esp = null): void
    {
        $this->slugar_esp = $slugar_esp;
    }

    /**
     *
     * @return int|null $itarifa
     */
    /**
     * @deprecated Usar `getTarifaVo(): ?TarifaId` en su lugar.
     */
    public function getTarifa(): ?int
    {
        return $this->itarifa?->value();
    }

    /**
     *
     * @param int|null $itarifa
     */
    /**
     * @deprecated Usar `setTarifaVo(?TarifaId $vo = null): void` en su lugar.
     */
    public function setTarifa(?int $itarifa = null): void
    {
        $this->itarifa = TarifaId::fromNullable($itarifa);
    }

    public function getTarifaVo(): ?TarifaId
    {
        return $this->itarifa;
    }

    public function setTarifaVo(?TarifaId $vo = null): void
    {
        $this->itarifa = $vo;
    }

    /**
     *
     * @return int|null $iid_repeticion
     */
    /**
     * @deprecated Usar `setIdRepeticionVo()` en su lugar.
     */
    public function getId_repeticion(): ?int
    {
        return $this->iid_repeticion?->value();
    }

    /**
     *
     * @param int|null $iid_repeticion
     */
    /**
     * @deprecated Usar `setIdRepeticionVo(): void` en su lugar.
     */
    public function setId_repeticion(?int $id_repeticion = null): void
    {
        $this->iid_repeticion = new RepeticionId($id_repeticion);
    }

    public function getIdRepeticionVo(): ?RepeticionId
    {
        return $this->iid_repeticion;
    }

    public function setIdRepeticionVo(?RepeticionId $id_repeticion = null): void
    {
        $this->iid_repeticion = $id_repeticion;
    }


    /**
     *
     * @return bool|null $bpublicado
     */
    public function isPublicado(): ?bool
    {
        return $this->bpublicado;
    }

    /**
     *
     * @param bool|null $bpublicado
     */
    public function setPublicado(?bool $bpublicado = null): void
    {
        $this->bpublicado = $bpublicado;
    }

    /**
     *
     * @return string|null $sid_tabla
     */
    /**
     * @deprecated Usar `getIdTablaVo(): ?IdTablaCode` en su lugar.
     */
    public function getId_tabla(): ?string
    {
        return $this->sid_tabla?->value();
    }

    /**
     *
     * @param string|null $sid_tabla
     */
    /**
     * @deprecated Usar `setIdTablaVo(?IdTablaCode $vo = null): void` en su lugar.
     */
    public function setId_tabla(?string $sid_tabla = null): void
    {
        $this->sid_tabla = new IdTablaCode($sid_tabla);
    }

    public function getIdTablaVo(): ?IdTablaCode
    {
        return IdTablaCode::fromString($this->sid_tabla);
    }

    public function setIdTablaVo(?IdTablaCode $vo = null): void
    {
        $this->sid_tabla = $vo?->value();
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
}