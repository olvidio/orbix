<?php

namespace src\notas\application\repositories;

use PDO;
use src\notas\domain\entity\Nota;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\infrastructure\repositories\ConstantNotaRepository;
use src\notas\infrastructure\repositories\PgNotaRepository;


use function core\is_true;
/**
 *
 * Clase para gestionar la lista de objetos tipo Nota
 * 
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class NotaRepository implements NotaRepositoryInterface
{

    /**$
     * @var NotaRepositoryInterface
     */
    private NotaRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = new ConstantNotaRepository();
    }

    public function getArrayNotasNoSuperadas(): array
    {
        return $this->repository->getArrayNotasNoSuperadas();
    }

    public function getArrayNotasSuperadas(): array
    {
        return $this->repository->getArrayNotasSuperadas();
    }

    public function getArrayNotas(): array
    {
        return $this->repository->getArrayNotas();
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Nota $Nota): bool
    {
        return $this->repository->Eliminar($Nota);
    }

	public function Guardar(Nota $Nota): bool
    {
        return $this->repository->Guardar($Nota);
    }

	public function getErrorTxt(): string
    {
        return $this->repository->getErrorTxt();
    }

	public function getoDbl(): PDO
    {
        return $this->repository->getoDbl();
    }

	public function setoDbl(PDO $oDbl): void
    {
        $this->repository->setoDbl($oDbl);
    }

	public function getNomTabla(): string
    {
        return $this->repository->getNomTabla();
    }
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param int $id_situacion
     * @return array|bool
	
     */
    public function datosById(int $id_situacion): array|bool
    {
        return $this->repository->datosById($id_situacion);
    }
	
    /**
     * Busca la clase con id_situacion en el repositorio.
	
     */
    public function findById(int $id_situacion): ?Nota
    {
        return $this->repository->findById($id_situacion);
    }

}