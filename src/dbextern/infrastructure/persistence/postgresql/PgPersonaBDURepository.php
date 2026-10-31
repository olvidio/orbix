<?php

namespace src\dbextern\infrastructure\persistence\postgresql;

use PDO;
use PDOStatement;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\PersonaBDU;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgPersonaBDURepository extends ClaseRepository implements PersonaBDURepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('tmp_bdu');
    }

    /**
     * @return list<PersonaBDU>
     */
    public function getPersonaBDUQuery(string $sQuery = ''): array
    {
        $oDbl = $this->getoDbl();
        $oPersonaBDUSet = new Set();
        $stmt = $oDbl->query($sQuery);
        /** @var list<PersonaBDU> $personas */
        $personas = [];
        if ($stmt instanceof PDOStatement) {
            foreach ($stmt as $aDatos) {
                if (!is_array($aDatos)) {
                    continue;
                }
                $normalized = [];
                foreach ($aDatos as $key => $value) {
                    $normalized[(string) $key] = $value;
                }
                $personas[] = PersonaBDU::fromArray($normalized);
            }
        }

        return $personas;
    }

    public function Eliminar(PersonaBDU $PersonaBDU): bool
    {
        return false;
    }

    public function Guardar(PersonaBDU $PersonaBDU): bool
    {
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_listas): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE Identif = $id_listas";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }

    public function findById(int $id_listas): ?PersonaBDU
    {
        $aDatos = $this->datosById($id_listas);
        if ($aDatos === false) {
            return null;
        }

        return PersonaBDU::fromArray($aDatos);
    }
}
