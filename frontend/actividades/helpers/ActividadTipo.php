<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\helpers\ActividadTipoTwigHashCompose;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\web\Desplegable;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Widget UI del selector de tipo de actividad (sfsv / asistentes / actividad /
 * nom_tipo) con su cascada de "posibles". Sustituye al antiguo
 * `src\actividades\application\ActividadTipo`, que mezclaba lógica de UI con
 * acceso a `TipoDeActividadRepositoryInterface`.
 *
 * La resolución de "posibles" ya no toca el repositorio: se delega en
 * {@see TiposDeActividades}, que se autocarga (maps + filas) vía
 * {@see TipoActivMetadataLoader} con UNA sola request a
 * `/src/actividades/tipo_activ_metadata` por petición de página.
 *
 * La permisología (perm_oficina, jefeCalendario) y el render Twig se mantienen
 * exactamente igual que en la versión backend para que el HTML renderizado sea
 * el mismo que producía la clase original.
 */
class ActividadTipo
{
    private ?string $ssfsv = null;
    private ?string $sasistentes = null;
    private ?string $sactividad = null;
    private ?string $snom_tipo = null;
    private ?int $status = null;
    private ?string $que = null;
    private int|string|null $id_tipo_activ = null;
    private ?string $para = null;
    private bool $bperm_jefe = false;
    private bool $bAll = false;
    private ?bool $evitar_procesos = null;

    /**
     * Renderiza el desplegable y hace `echo` directo del HTML resultante (vía
     * `ViewNewTwig::renderizar`). Para obtenerlo como string usar
     * {@see self::captureHtml()}.
     */
    public function getHtml(bool $extendida = false): void
    {
        $isfsv = OrbixRuntime::miSfsv();
        $aSfsv = [1 => 'sv', 2 => 'sf'];

        if (empty($this->ssfsv)) {
            $this->ssfsv = $aSfsv[$isfsv] ?? '';
        }
        if (empty($this->status)) {
            $this->status = ActividadStatusId::ACTUAL;
        }

        if (!empty($this->id_tipo_activ)) {
            $oTipoActiv = new TiposDeActividades($this->id_tipo_activ, (bool)$extendida);
            $this->ssfsv = $oTipoActiv->getSfsvText();
            $this->sasistentes = $oTipoActiv->getAsistentesText();
            if ($extendida) {
                $this->sactividad = $oTipoActiv->getActividad2DigitosText();
            } else {
                $this->sactividad = $oTipoActiv->getActividadText();
            }
        } else {
            $oTipoActiv = new TiposDeActividades('', (bool)$extendida);
            if (!empty($this->ssfsv)) {
                $oTipoActiv->setSfsvText($this->ssfsv);
            }
            if (!empty($this->sasistentes)) {
                $oTipoActiv->setAsistentesText($this->sasistentes);
            }
            if ($extendida) {
                if (!empty($this->sactividad)) {
                    $oTipoActiv->setActividad2DigitosText($this->sactividad);
                }
            } else {
                if (!empty($this->sactividad)) {
                    $oTipoActiv->setActividadText($this->sactividad);
                }
            }
            if (!empty($this->snom_tipo)) {
                $oTipoActiv->setNom_tipoText($this->snom_tipo);
            }
            $oTipoActiv->setPosiblesAll($this->bAll);
        }

        $a_sfsv_posibles = $oTipoActiv->getSfsvPosibles();
        if ($extendida) {
            $a_actividades_posibles = $oTipoActiv->getActividadesPosibles2Digitos();
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles2Digitos();
            $val_blanco_activ = '..';
            $val_blanco_nom = '..';
        } else {
            $a_actividades_posibles = $oTipoActiv->getActividadesPosibles1Digito();
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles3Digitos();
            $val_blanco_activ = '.';
            $val_blanco_nom = '...';
        }

        $oPerm = ActividadesPermSupport::oPerm();
        $array2 = [];
        if ($oPerm !== null && $oPerm->have_perm_oficina('est')) {
            $array2 = array_merge($array2, [1 => 'n', 3 => 'agd']);
        }
        if ($oPerm !== null && $oPerm->have_perm_oficina('sm')) {
            $array2 = array_merge($array2, [1 => 'n']);
        }
        if ($oPerm !== null && $oPerm->have_perm_oficina('nax')) {
            $array2 = array_merge($array2, [1 => 'nax']);
        }
        if ($oPerm !== null && $oPerm->have_perm_oficina('agd')) {
            $array2 = array_merge($array2, [3 => 'agd']);
        }
        if ($oPerm !== null && $oPerm->have_perm_oficina('sg')) {
            $array2 = array_merge($array2, [4 => 's', 5 => 'sg']);
        }
        if ($oPerm !== null && $oPerm->have_perm_oficina('des')) {
            if ($this->status === ActividadStatusId::ACTUAL) {
                $array_des = $oTipoActiv->getAsistentesPosibles();
            } else {
                $array_des = [6 => 'sssc'];
            }
            $array2 = array_merge($array2, $array_des);
        }
        if ($oPerm !== null && $oPerm->have_perm_oficina('sr')) {
            $array2 = array_merge($array2, [7 => 'sr']);
        }
        if ($oPerm !== null && $oPerm->have_perm_oficina('calendario')) { // desde la sf
            $array2 = array_merge($array2, $oTipoActiv->getAsistentesPosibles());
        }

        // si es una búsqueda, también puedo buscar todos. (Excepto sf/sv)
        if (ActividadesPermSupport::isJefeCalendario()
            || ((isset($this->que) && $this->que === 'buscar') || $this->bperm_jefe)
        ) {
            $oTipoActivB = new TiposDeActividades('', (bool)$extendida);
            if ($this->ssfsv) {
                $oTipoActivB->setSfsvText($this->ssfsv);
            }
            $a_asistentes_posibles = $oTipoActivB->getAsistentesPosibles();
        } else {
            $oTipoActivB = new TiposDeActividades('', (bool)$extendida);
            if ($this->ssfsv) {
                $oTipoActivB->setSfsvText($this->ssfsv);
            }
            $array1 = $oTipoActivB->getAsistentesPosibles();
            $a_asistentes_posibles = array_intersect($array1, $array2);
        }

        // pasar texto a numero
        $isfsvId = $oTipoActiv->getSfsvId();
        $iasistentes = $oTipoActiv->getAsistentesId();
        $iactividad = $oTipoActiv->getActividadId();
        $inom_tipo = $oTipoActiv->getNom_tipoId();

        $oDesplSfsv = new Desplegable();
        $oDesplSfsv->setNombre('isfsv_val');
        $oDesplSfsv->setOpciones($a_sfsv_posibles);
        $oDesplSfsv->setOpcion_sel((string) $isfsvId);
        if ($this->bAll === true) {
            $oDesplSfsv->setBlanco('t');
            $oDesplSfsv->setValBlanco('.');
        }
        $oDesplSfsv->setAction('fnjs_asistentes()');

        $oDesplAsistentes = new Desplegable();
        $oDesplAsistentes->setNombre('iasistentes_val');
        $oDesplAsistentes->setOpciones($a_asistentes_posibles);
        $oDesplAsistentes->setOpcion_sel((string) $iasistentes);
        $oDesplAsistentes->setBlanco('t');
        $oDesplAsistentes->setValBlanco('.');
        $oDesplAsistentes->setAction('fnjs_actividad()');

        $oDesplActividad = new Desplegable();
        $oDesplActividad->setNombre('iactividad_val');
        $oDesplActividad->setOpciones($a_actividades_posibles);
        $oDesplActividad->setOpcion_sel($iactividad);
        $oDesplActividad->setBlanco('t');
        $oDesplActividad->setValBlanco($val_blanco_activ);
        $oDesplActividad->setAction('fnjs_nom_tipo()');

        $oDesplNomTipo = new Desplegable();
        $oDesplNomTipo->setNombre('inom_tipo_val');
        $oDesplNomTipo->setOpciones($a_nom_tipo_posibles);
        $oDesplNomTipo->setOpcion_sel((string) $inom_tipo);
        $oDesplNomTipo->setBlanco('t');
        $oDesplNomTipo->setValBlanco($val_blanco_nom);
        if ($this->que === 'buscar') {
            $oDesplNomTipo->setAction('fnjs_id_activ()');
        } else {
            $oDesplNomTipo->setAction('fnjs_act_id_activ()');
        }

        $url = rtrim(OrbixRuntime::getWeb(), '/') . '/src/actividades/actividad_tipo_get';
        $url_act = OrbixRuntime::getWeb() . '/frontend/actividades/controller/actividad_ver.php';

        if ($this->getEvitarProcesos() !== true) {
            $procesos_installed = OrbixRuntime::isAppInstalled('procesos');
        } else {
            $procesos_installed = false;
        }

        $a_campos = [
            'url' => $url,
            'url_act' => $url_act,
            'perm_jefe' => $this->bperm_jefe,
            'isfsv' => $isfsvId,
            'oDesplSfsv' => $oDesplSfsv,
            'oDesplAsistentes' => $oDesplAsistentes,
            'oDesplActividad' => $oDesplActividad,
            'oDesplNomTipo' => $oDesplNomTipo,
            'procesos_installed' => $procesos_installed,
            'extendida' => $extendida,
        ];
        $a_campos = ActividadTipoTwigHashCompose::withHashTokens($a_campos);

        $aditionalPaths = ['actividades' => 'frontend/actividades/view'];
        switch ($this->para) {
            case 'tipoactiv-tarifas':
                $oView = new ViewNewTwig('frontend/actividadtarifas/controller', $aditionalPaths);
                $oView->renderizar('actividad_tipo_que.html.twig', $a_campos);
                break;
            case 'procesos':
                $oView = new ViewNewTwig('frontend/procesos/controller', $aditionalPaths);
                $oView->renderizar('actividad_tipo_que_perm.html.twig', $a_campos);
                break;
            case 'cambios':
                $oView = new ViewNewTwig('frontend/cambios/controller', $aditionalPaths);
                $oView->renderizar('actividad_tipo_que_perm.html.twig', $a_campos);
                break;
            case 'gestion':
                $oView = new ViewNewTwig('frontend/actividades/controller', $aditionalPaths);
                $oView->renderizar('actividad_tipo_que_gestion.html.twig', $a_campos);
                break;
            case 'actividades':
            default:
                $oView = new ViewNewTwig('frontend/actividades/controller', $aditionalPaths);
                $oView->renderizar('actividad_tipo_que.html.twig', $a_campos);
        }
    }

    /**
     * Mismo efecto visual que {@see self::getHtml()} (echo vía Twig) pero como string.
     * Útil para construir vistas desde casos de uso sin que los consumidores
     * tengan que envolver la llamada en `ob_start`/`ob_get_clean`.
     */
    public function captureHtml(bool $extendida = false): string
    {
        ob_start();
        try {
            $this->getHtml($extendida);
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean() ?: '';
    }

    public function setPerm_jefe(mixed $perm_jefe): void
    {
        $this->bperm_jefe = (bool)$perm_jefe;
    }

    public function setSfsvAll(mixed $bAll = false): void
    {
        $this->bAll = (bool)$bAll;
    }

    public function setSfsv(mixed $ssfsv): void
    {
        $this->ssfsv = $ssfsv === null ? null : \frontend\shared\helpers\PayloadCoercion::string($ssfsv);
    }

    public function setAsistentes(mixed $sasistentes): void
    {
        $this->sasistentes = $sasistentes === null ? null : \frontend\shared\helpers\PayloadCoercion::string($sasistentes);
    }

    public function setActividad(mixed $sactividad): void
    {
        $this->sactividad = $sactividad === null ? null : \frontend\shared\helpers\PayloadCoercion::string($sactividad);
    }

    public function setActividad2Digitos(mixed $sactividad): void
    {
        $this->sactividad = $sactividad === null ? null : \frontend\shared\helpers\PayloadCoercion::string($sactividad);
    }

    public function setNom_tipo(mixed $snom_tipo): void
    {
        $this->snom_tipo = $snom_tipo === null ? null : \frontend\shared\helpers\PayloadCoercion::string($snom_tipo);
    }

    public function setStatus(mixed $status): void
    {
        $this->status = $status === null ? null : \frontend\shared\helpers\PayloadCoercion::int($status);
    }

    public function setQue(mixed $que): void
    {
        $this->que = $que === null ? null : \frontend\shared\helpers\PayloadCoercion::string($que);
    }

    public function setId_tipo_activ(mixed $id_tipo_activ): void
    {
        if ($id_tipo_activ === null) {
            $this->id_tipo_activ = null;
        } elseif (is_int($id_tipo_activ) || is_string($id_tipo_activ)) {
            $this->id_tipo_activ = $id_tipo_activ;
        } else {
            $this->id_tipo_activ = \frontend\shared\helpers\PayloadCoercion::string($id_tipo_activ);
        }
    }

    public function setPara(mixed $para = 'actividades'): void
    {
        $this->para = $para === null ? null : \frontend\shared\helpers\PayloadCoercion::string($para);
    }

    public function getEvitarProcesos(): ?bool
    {
        return $this->evitar_procesos;
    }

    /**
     * Indica si se debe ignorar la app `procesos` aunque esté instalada
     * (la plantilla la usa para decidir si pinta el bloque de fases).
     */
    public function setEvitarProcesos(mixed $evitar_procesos): void
    {
        $this->evitar_procesos = $evitar_procesos === null ? null : (bool)$evitar_procesos;
    }
}
