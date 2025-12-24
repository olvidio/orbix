<?php

namespace src\encargossacd\domain\entity;

use web\DateTimeLocal;
use web\NullDateTimeLocal;
use web\NullTimeLocal;
use web\TimeLocal;

/**
 * Clase que implementa la entidad propuesta_encargo_sacd_horario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class EncargoSacdHorario
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de EncargoSacdHorario
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_enc de EncargoSacdHorario
     *
     * @var int
     */
    private int $iid_enc;
    /**
     * Id_nom de EncargoSacdHorario
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * F_ini de EncargoSacdHorario
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_ini = null;
    /**
     * F_fin de EncargoSacdHorario
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_fin = null;
    /**
     * Dia_ref de EncargoSacdHorario
     *
     * @var string|null
     */
    private string|null $sdia_ref = null;
    /**
     * Dia_num de EncargoSacdHorario
     *
     * @var int|null
     */
    private int|null $idia_num = null;
    /**
     * Mas_menos de EncargoSacdHorario
     *
     * @var string|null
     */
    private string|null $smas_menos = null;
    /**
     * Dia_inc de EncargoSacdHorario
     *
     * @var int|null
     */
    private int|null $idia_inc = null;
    /**
     * H_ini de EncargoSacdHorario
     *
     * @var TimeLocal|null
     */
    private TimeLocal|null $th_ini = null;
    /**
     * H_fin de EncargoSacdHorario
     *
     * @var TimeLocal|null
     */
    private TimeLocal|null $th_fin = null;
    /**
     * Id_item_tarea_sacd de EncargoSacdHorario
     *
     * @var int|null
     */
    private int|null $iid_item_tarea_sacd = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoSacdHorario
     */
    public function setAllAttributes(array $aDatos): EncargoSacdHorario
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
        if (array_key_exists('id_item_tarea_sacd', $aDatos)) {
            $this->setId_item_tarea_sacd($aDatos['id_item_tarea_sacd']);
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
    public function getDia_ref(): ?string
    {
        return $this->sdia_ref;
    }

    /**
     *
     * @param string|null $sdia_ref
     */
    public function setDia_ref(?string $sdia_ref = null): void
    {
        $this->sdia_ref = $sdia_ref;
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
    public function getMas_menos(): ?string
    {
        return $this->smas_menos;
    }

    /**
     *
     * @param string|null $smas_menos
     */
    public function setMas_menos(?string $smas_menos = null): void
    {
        $this->smas_menos = $smas_menos;
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
     * @return int|null $iid_item_tarea_sacd
     */
    public function getId_item_tarea_sacd(): ?int
    {
        return $this->iid_item_tarea_sacd;
    }

    /**
     *
     * @param int|null $iid_item_tarea_sacd
     */
    public function setId_item_tarea_sacd(?int $iid_item_tarea_sacd = null): void
    {
        $this->iid_item_tarea_sacd = $iid_item_tarea_sacd;
    }
}