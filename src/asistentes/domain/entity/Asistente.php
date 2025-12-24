<?php

namespace src\asistentes\domain\entity;

use core\ConfigGlobal;
use src\actividadplazas\domain\GestorResumenPlazas;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\shared\domain\events\EntidadModificada;
use src\shared\domain\traits\EmitsDomainEvents;
use function core\is_true;

/**
 * Clase que implementa la entidad d_asistentes_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class Asistente
{
    use EmitsDomainEvents;
    /**
     * Saber si puedo modificar.
     * - true para asistentes de mi dl, y para los de paso que he puesto yo
     * - false para asistentes de otra dl, y para los de paso que NO he puesto yo
     *
     * @return boolean
     */
    public function perm_modificar()
    {
        return $this->getDl_responsable() === ConfigGlobal::mi_delef();
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_activ de Asistente
     *
     * @var int
     */
    private int $iid_activ;
    /**
     * Id_nom de Asistente
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Propio de Asistente
     *
     * @var bool
     */
    private bool $bpropio;
    /**
     * Est_ok de Asistente
     *
     * @var bool
     */
    private bool $best_ok;
    /**
     * Cfi de Asistente
     *
     * @var bool
     */
    private bool $bcfi;
    /**
     * Cfi_con de Asistente
     *
     * @var int|null
     */
    private int|null $icfi_con = null;
    /**
     * Falta de Asistente
     *
     * @var bool
     */
    private bool $bfalta;
    /**
     * Encargo de Asistente
     *
     * @var string|null
     */
    private string|null $sencargo = null;
    /**
     * Dl_responsable de Asistente
     *
     * @var string|null
     */
    private string|null $sdl_responsable = null;
    /**
     * Observ de Asistente
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * Id_tabla de Asistente
     *
     * @var string|null
     */
    private string|null $sid_tabla = null;
    /**
     * Plaza de Asistente
     *
     * @var int|null
     */
    private int|null $iplaza = null;
    /**
     * Propietario de Asistente
     *
     * @var string|null
     */
    private string|null $spropietario = null;
    /**
     * Observ_est de Asistente
     *
     * @var string|null
     */
    private string|null $sobserv_est = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Asistente
     */
    public function setAllAttributes(array $aDatos): Asistente
    {
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('propio', $aDatos)) {
            $this->setPropio(is_true($aDatos['propio']));
        }
        if (array_key_exists('est_ok', $aDatos)) {
            $this->setEst_ok(is_true($aDatos['est_ok']));
        }
        if (array_key_exists('cfi', $aDatos)) {
            $this->setCfi(is_true($aDatos['cfi']));
        }
        if (array_key_exists('cfi_con', $aDatos)) {
            $this->setCfi_con($aDatos['cfi_con']);
        }
        if (array_key_exists('falta', $aDatos)) {
            $this->setFalta(is_true($aDatos['falta']));
        }
        if (array_key_exists('encargo', $aDatos)) {
            $this->setEncargo($aDatos['encargo']);
        }
        if (array_key_exists('dl_responsable', $aDatos)) {
            $this->setDl_responsable($aDatos['dl_responsable']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('id_tabla', $aDatos)) {
            $this->setId_tabla($aDatos['id_tabla']);
        }
        if (array_key_exists('plaza', $aDatos)) {
            $this->setPlaza($aDatos['plaza']);
        }
        if (array_key_exists('propietario', $aDatos)) {
            $this->setPropietario($aDatos['propietario']);
        }
        if (array_key_exists('observ_est', $aDatos)) {
            $this->setObserv_est($aDatos['observ_est']);
        }
        return $this;
    }

    /**
     * Marca esta entidad como nueva (INSERT) y emite el evento correspondiente
     *
     * @param array $datosActuales Datos actuales de la entidad (opcional para contexto)
     * @return void
     */
    public function marcarComoNueva(array $datosActuales = []): void
    {
        $datosNuevos = $this->toArray();

        $this->recordEvent(new EntidadModificada(
            objeto: 'Asistente',
            tipoCambio: 'INSERT',
            idActiv: $this->iid_activ,
            datosNuevos: $datosNuevos,
            datosActuales: $datosActuales
        ));
    }

    /**
     * Marca esta entidad como modificada (UPDATE) y emite el evento correspondiente
     *
     * @param array $datosActuales Datos anteriores antes de la modificación
     * @return void
     */
    public function marcarComoModificada(array $datosActuales): void
    {
        $datosNuevos = $this->toArray();

        $this->recordEvent(new EntidadModificada(
            objeto: 'Asistente',
            tipoCambio: 'UPDATE',
            idActiv: $this->iid_activ,
            datosNuevos: $datosNuevos,
            datosActuales: $datosActuales
        ));
    }

    /**
     * Marca esta entidad como eliminada (DELETE) y emite el evento correspondiente
     *
     * @param array $datosActuales Datos actuales antes de eliminar
     * @return void
     */
    public function marcarComoEliminada(array $datosActuales): void
    {
        $this->recordEvent(new EntidadModificada(
            objeto: 'Asistente',
            tipoCambio: 'DELETE',
            idActiv: $this->iid_activ,
            datosNuevos: [],
            datosActuales: $datosActuales
        ));
    }

    /**
     * Convierte la entidad a un array asociativo
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id_activ' => $this->iid_activ,
            'id_nom' => $this->iid_nom,
            'propio' => $this->bpropio,
            'est_ok' => $this->best_ok,
            'cfi' => $this->bcfi,
            'cfi_con' => $this->icfi_con,
            'falta' => $this->bfalta,
            'encargo' => $this->sencargo,
            'dl_responsable' => $this->sdl_responsable,
            'observ' => $this->sobserv,
            'id_tabla' => $this->sid_tabla,
            'plaza' => $this->iplaza,
            'propietario' => $this->spropietario,
            'observ_est' => $this->sobserv_est,
        ];
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
     * @return bool $bpropio
     */
    public function isPropio(): bool
    {
        return $this->bpropio;
    }

    /**
     *
     * @param bool $bpropio
     */
    public function setPropio(bool $bpropio): void
    {
        $this->bpropio = $bpropio;
    }

    /**
     *
     * @return bool $best_ok
     */
    public function isEst_ok(): bool
    {
        return $this->best_ok;
    }

    /**
     *
     * @param bool $best_ok
     */
    public function setEst_ok(bool $best_ok): void
    {
        $this->best_ok = $best_ok;
    }

    /**
     *
     * @return bool $bcfi
     */
    public function isCfi(): bool
    {
        return $this->bcfi;
    }

    /**
     *
     * @param bool $bcfi
     */
    public function setCfi(bool $bcfi): void
    {
        $this->bcfi = $bcfi;
    }

    /**
     *
     * @return int|null $icfi_con
     */
    public function getCfi_con(): ?int
    {
        return $this->icfi_con;
    }

    /**
     *
     * @param int|null $icfi_con
     */
    public function setCfi_con(?int $icfi_con = null): void
    {
        $this->icfi_con = $icfi_con;
    }

    /**
     *
     * @return bool $bfalta
     */
    public function isFalta(): bool
    {
        return $this->bfalta;
    }

    /**
     *
     * @param bool $bfalta
     */
    public function setFalta(bool $bfalta): void
    {
        $this->bfalta = $bfalta;
    }

    /**
     *
     * @return string|null $sencargo
     */
    public function getEncargo(): ?string
    {
        return $this->sencargo;
    }

    /**
     *
     * @param string|null $sencargo
     */
    public function setEncargo(?string $sencargo = null): void
    {
        $this->sencargo = $sencargo;
    }

    /**
     *
     * @return string|null $sdl_responsable
     */
    public function getDl_responsable(): ?string
    {
        return $this->sdl_responsable;
    }

    /**
     *
     * @param string|null $sdl_responsable
     */
    public function setDl_responsable(?string $sdl_responsable = null): void
    {
        $this->sdl_responsable = $sdl_responsable;
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
     * @return string|null $sid_tabla
     */
    public function getId_tabla(): ?string
    {
        return $this->sid_tabla;
    }

    /**
     *
     * @param string|null $sid_tabla
     */
    public function setId_tabla(?string $sid_tabla = null): void
    {
        $this->sid_tabla = $sid_tabla;
    }

    /**
     *
     * @return int|null $iplaza
     */
    public function getPlaza(): ?int
    {
        return $this->iplaza;
    }

    /**
     *
     * @param int|null $iplaza
     */
    public function setPlaza(?int $iplaza = null): void
    {
        $this->iplaza = $iplaza;
    }
    /**
     * No puede estar en setPlaza, porque cuando se hidrata con la DB entra en un bucle infinito
     * @param int|null $iplaza
     */
    public function setPlazaComprobando(?int $iplaza = null): void
    {
        // tipos de actividad para los que no hay que comprobar la plaza
        // 132500 => agd ca sem invierno
        //$aId_tipo_activ_no = [132500,00000];
        //$oActividad = new Actividad($this->iid_activ);
        //$id_tipo_activ = $oActividad->getId_tipo_activ();
        //if (in_array($id_tipo_activ, $aId_tipo_activ_no)) {
        //	return $this->setPlazaSinComprobar($iplaza);
        //}

        //hacer comprobaciones de plazas disponibles...
        $plaza_actual = $this->getPlaza();

        if ($plaza_actual < PlazaId::DENEGADA && $iplaza > PlazaId::DENEGADA) {
            $this->iplaza = $iplaza;
            $gesActividadPlazasR = new GestorResumenPlazas();
            $gesActividadPlazasR->setId_activ($this->iid_activ);
            if ($gesActividadPlazasR->getLibres() > 0) {
                //debe asignarse un propietario. Sólo si es asignada o confirmada
                $rta = $gesActividadPlazasR->getPropiedadPlazaLibre();
                if ($rta['success']) {
                    $propiedad = $rta['propiedad'];
                    if (empty($propiedad)) {
                        exit (_("no debería pasar. No puede haber una plaza libre sin propietario"));
                    } else {
                        $prop = key($propiedad);
                        $this->setPropietario($prop);
                    }
                } else {
                    $err_txt = $rta['mensaje'];
                    exit ($err_txt);
                }
            } else {
                $this->iplaza = PlazaId::PEDIDA;
            }
        } else {
            $this->iplaza = $iplaza;
        }
    }

    /**
     *
     * @return string|null $spropietario
     */
    public function getPropietario(): ?string
    {
        return $this->spropietario;
    }

    /**
     *
     * @param string|null $spropietario
     */
    public function setPropietario(?string $spropietario = null): void
    {
        $this->spropietario = $spropietario;
    }

    /**
     *
     * @return string|null $sobserv_est
     */
    public function getObserv_est(): ?string
    {
        return $this->sobserv_est;
    }

    /**
     *
     * @param string|null $sobserv_est
     */
    public function setObserv_est(?string $sobserv_est = null): void
    {
        $this->sobserv_est = $sobserv_est;
    }
}