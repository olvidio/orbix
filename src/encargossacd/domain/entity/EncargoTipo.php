<?php

namespace src\encargossacd\domain\entity;
use src\encargossacd\domain\value_objects\EncargoTipoId;
use src\encargossacd\domain\value_objects\EncargoTipoText;
use src\encargossacd\domain\value_objects\EncargoModHorarioId;

/**
 * Clase que implementa la entidad encargo_tipo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class EncargoTipo
{
    // NO se usan, son solo para asegurar que exista la traducción
    private function traduccion()
    {
        $p = _("opcional");
        $a = _("módulos");
        $t = _("día y hora");
        $txt = _("ctr") .
            _("cgi") .
            _("igl") .
            _("stgr") .
            _("estudio/descanso") .
            _("otros") .
            _("personales") .
            _("Zona Misas");

        return $p . $a . $t . $txt;
    }

    //definición de variables globales para las funciones de tipo de encargo
    const GRUPO = [
        1 => "ctr",
        2 => "cgi",
        3 => "igl",
        4 => "stgr",
        5 => "estudio/descanso",
        6 => "otros",
        7 => "personales",
        8 => "Zona Misas",
    ];

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_tipo_enc de EncargoTipo
     *
     * @var EncargoTipoId
     */
    private EncargoTipoId $iid_tipo_enc;
    /**
     * Tipo_enc de EncargoTipo
     *
     * @var EncargoTipoText
     */
    private EncargoTipoText $stipo_enc;
    /**
     * Mod_horario de EncargoTipo
     *
     * @var EncargoModHorarioId
     */
    private EncargoModHorarioId $imod_horario;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoTipo
     */
    public function setAllAttributes(array $aDatos): EncargoTipo
    {
        if (array_key_exists('id_tipo_enc', $aDatos)) {
            $this->setId_tipo_enc($aDatos['id_tipo_enc']);
        }
        if (array_key_exists('tipo_enc', $aDatos)) {
            $this->setTipo_enc($aDatos['tipo_enc']);
        }
        if (array_key_exists('mod_horario', $aDatos)) {
            $this->setMod_horario($aDatos['mod_horario']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_tipo_enc
     */
    /**
     * @deprecated Usar `getTipo_enc_idVo(): EncargoTipoId` en su lugar.
     */
    public function getId_tipo_enc(): int
    {
        return $this->iid_tipo_enc->value();
    }

    /**
     *
     * @param int $iid_tipo_enc
     */
    /**
     * @deprecated Usar `setTipo_enc_idVo(EncargoTipoId $vo): void` en su lugar.
     */
    public function setId_tipo_enc(int $iid_tipo_enc): void
    {
        $this->iid_tipo_enc = new EncargoTipoId($iid_tipo_enc);
    }

    public function getTipo_enc_idVo(): EncargoTipoId
    {
        return $this->iid_tipo_enc;
    }

    public function setTipo_enc_idVo(EncargoTipoId $vo): void
    {
        $this->iid_tipo_enc = $vo;
    }

    /**
     *
     * @return string $stipo_enc
     */
    /**
     * @deprecated Usar `getTipo_encVo(): EncargoTipoText` en su lugar.
     */
    public function getTipo_enc(): string
    {
        return $this->stipo_enc->value();
    }

    /**
     *
     * @param string $stipo_enc
     */
    /**
     * @deprecated Usar `setTipo_encVo(EncargoTipoText $vo): void` en su lugar.
     */
    public function setTipo_enc(string $stipo_enc): void
    {
        $this->stipo_enc = new EncargoTipoText($stipo_enc);
    }

    public function getTipo_encVo(): EncargoTipoText
    {
        return $this->stipo_enc;
    }

    public function setTipo_encVo(EncargoTipoText $vo): void
    {
        $this->stipo_enc = $vo;
    }

    /**
     *
     * @return int $imod_horario
     */
    /**
     * @deprecated Usar `getMod_horarioVo(): EncargoModHorarioId` en su lugar.
     */
    public function getMod_horario(): int
    {
        return $this->imod_horario->value();
    }

    /**
     *
     * @param int $imod_horario
     */
    /**
     * @deprecated Usar `setMod_horarioVo(EncargoModHorarioId $vo): void` en su lugar.
     */
    public function setMod_horario(int $imod_horario): void
    {
        $this->imod_horario = new EncargoModHorarioId($imod_horario);
    }

    public function getMod_horarioVo(): EncargoModHorarioId
    {
        return $this->imod_horario;
    }

    public function setMod_horarioVo(EncargoModHorarioId $vo): void
    {
        $this->imod_horario = $vo;
    }
}