<?php

namespace src\asistentes\domain\contracts;

use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistentePk;
use src\shared\domain\criteria\Criteria;


/**
 * Interfaz de la clase Asistente y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
interface AsistenteRepositoryInterface
{


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Asistentes que cumplen el criterio.
     *
     * @return list<Asistente>
     */
    public function matching(Criteria $criteria): array;

    /**
     * Une los resultados de varios repositorios de asistentes (Dl, Out, Ex…) para un criterio.
     * El orden y la paginación (limit/offset) de `$criteria` se aplican sobre el conjunto ya unido,
     * en memoria: solo se admiten órdenes por campo de AsistenteField.
     *
     * @param list<class-string<AsistenteRepositoryInterface>> $repos
     * @return list<Asistente>
     * @throws \InvalidArgumentException si un repositorio no es de asistentes o el orden no se puede aplicar
     */
    public function matchingConjunto(array $repos, Criteria $criteria): array;

    /**
     * Une colecciones de varios repositorios de asistentes (Dl, Out, Ex…).
     *
     * @deprecated usar matchingConjunto(array, Criteria)
     * @param list<array{repo: class-string, get: string}> $a_Clases
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Asistente>
     */
    public function getConjunt(array $a_Clases, string $namespace, array $aWhere, array $aOperators): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Asistente $Asistente, bool $registrarCambios = true): bool;

    public function Guardar(Asistente $Asistente, bool $registrarCambios = true): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_activ
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ, int $id_nom): array|false;

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(AsistentePk $pk): array|false;

    /**
     * Busca la clase con id_activ en el repositorio.
     */
    public function findById(int $id_activ, int $id_nom): ?Asistente;

    public function findByPk(AsistentePk $pk): ?Asistente;
}
