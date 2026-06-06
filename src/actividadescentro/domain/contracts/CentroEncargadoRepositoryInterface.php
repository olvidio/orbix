<?php

namespace src\actividadescentro\domain\contracts;

use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadescentro\domain\value_objects\CentroEncargadoPk;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;

interface CentroEncargadoRepositoryInterface
{
    public function getProximasActividadesDeCentro(int $id_ubi, string $f_ini_act_iso): string;

    /**
     * @return list<ActividadAll>
     */
    public function getActividadesDeCentros(int $iid_ubi, string $scondicion = ''): array;

    /**
     * @return list<CentroDl|CentroEllas>
     */
    public function getCentrosEncargadosActividad(int $iid_activ): array;

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     *
     * @return list<CentroEncargado>
     */
    public function getCentrosEncargados(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(CentroEncargado $CentroEncargado, bool $registrarCambios = true): bool;

    public function Guardar(CentroEncargado $CentroEncargado, bool $registrarCambios = true): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ, int $id_ubi): array|false;

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(CentroEncargadoPk $pk): array|false;

    public function findById(int $id_activ, int $id_ubi): ?CentroEncargado;

    public function findByPk(CentroEncargadoPk $pk): ?CentroEncargado;
}
