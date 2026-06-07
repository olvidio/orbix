<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\config\ConfigGlobal;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;

/**
 * GestorPersonaAll
 *
 * Dado que no parece que vaya a ser necesario asilar completamente los esquemas, con esta
 * clase podré consultar la tabla padre de todas las personas. Es útil cuando no se encuentra
 * la persona en la delegación que se espera.
 *
 */
class PgPersonaAllRepository extends ClaseRepository implements PersonaAllRepositoryInterface
{
    public function __construct(
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
    ) {
        $this->setoDbl(GlobalPdo::get('oDBP'));
        $this->setNomTabla('global.personas');
    }

    public function getPersonaByIdNom(int $id_nom): ?\src\personas\domain\entity\PersonaDl
    {

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $mi_id_schema = ConfigGlobal::mi_id_schema();
        // buscar los 'A' de mi schema
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion = 'A' AND id_schema = $mi_id_schema ";
        if ($this->ejecutar($sql) !== FALSE) {
            return $this->personaDlRepository->findById($id_nom);
        }

        // sino, los que vienen de otra dl
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion = 'A' ORDER BY f_situacion";
        if ($oDblSt = $this->ejecutar($sql)) {
            $id_schema_persona = null;
            foreach ($oDblSt->fetchAll(\PDO::FETCH_ASSOC) as $aDades) {
                if (is_array($aDades) && isset($aDades['id_schema']) && is_numeric($aDades['id_schema'])) {
                    $id_schema_persona = (int)$aDades['id_schema'];
                }
            }
            // Si hay más de uno, me quedo con el que tiene la fecha de cambio situación más reciente.
            // Lo marco como publicado
            if ($id_schema_persona !== null) {
                $sql = "UPDATE $nom_tabla SET es_publico = 't' WHERE id_nom=$id_nom AND situacion = 'A' AND id_schema = $id_schema_persona";
                $this->ejecutar($sql);
            }
        }

        // que esté en la dl, pero no en situación = 'A'
        // buscar los distintos de 'A' de mi schema
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion != 'A' AND id_schema = $mi_id_schema ";
        if ($this->ejecutar($sql) !== FALSE) {
            return $this->personaDlRepository->findById($id_nom);
        }
        return null;
    }

    /** @return \PDOStatement|false */
    private function ejecutar(string $sql): \PDOStatement|false
    {
        $oDbl = $this->getoDbl();
        if (($oDblSt = $oDbl->query($sql)) === false) {
            $sClauError = 'PersonaAll.select';
            if (isset($_SESSION['oGestorErrores']) && is_object($_SESSION['oGestorErrores']) && method_exists($_SESSION['oGestorErrores'], 'addErrorAppLastError')) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            }
            return false;
        }
        if (empty($oDblSt->rowCount())) {
            return false;
        }

        return $oDblSt;
    }

}
