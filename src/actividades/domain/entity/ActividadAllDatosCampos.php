<?php

namespace src\actividades\domain\entity;

use src\actividades\domain\value_objects\StatusId;
use src\shared\domain\DatosCampo;

/**
 * Metadatos de campos avisables para {@see ActividadAll}.
 *
 * Portado desde la legacy `classes/production/orbix/actividades/model/entity/ActividadAll.php`.
 */
trait ActividadAllDatosCampos
{
    /**
     * @return list<DatosCampo>
     */
    public function getDatosCampos(): array
    {
        return [
            $this->datosCampoIdTipoActiv(),
            $this->datosCampoDlOrg(),
            $this->datosCampoNomActiv(),
            $this->datosCampoIdUbi(),
            $this->datosCampoFIni(),
            $this->datosCampoHIni(),
            $this->datosCampoFFin(),
            $this->datosCampoHFin(),
            $this->datosCampoPrecio(),
            $this->datosCampoNumAsistentes(),
            $this->datosCampoStatus(),
            $this->datosCampoObserv(),
            $this->datosCampoNivelStgr(),
            $this->datosCampoObservMaterial(),
            $this->datosCampoLugarEsp(),
            $this->datosCampoTarifa(),
            $this->datosCampoIdRepeticion(),
            $this->datosCampoIdTabla(),
            $this->datosCampoPlazas(),
            $this->datosCampoPublicado(),
        ];
    }

    private function actividadAllNomTabla(): string
    {
        return 'a_actividades_all';
    }

    private function datosCampoIdTipoActiv(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'id_tipo_activ']);
        $c->setEtiqueta(_('id_tipo_activ'));
        $c->setAviso(false);

        return $c;
    }

    private function datosCampoDlOrg(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'dl_org']);
        $c->setEtiqueta(_('dl_org'));
        $c->setAviso(false);

        return $c;
    }

    private function datosCampoNomActiv(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'nom_activ']);
        $c->setEtiqueta(_('nombre actividad'));

        return $c;
    }

    private function datosCampoIdUbi(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'id_ubi']);
        $c->setEtiqueta(_('id del Lugar'));

        return $c;
    }

    private function datosCampoFIni(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'f_ini']);
        $c->setEtiqueta(_('fecha inicio'));
        $c->setTipo('fecha');

        return $c;
    }

    private function datosCampoHIni(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'h_ini']);
        $c->setEtiqueta(_('hora inicio'));

        return $c;
    }

    private function datosCampoFFin(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'f_fin']);
        $c->setEtiqueta(_('fecha fin'));
        $c->setTipo('fecha');

        return $c;
    }

    private function datosCampoHFin(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'h_fin']);
        $c->setEtiqueta(_('hora fin'));

        return $c;
    }

    private function datosCampoPrecio(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'precio']);
        $c->setEtiqueta(_('precio'));
        $c->setRegExp('/^(\d+)[,.]?\d{0,2}$/');
        $txt = _('tiene un formato no válido.');
        $txt .= "\n";
        $txt .= _('se admite un separador para los decimales (máximo 2)');
        $txt .= "\n";
        $txt .= _('no se admite separador para los miles');
        $txt .= "\n";
        $txt .= _('ejemplo: 1254.56');
        $c->setRegExpText($txt);

        return $c;
    }

    private function datosCampoNumAsistentes(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'num_asistentes']);
        $c->setEtiqueta(_('número de asistentes'));
        $c->setAviso(false);

        return $c;
    }

    private function datosCampoStatus(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'status']);
        $c->setEtiqueta(_('status'));
        $c->setTipo('array');
        $c->setLista(StatusId::getArrayStatus(true));

        return $c;
    }

    private function datosCampoObserv(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'observ']);
        $c->setEtiqueta(_('observaciones'));

        return $c;
    }

    private function datosCampoNivelStgr(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'nivel_stgr']);
        $c->setEtiqueta(_('nivel de stgr'));

        return $c;
    }

    private function datosCampoObservMaterial(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'observ_material']);
        $c->setEtiqueta(_('observaciones material'));

        return $c;
    }

    private function datosCampoLugarEsp(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'lugar_esp']);
        $c->setEtiqueta(_('lugar especial'));

        return $c;
    }

    private function datosCampoTarifa(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'tarifa']);
        $c->setEtiqueta(_('tarifa'));

        return $c;
    }

    private function datosCampoIdRepeticion(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'id_repeticion']);
        $c->setEtiqueta(_('id repeticion'));

        return $c;
    }

    private function datosCampoIdTabla(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'id_tabla']);
        $c->setEtiqueta(_('id_tabla'));
        $c->setAviso(false);

        return $c;
    }

    private function datosCampoPlazas(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'plazas']);
        $c->setEtiqueta(_('plazas'));

        return $c;
    }

    private function datosCampoPublicado(): DatosCampo
    {
        $c = new DatosCampo(['nom_tabla' => $this->actividadAllNomTabla(), 'nom_camp' => 'publicado']);
        $c->setEtiqueta(_('publicado'));

        return $c;
    }
}
