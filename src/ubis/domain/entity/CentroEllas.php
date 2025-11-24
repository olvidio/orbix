<?php

namespace src\ubis\domain\entity;

use src\ubis\application\services\UbiContactsTrait;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad cu_centros_dlf
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class CentroEllas
{
    // Esto inyecta los métodos getDirecciones, emailPrincipalOPrimero y getTeleco aquí
    use UbiContactsTrait;

    protected $repoCasaDireccion;
    protected $repoDireccion;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_ubi de CentroEllas
     *
     * @var int
     */
    private int $iid_ubi;
    /**
     * Tipo_ubi de CentroEllas
     *
     * @var string|null
     */
    private string|null $stipo_ubi = null;
    /**
     * Nombre_ubi de CentroEllas
     *
     * @var string
     */
    private string $snombre_ubi;
    /**
     * Dl de CentroEllas
     *
     * @var string|null
     */
    private string|null $sdl = null;
    /**
     * Pais de CentroEllas
     *
     * @var string|null
     */
    private string|null $spais = null;
    /**
     * Region de CentroEllas
     *
     * @var string|null
     */
    private string|null $sregion = null;
    /**
     * Status de CentroEllas
     *
     * @var bool
     */
    private bool $bstatus;
    /**
     * F_status de CentroEllas
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_status = null;
    /**
     * Sv de CentroEllas
     *
     * @var bool|null
     */
    private bool|null $bsv = null;
    /**
     * Sf de CentroEllas
     *
     * @var bool|null
     */
    private bool|null $bsf = null;
    /**
     * Tipo_ctr de CentroEllas
     *
     * @var string|null
     */
    private string|null $stipo_ctr = null;
    /**
     * Tipo_labor de CentroEllas
     *
     * @var int|null
     */
    private int|null $itipo_labor = null;
    /**
     * Cdc de CentroEllas
     *
     * @var bool|null
     */
    private bool|null $bcdc = null;
    /**
     * Id_ctr_padre de CentroEllas
     *
     * @var int|null
     */
    private int|null $iid_ctr_padre = null;
    /**
     * Id_zona de CentroEllas
     *
     * @var int|null
     */
    private int|null $iid_zona = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return CentroEllas
     */
    public function setAllAttributes(array $aDatos): CentroEllas
    {
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_ubi($aDatos['id_ubi']);
        }
        if (array_key_exists('tipo_ubi', $aDatos)) {
            $this->setTipo_ubi($aDatos['tipo_ubi']);
        }
        if (array_key_exists('nombre_ubi', $aDatos)) {
            $this->setNombre_ubi($aDatos['nombre_ubi']);
        }
        if (array_key_exists('dl', $aDatos)) {
            $this->setDl($aDatos['dl']);
        }
        if (array_key_exists('pais', $aDatos)) {
            $this->setPais($aDatos['pais']);
        }
        if (array_key_exists('region', $aDatos)) {
            $this->setRegion($aDatos['region']);
        }
        if (array_key_exists('status', $aDatos)) {
            $this->setStatus(is_true($aDatos['status']));
        }
        if (array_key_exists('f_status', $aDatos)) {
            $this->setF_status($aDatos['f_status']);
        }
        if (array_key_exists('sv', $aDatos)) {
            $this->setSv(is_true($aDatos['sv']));
        }
        if (array_key_exists('sf', $aDatos)) {
            $this->setSf(is_true($aDatos['sf']));
        }
        if (array_key_exists('tipo_ctr', $aDatos)) {
            $this->setTipo_ctr($aDatos['tipo_ctr']);
        }
        if (array_key_exists('tipo_labor', $aDatos)) {
            $this->setTipo_labor($aDatos['tipo_labor']);
        }
        if (array_key_exists('cdc', $aDatos)) {
            $this->setCdc(is_true($aDatos['cdc']));
        }
        if (array_key_exists('id_ctr_padre', $aDatos)) {
            $this->setId_ctr_padre($aDatos['id_ctr_padre']);
        }
        if (array_key_exists('id_zona', $aDatos)) {
            $this->setId_zona($aDatos['id_zona']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_ubi
     */
    public function getId_ubi(): int
    {
        return $this->iid_ubi;
    }

    /**
     *
     * @param int $iid_ubi
     */
    public function setId_ubi(int $iid_ubi): void
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     *
     * @return string|null $stipo_ubi
     */
    public function getTipo_ubi(): ?string
    {
        return $this->stipo_ubi;
    }

    /**
     *
     * @param string|null $stipo_ubi
     */
    public function setTipo_ubi(?string $stipo_ubi = null): void
    {
        $this->stipo_ubi = $stipo_ubi;
    }

    /**
     *
     * @return string $snombre_ubi
     */
    public function getNombre_ubi(): string
    {
        return $this->snombre_ubi;
    }

    /**
     *
     * @param string $snombre_ubi
     */
    public function setNombre_ubi(string $snombre_ubi): void
    {
        $this->snombre_ubi = $snombre_ubi;
    }

    /**
     *
     * @return string|null $sdl
     */
    public function getDl(): ?string
    {
        return $this->sdl;
    }

    /**
     *
     * @param string|null $sdl
     */
    public function setDl(?string $sdl = null): void
    {
        $this->sdl = $sdl;
    }

    /**
     *
     * @return string|null $spais
     */
    public function getPais(): ?string
    {
        return $this->spais;
    }

    /**
     *
     * @param string|null $spais
     */
    public function setPais(?string $spais = null): void
    {
        $this->spais = $spais;
    }

    /**
     *
     * @return string|null $sregion
     */
    public function getRegion(): ?string
    {
        return $this->sregion;
    }

    /**
     *
     * @param string|null $sregion
     */
    public function setRegion(?string $sregion = null): void
    {
        $this->sregion = $sregion;
    }

    /**
     *
     * @return bool $bstatus
     */
    public function isStatus(): bool
    {
        return $this->bstatus;
    }

    /**
     *
     * @param bool $bstatus
     */
    public function setStatus(bool $bstatus): void
    {
        $this->bstatus = $bstatus;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_status
     */
    public function getF_status(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_status ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_status
     */
    public function setF_status(DateTimeLocal|null $df_status = null): void
    {
        $this->df_status = $df_status;
    }

    /**
     *
     * @return bool|null $bsv
     */
    public function isSv(): ?bool
    {
        return $this->bsv;
    }

    /**
     *
     * @param bool|null $bsv
     */
    public function setSv(?bool $bsv = null): void
    {
        $this->bsv = $bsv;
    }

    /**
     *
     * @return bool|null $bsf
     */
    public function isSf(): ?bool
    {
        return $this->bsf;
    }

    /**
     *
     * @param bool|null $bsf
     */
    public function setSf(?bool $bsf = null): void
    {
        $this->bsf = $bsf;
    }

    /**
     *
     * @return string|null $stipo_ctr
     */
    public function getTipo_ctr(): ?string
    {
        return $this->stipo_ctr;
    }

    /**
     *
     * @param string|null $stipo_ctr
     */
    public function setTipo_ctr(?string $stipo_ctr = null): void
    {
        $this->stipo_ctr = $stipo_ctr;
    }

    /**
     *
     * @return int|null $itipo_labor
     */
    public function getTipo_labor(): ?int
    {
        return $this->itipo_labor;
    }

    /**
     *
     * @param int|null $itipo_labor
     */
    public function setTipo_labor(?int $itipo_labor = null): void
    {
        $this->itipo_labor = $itipo_labor;
    }

    /**
     *
     * @return bool|null $bcdc
     */
    public function isCdc(): ?bool
    {
        return $this->bcdc;
    }

    /**
     *
     * @param bool|null $bcdc
     */
    public function setCdc(?bool $bcdc = null): void
    {
        $this->bcdc = $bcdc;
    }

    /**
     *
     * @return int|null $iid_ctr_padre
     */
    public function getId_ctr_padre(): ?int
    {
        return $this->iid_ctr_padre;
    }

    /**
     *
     * @param int|null $iid_ctr_padre
     */
    public function setId_ctr_padre(?int $iid_ctr_padre = null): void
    {
        $this->iid_ctr_padre = $iid_ctr_padre;
    }

    /**
     *
     * @return int|null $iid_zona
     */
    public function getId_zona(): ?int
    {
        return $this->iid_zona;
    }

    /**
     *
     * @param int|null $iid_zona
     */
    public function setId_zona(?int $iid_zona = null): void
    {
        $this->iid_zona = $iid_zona;
    }
    /* MÉTODOS PARA GESTIÓN DE DIRECCIONES ----------------------------------------*/

    /**
     * Obtiene Una direccione con sus metadatos (principal, propietario)
     *
     * @return array<DireccionDetalle>
     */
    public function getUnaDireccionDetallada(int $id_direccion): ?DireccionDetalle
    {
        $relaciones = $this->repoCasaDireccion->getRelacionesPorUbi($this->getId_ubi());
        $direcciDetallada = null;

        foreach ($relaciones as $row) {
            if ($id_direccion !== $row['id_direccion']) {
                continue;
            }
            $direccion = $this->repoDireccion->findById($row['id_direccion']);
            if ($direccion !== null) {
                // Creamos el objeto intermedio con los booleanos de la DB
                // Aseguramos conversión a bool explícita
                $esPrincipal = is_true($row['principal']);
                $esPropietario = is_true($row['propietario']);

                $direccionDetallada = new DireccionDetalle(
                    $direccion,
                    $esPrincipal,
                    $esPropietario
                );
            }
        }

        return $direccionDetallada;
    }

    /**
     * Obtiene las direcciones con sus metadatos (principal, propietario)
     *
     * @return array<DireccionDetalle>
     */
    public function getDireccionesDetalladas(): array
    {

        $relaciones = $this->repoCasaDireccion->getRelacionesPorUbi($this->getId_ubi());
        $direccionesDetalladas = [];

        foreach ($relaciones as $row) {
            $direccion = $this->repoDireccion->findById($row['id_direccion']);
            if ($direccion !== null) {
                // Creamos el objeto intermedio con los booleanos de la DB
                // Aseguramos conversión a bool explícita
                $esPrincipal = is_true($row['principal']);
                $esPropietario = is_true($row['propietario']);

                $direccionesDetalladas[] = new DireccionDetalle(
                    $direccion,
                    $esPrincipal,
                    $esPropietario
                );
            }
        }

        return $direccionesDetalladas;
    }

    /**
     * Obtiene todas las direcciones asociadas a esta casa
     *
     * @return array<Direccion>
     */
    public function getDirecciones(): array
    {
        $detalles = $this->getDireccionesDetalladas();
        return array_map(fn(DireccionDetalle $d) => $d->getDireccion(), $detalles);
    }

    /**
     * Añade una dirección a esta casa
     */
    public function addDireccion(int $id_direccion, bool $principal = false, bool $propietario = false): void
    {
        $this->repoCasaDireccion->asociarDireccion(
            $this->getId_ubi(),
            $id_direccion,
            $principal,
            $propietario
        );
    }

    /**
     * Elimina una dirección de esta casa
     */
    public function removeDireccion(int $id_direccion): void
    {
        $this->repoCasaDireccion->desasociarDireccion($this->getId_ubi(), $id_direccion);
    }

    /**
     * Obtiene la dirección principal de esta casa
     */
    public function getDireccionPrincipal(): ?Direccion
    {
        $id_direccion_principal = $this->repoCasaDireccion->getDireccionPrincipal($this->getId_ubi());

        if ($id_direccion_principal === null) {
            return null;
        }

        return $this->repoDireccion->findById($id_direccion_principal);
    }

    /**
     * Establece una dirección como principal
     */
    public function establecerDireccionPrincipal(int $id_direccion): void
    {
        $this->repoCasaDireccion->establecerDireccionPrincipal($this->getId_ubi(), $id_direccion);
    }

    /**
     * Cambia el estado de propiedad de una dirección específica.
     */
    public function cambiarEstadoPropietario(int $id_direccion, bool $esPropietario): void
    {
        // Llamamos a un método en el repositorio para actualizar solo este campo
        $this->repoCasaDireccion->updatePropietario($this->getId_ubi(), $id_direccion, $esPropietario);
    }

}