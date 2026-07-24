<?php

namespace src\personas\infrastructure\persistence\postgresql;

use PDO;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaPub;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;

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

        // que esté en la dl, pero no en situación = 'A'
        // buscar los distintos de 'A' de mi schema
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion != 'A' AND id_schema = $mi_id_schema ";
        if ($this->ejecutar($sql) !== FALSE) {
            return $this->personaDlRepository->findById($id_nom);
        }
        return null;
    }

    public function findByIdNomParaLookup(int $id_nom, ?int $id_schema = null): ?PersonaPub
    {
        if ($id_nom === 0) {
            return null;
        }

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $vigente = PersonaPublicacion::sqlPublicacionVigente('');

        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = :id_nom";
        if ($id_schema !== null) {
            $sql .= ' AND id_schema = :id_schema';
        }
        $sql .= " ORDER BY
                  CASE WHEN situacion = 'A' THEN 0 ELSE 1 END,
                  CASE WHEN $vigente THEN 0 ELSE 1 END,
                  f_situacion DESC NULLS LAST,
                  id_schema ASC
                LIMIT 1";

        $stmt = $oDbl->prepare($sql);
        if ($stmt === false) {
            return null;
        }

        $params = ['id_nom' => $id_nom];
        if ($id_schema !== null) {
            $params['id_schema'] = $id_schema;
        }

        if ($stmt->execute($params) === false) {
            return null;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return null;
        }

        $aDatos['f_nacimiento'] = (new ConverterDate('date', $aDatos['f_nacimiento'] ?? null))->fromPg();
        $aDatos['f_situacion'] = (new ConverterDate('date', $aDatos['f_situacion'] ?? null))->fromPg();
        $aDatos['f_inc'] = (new ConverterDate('date', $aDatos['f_inc'] ?? null))->fromPg();
        $aDatos = PersonaPublicacion::hydrateRow($aDatos);

        return PersonaPub::fromArray($aDatos);
    }

    public function marcarPublicadoPara(
        int $id_nom,
        int $id_schema,
        string $dlOrStar,
        ?\DateTimeInterface $hasta = null,
    ): bool {
        if ($id_nom <= 0 || $id_schema < 1) {
            return false;
        }

        $dl = PersonaPublicacion::normalizarDl(trim($dlOrStar));
        if ($dl === '') {
            return false;
        }

        if ($hasta === null && $dl !== PersonaPublicacion::DL_TODAS) {
            $hasta = PersonaPublicacion::fechaHastaDefault();
        }

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sqlSel = "SELECT publicado_para
                   FROM $nom_tabla
                   WHERE id_nom = :id_nom AND situacion = 'A' AND id_schema = :id_schema
                   LIMIT 1";
        $stmtSel = $oDbl->prepare($sqlSel);
        if ($stmtSel === false || !$stmtSel->execute(['id_nom' => $id_nom, 'id_schema' => $id_schema])) {
            return false;
        }
        $row = $stmtSel->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return false;
        }

        $mapa = PersonaPublicacion::fromPg($row['publicado_para'] ?? null);
        $mapa = PersonaPublicacion::mergeDestino($mapa, $dl, $hasta);
        $publicadoPara = PersonaPublicacion::toPg($mapa);

        $sql = "UPDATE $nom_tabla SET
                    publicado_para = CAST(:publicado_para AS jsonb)
                WHERE id_nom = :id_nom AND situacion = 'A' AND id_schema = :id_schema";

        $stmt = $oDbl->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        return $stmt->execute([
            'publicado_para' => $publicadoPara,
            'id_nom' => $id_nom,
            'id_schema' => $id_schema,
        ]) && $stmt->rowCount() > 0;
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
