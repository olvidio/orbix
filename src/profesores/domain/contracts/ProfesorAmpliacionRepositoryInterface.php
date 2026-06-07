<?php

namespace src\profesores\domain\contracts;

use src\profesores\domain\entity\ProfesorAmpliacion;
use src\asignaturas\domain\value_objects\AsignaturaId;


/**
 * Interfaz de la clase ProfesorAmpliacion y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
interface ProfesorAmpliacionRepositoryInterface
{

    /**
     * @deprecated Usar getArrayProfesoresAsignaturaVo(AsignaturaId $id_asignatura)
     */
    /**
     * @return array<int, string>
     */
    public function getArrayProfesoresAsignatura(int $id_asignatura): array;

    /**
     * @return array<int, string>
     */
    public function getArrayProfesoresAsignaturaVo(AsignaturaId $id_asignatura): array;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ProfesorAmpliacion
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ProfesorAmpliacion> Una colección de objetos de tipo ProfesorAmpliacion
     */
    public function getProfesorAmpliaciones(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorAmpliacion $ProfesorAmpliacion): bool;

    public function Guardar(ProfesorAmpliacion $ProfesorAmpliacion): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item): ?ProfesorAmpliacion;

    public function getNewId(): int;
}