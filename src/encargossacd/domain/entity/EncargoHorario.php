<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\encargossacd\domain\value_objects\MesNum;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use web\NullTimeLocal;
use web\TimeLocal;

/**
 * Clase que implementa la entidad encargo_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class EncargoHorario
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_enc de EncargoHorario
     *
     * @var int
     */
    private int $iid_enc;
    /**
     * Id_item_h de EncargoHorario
     *
     * @var int
     */
    private int $iid_item_h;
    /**
     * F_ini de EncargoHorario
     *
     * @var DateTimeLocal
     */
    private DateTimeLocal $df_ini;
    /**
     * F_fin de EncargoHorario
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_fin = null;
    /**
     * Dia_ref de EncargoHorario
     *
     * @var DiaRefCode|null
     */
    private DiaRefCode|null $sdia_ref = null;
    /**
     * Dia_num de EncargoHorario
     *
     * @var int|null
     */
    private int|null $idia_num = null;
    /**
     * Mas_menos de EncargoHorario
     *
     * @var MasMenosCode|null
     */
    private MasMenosCode|null $smas_menos = null;
    /**
     * Dia_inc de EncargoHorario
     *
     * @var int|null
     */
    private int|null $idia_inc = null;
    /**
     * H_ini de EncargoHorario
     *
     * @var TimeLocal|null
     */
    private TimeLocal|null $th_ini = null;
    /**
     * H_fin de EncargoHorario
     *
     * @var TimeLocal|null
     */
    private TimeLocal|null $th_fin = null;
    /**
     * N_sacd de EncargoHorario
     *
     * @var int|null
     */
    private int|null $in_sacd = null;
    /**
     * Mes de EncargoHorario
     *
     * @var MesNum|null
     */
    private MesNum|null $imes = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoHorario
     */
    public function setAllAttributes(array $aDatos): EncargoHorario
    {
        if (array_key_exists('id_enc', $aDatos)) {
            $this->setId_enc($aDatos['id_enc']);
        }
        if (array_key_exists('id_item_h', $aDatos)) {
            $this->setId_item_h($aDatos['id_item_h']);
        }
        if (array_key_exists('f_ini', $aDatos)) {
            $this->setF_ini($aDatos['f_ini']);
        }
        if (array_key_exists('f_fin', $aDatos)) {
            $this->setF_fin($aDatos['f_fin']);
        }
        if (array_key_exists('dia_ref', $aDatos)) {
            $this->setDia_ref($aDatos['dia_ref']);
        }
        if (array_key_exists('dia_num', $aDatos)) {
            $this->setDia_num($aDatos['dia_num']);
        }
        if (array_key_exists('mas_menos', $aDatos)) {
            $this->setMas_menos($aDatos['mas_menos']);
        }
        if (array_key_exists('dia_inc', $aDatos)) {
            $this->setDia_inc($aDatos['dia_inc']);
        }
        if (array_key_exists('h_ini', $aDatos)) {
            $this->setH_ini($aDatos['h_ini']);
        }
        if (array_key_exists('h_fin', $aDatos)) {
            $this->setH_fin($aDatos['h_fin']);
        }
        if (array_key_exists('n_sacd', $aDatos)) {
            $this->setN_sacd($aDatos['n_sacd']);
        }
        if (array_key_exists('mes', $aDatos)) {
            $this->setMes($aDatos['mes']);
        }
        return $this;
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
     * @return int $iid_item_h
     */
    public function getId_item_h(): int
    {
        return $this->iid_item_h;
    }

    /**
     *
     * @param int $iid_item_h
     */
    public function setId_item_h(int $iid_item_h): void
    {
        $this->iid_item_h = $iid_item_h;
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
    /**
     *
     * @return string|null $sdia_ref
     */
    /**
     * @deprecated Usar `getDia_refVo(): ?DiaRefCode` en su lugar.
     */
    public function getDia_ref(): ?string
    {
        return $this->sdia_ref?->value();
    }
    /**
     *
     * @param string|null $sdia_ref
     */
    /**
     * @deprecated Usar `setDia_refVo(?DiaRefCode $vo): void` en su lugar.
     */
    public function setDia_ref(?string $sdia_ref = null): void
    {
        $this->sdia_ref = $sdia_ref !== null ? new DiaRefCode($sdia_ref) : null;
    }

    public function getDia_refVo(): ?DiaRefCode
    {
        return $this->sdia_ref;
    }

    public function setDia_refVo(?DiaRefCode $vo): void
    {
        $this->sdia_ref = $vo;
    }

    /**
     *
     * @return int|null $idia_num
     */
    public function getDia_num(): ?int
    {
        return $this->idia_num;
    }

    /**
     *
     * @param int|null $idia_num
     */
    public function setDia_num(?int $idia_num = null): void
    {
        $this->idia_num = $idia_num;
    }
    /**
     *
     * @return string|null $smas_menos
     */
    /**
     * @deprecated Usar `getMas_menosVo(): ?MasMenosCode` en su lugar.
     */
    public function getMas_menos(): ?string
    {
        return $this->smas_menos?->value();
    }
    /**
     *
     * @param string|null $smas_menos
     */
    /**
     * @deprecated Usar `setMas_menosVo(?MasMenosCode $vo): void` en su lugar.
     */
    public function setMas_menos(?string $smas_menos = null): void
    {
        $this->smas_menos = $smas_menos !== null ? new MasMenosCode($smas_menos) : null;
    }

    public function getMas_menosVo(): ?MasMenosCode
    {
        return $this->smas_menos;
    }

    public function setMas_menosVo(?MasMenosCode $vo): void
    {
        $this->smas_menos = $vo;
    }

    /**
     *
     * @return int|null $idia_inc
     */
    public function getDia_inc(): ?int
    {
        return $this->idia_inc;
    }

    /**
     *
     * @param int|null $idia_inc
     */
    public function setDia_inc(?int $idia_inc = null): void
    {
        $this->idia_inc = $idia_inc;
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
     * @return int|null $in_sacd
     */
    public function getN_sacd(): ?int
    {
        return $this->in_sacd;
    }

    /**
     *
     * @param int|null $in_sacd
     */
    public function setN_sacd(?int $in_sacd = null): void
    {
        $this->in_sacd = $in_sacd;
    }
    /**
     *
     * @return int|null $imes
     */
    /**
     * @deprecated Usar `getMesVo(): ?MesNum` en su lugar.
     */
    public function getMes(): ?int
    {
        return $this->imes?->value();
    }
    /**
     *
     * @param int|null $imes
     */
    /**
     * @deprecated Usar `setMesVo(?MesNum $vo): void` en su lugar.
     */
    public function setMes(?int $imes = null): void
    {
        $this->imes = $imes !== null ? new MesNum($imes) : null;
    }

    public function getMesVo(): ?MesNum
    {
        return $this->imes;
    }

    public function setMesVo(?MesNum $vo): void
    {
        $this->imes = $vo;
    }
}