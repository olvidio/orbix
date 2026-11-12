<?php

namespace Tests\unit\notas;

use src\notas\domain\criteria\PersonaNotaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Búsqueda de notas por igualdad de campos con `matching()` (sustituye al antiguo `getPersonaNotas`).
 */
trait BuscaPersonaNotasTrait
{
    /** `id_activ` fijo de las notas de `NotasFactory`. */
    private const ID_ACTIV_FACTORY = 30012345;

    /**
     * @param object $repo repositorio de notas con `matching(Criteria)`
     * @param array<string, mixed> $campos nombre de campo => valor (solo igualdad)
     * @return array<int, object>
     */
    private function buscarPersonaNotas(object $repo, array $campos): array
    {
        $criteria = Criteria::create();
        foreach ($campos as $campo => $valor) {
            $criteria = $criteria->andWhere(Filter::eq(PersonaNotaField::from($campo), $valor));
        }

        return $repo->matching($criteria);
    }

    private ?\PDO $pdoNotasDlOriginal = null;

    /**
     * Guarda la conexión del repositorio compartido de notas de la dl. Algunos tests la cambian con `setoDbl()`
     * para que el caso de uso escriba en otro esquema; sin restaurarla, todos los tests siguientes del proceso
     * leían y escribían en ese esquema.
     */
    private function guardarConexionNotasDl(): void
    {
        $this->pdoNotasDlOriginal = $GLOBALS['container']->get(\src\notas\domain\contracts\PersonaNotaDlRepositoryInterface::class)->getoDbl();
    }

    private function restaurarConexionNotasDl(): void
    {
        if ($this->pdoNotasDlOriginal !== null) {
            $GLOBALS['container']->get(\src\notas\domain\contracts\PersonaNotaDlRepositoryInterface::class)->setoDbl($this->pdoNotasDlOriginal);
        }
    }

    /**
     * Borra de la dl de la sesión las notas que deja `NotasFactory` (todas llevan `id_activ` 30012345) para las
     * personas de prueba. Se llama antes y después de cada test: una ejecución cortada antes de su limpieza dejaba
     * la nota y, si otra ejecución elegía al azar la misma asignatura, fallaba con «Ya existe esta nota».
     *
     * @param list<int> $idsNom
     */
    private function limpiarNotasDeFactory(array $idsNom): void
    {
        $repo = $GLOBALS['container']->get(\src\notas\domain\contracts\PersonaNotaDlRepositoryInterface::class);
        foreach ($idsNom as $idNom) {
            foreach ($this->buscarPersonaNotas($repo, ['id_nom' => $idNom, 'id_activ' => self::ID_ACTIV_FACTORY]) as $oPersonaNota) {
                $repo->Eliminar($oPersonaNota);
            }
        }
    }
}
