<?php

namespace Tests\integration\personas\domain;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBPropiedades;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\TrasladoDl;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\factories\personas\PersonaNFactory;
use Tests\myTest;

class TrasladoDlCopyPersonaTest extends myTest
{
    private string $schemaOrg = 'H-dlbv';
    private string $schemaDst = 'Ch-crChv';
    private int $idNom;

    public function setUp(): void
    {
        try {
            parent::setUp();
        } catch (\Throwable $e) {
            $this->markTestSkipped('BD de pruebas no disponible: ' . $e->getMessage());
        }
    }

    public function test_copy_persona_copia_persona_n_entre_esquemas(): void
    {
        $this->idNom = random_int(79000000, 79999999);

        $oDBorg = $this->connectionForSchema($this->schemaOrg);
        $oDBdst = $this->connectionForSchema($this->schemaDst);

        $originRepo = $this->personaNRepositoryWithConnection($oDBorg);
        $destRepo = $this->personaNRepositoryWithConnection($oDBdst);

        $this->cleanupPersonaDl($oDBorg, $this->idNom);
        $this->cleanupPersonaDl($oDBdst, $this->idNom);
        $this->cleanupPersona($originRepo, $this->idNom);
        $this->cleanupPersona($destRepo, $this->idNom);

        $factory = new PersonaNFactory();
        $persona = $factory->createSimple();
        $persona->setId_nom($this->idNom);
        $persona->setDlVo('dlb');
        $originRepo->Guardar($persona);
        $this->insertPersonaDl($oDBorg, $this->idNom, 'n', 'dlb', 'Apellido', 'A');

        $traslado = new TrasladoDl();
        $traslado->setId_nom($this->idNom);
        $traslado->setReg_dl_org($this->schemaOrg);
        $traslado->setReg_dl_dst($this->schemaDst);
        $traslado->setF_dl(new DateTimeLocal());

        $result = $traslado->copiarPersona();
        $this->assertTrue($result, $traslado->getError() ?? 'copyPersona devolvió false');

        $copied = $destRepo->findById($this->idNom);
        $this->assertInstanceOf(PersonaN::class, $copied);
        $this->assertSame('crCh', $copied->getDl());
        $this->assertSame('A', $copied->getSituacion());

        $this->cleanupPersona($originRepo, $this->idNom);
        $this->cleanupPersona($destRepo, $this->idNom);
        $this->cleanupPersonaDl($oDBorg, $this->idNom);
        $this->cleanupPersonaDl($oDBdst, $this->idNom);
    }

    private function cleanupPersona(PersonaNRepositoryInterface $repo, int $idNom): void
    {
        $persona = $repo->findById($idNom);
        if ($persona !== null) {
            $repo->Eliminar($persona);
        }
    }

    private function personaNRepositoryWithConnection(\PDO $oDB): PersonaNRepositoryInterface
    {
        $factory = $GLOBALS['container']->get(ConnectionRepositoryFactoryInterface::class);
        return $factory->createWithConnection(PersonaNRepositoryInterface::class, $oDB);
    }

    private function insertPersonaDl(
        \PDO $oDB,
        int $idNom,
        string $idTabla,
        string $dl,
        string $apellido1,
        string $situacion
    ): void {
        $idSchema = $this->schemaIdFromSearchPath($oDB);
        $sql = 'INSERT INTO personas_dl (id_schema,id_nom,id_tabla,dl,apellido1,situacion) VALUES (:id_schema,:id_nom,:id_tabla,:dl,:apellido1,:situacion)';
        $stmt = $oDB->prepare($sql);
        $stmt->execute([
            'id_schema' => $idSchema,
            'id_nom' => $idNom,
            'id_tabla' => $idTabla,
            'dl' => $dl,
            'apellido1' => $apellido1,
            'situacion' => $situacion,
        ]);
    }

    private function cleanupPersonaDl(\PDO $oDB, int $idNom): void
    {
        $stmt = $oDB->prepare('DELETE FROM personas_dl WHERE id_nom = :id_nom');
        $stmt->execute(['id_nom' => $idNom]);
    }

    private function schemaIdFromSearchPath(\PDO $oDB): int
    {
        $row = $oDB->query('SHOW search_path')->fetch(\PDO::FETCH_ASSOC);
        $schema = trim(explode(',', $row['search_path'])[0], "\" ");
        $sql = "SELECT id FROM public.db_idschema WHERE schema = :schema";
        $stmt = $oDB->prepare($sql);
        $stmt->execute(['schema' => $schema]);
        $id = $stmt->fetchColumn();
        if ($id === false) {
            throw new \RuntimeException("No se ha encontrado id_schema para $schema");
        }
        return (int)$id;
    }

    private function connectionForSchema(string $schema): \PDO
    {
        if (ConfigGlobal::mi_sfsv() === 2) {
            $database = 'sf';
            if (ConfigGlobal::mi_region_dl() !== $schema) {
                $schema = 'restof';
            }
        } else {
            $database = 'sv';
            $oDBPropiedades = new DBPropiedades();
            $aEsquemas = $oDBPropiedades->array_posibles_esquemas();
            $aEsquemas['H-Hv'] = 'H-Hv';
            if (!in_array($schema, $aEsquemas, true)) {
                $schema = 'restov';
            }
        }

        $oConfigDB = new ConfigDB($database);
        $config = $oConfigDB->getEsquema($schema);
        $oConexion = new DBConnection($config);

        return $oConexion->getPDO();
    }
}
