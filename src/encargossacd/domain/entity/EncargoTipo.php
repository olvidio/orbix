<?php

namespace src\encargossacd\domain\entity;
use core\DatosCampo;
use core\Set;
use src\encargossacd\domain\value_objects\EncargoTipoId;
use src\encargossacd\domain\value_objects\EncargoTipoText;
use src\encargossacd\domain\value_objects\EncargoModHorarioId;
use src\shared\domain\traits\Hydratable;

class EncargoTipo
{
    use Hydratable;

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


    private EncargoTipoId $id_tipo_enc;

    private EncargoTipoText $tipo_enc;

    private EncargoModHorarioId $mod_horario;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated Usar `getTipoEncIdVo(): EncargoTipoId` en su lugar.
     */
    public function getId_tipo_enc(): int
    {
        return $this->id_tipo_enc->value();
    }


    /**
     * @deprecated Usar `setTipoEncIdVo(EncargoTipoId $vo): void` en su lugar.
     */
    public function setId_tipo_enc(int $id_tipo_enc): void
    {
        $this->id_tipo_enc = new EncargoTipoId($id_tipo_enc);
    }

    public function getTipoEncIdVo(): EncargoTipoId
    {
        return $this->id_tipo_enc;
    }

    public function setTipoEncIdVo(EncargoTipoId $vo): void
    {
        $this->id_tipo_enc = $vo;
    }


    /**
     * @deprecated Usar `getTipoEncVo(): EncargoTipoText` en su lugar.
     */
    public function getTipo_enc(): string
    {
        return $this->tipo_enc->value();
    }


    /**
     * @deprecated Usar `setTipoEncVo(EncargoTipoText $vo): void` en su lugar.
     */
    public function setTipo_enc(string $tipo_enc): void
    {
        $this->tipo_enc = new EncargoTipoText($tipo_enc);
    }

    public function getTipoEncVo(): EncargoTipoText
    {
        return $this->tipo_enc;
    }

    public function setTipoEncVo(EncargoTipoText $vo): void
    {
        $this->tipo_enc = $vo;
    }


    /**
     * @deprecated Usar `getModHorarioVo(): EncargoModHorarioId` en su lugar.
     */
    public function getMod_horario(): int
    {
        return $this->mod_horario->value();
    }


    /**
     * @deprecated Usar `setModHorarioVo(EncargoModHorarioId $vo): void` en su lugar.
     */
    public function setMod_horario(int $mod_horario): void
    {
        $this->mod_horario = new EncargoModHorarioId($mod_horario);
    }

    public function getModHorarioVo(): EncargoModHorarioId
    {
        return $this->mod_horario;
    }

    public function setModHorarioVo(EncargoModHorarioId $vo): void
    {
        $this->mod_horario = $vo;
    }


    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo_enc';
    }

  public function getDatosCampos(): array
    {
        $oEncargoTipoSet = new Set();
        $oEncargoTipoSet->add($this->getDatosId_tipo_enc());
        $oEncargoTipoSet->add($this->getDatosTipo_enc());
        $oEncargoTipoSet->add($this->getDatosMod_horario());
        return $oEncargoTipoSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo tipo_enc de EncargoTipo
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_tipo_enc(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_enc');
        $oDatosCampo->setMetodoGet('getId_tipo_enc');
        $oDatosCampo->setMetodoSet('setId_tipo_enc');
        $oDatosCampo->setEtiqueta(_("id tipo de encargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo tipo_enc de EncargoTipo
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTipo_enc(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_enc');
        $oDatosCampo->setMetodoGet('getTipo_enc');
        $oDatosCampo->setMetodoSet('setTipo_enc');
        $oDatosCampo->setEtiqueta(_("tipo de encargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo mod_horario de EncargoTipo
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosMod_horario(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('mod_horario');
        $oDatosCampo->setMetodoGet('getMod_horario');
        $oDatosCampo->setMetodoSet('setMod_horario');
        $oDatosCampo->setEtiqueta(_("tipo de horario"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista(EncargoModHorarioId::ARRAY_HORARIO_TXT);

        return $oDatosCampo;
    }
}