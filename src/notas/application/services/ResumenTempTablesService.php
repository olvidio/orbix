<?php

namespace src\notas\application\services;

use PDO;

class ResumenTempTablesService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function rebuildMainTable(
        string $tabla,
        string $personas,
        string $finCurso,
        string $curso,
        bool $isRstgr,
        array $delegaciones = []
    ): void {
        $sqlCreate = "CREATE TABLE IF NOT EXISTS $tabla(
                                        id_nom int4 NOT NULL PRIMARY KEY,
                                        id_tabla char(6),
                                        nom varchar(40),
                                        apellido1  varchar(25),
                                        apellido2  varchar(25),
                                        nivel_stgr int,
                                        situacion char(1),
                                        f_situacion date,
                                        f_o date,
                                        f_fl date,
                                        f_orden date,
                                        ce_lugar varchar(40),
                                        ce_ini int2,
                                        ce_fin int2,
                                        sacd bool,
                                        ctr text )";

        if ($isRstgr) {
            $sqlCreate = "CREATE TABLE IF NOT EXISTS $tabla(
                                        id_nom int4 NOT NULL,
                                        id_tabla char(6),
                                        nom varchar(40),
                                        apellido1  varchar(25),
                                        apellido2  varchar(25),
                                        nivel_stgr int,
                                        situacion char(1),
                                        f_situacion date,
                                        f_o date,
                                        f_fl date,
                                        f_orden date,
                                        ce_lugar varchar(40),
                                        ce_ini int2,
                                        ce_fin int2,
                                        sacd bool,
                                        ctr text )";
        }

        $this->pdo->query($sqlCreate);
        $this->pdo->query("CREATE INDEX IF NOT EXISTS {$tabla}_apellidos ON $tabla (apellido1,apellido2,nom)");
        $this->pdo->query("CREATE INDEX IF NOT EXISTS {$tabla}_stgr ON $tabla (nivel_stgr)");
        $this->pdo->query("TRUNCATE TABLE $tabla");

        $sqlLlenar = "INSERT INTO $tabla
                SELECT p.id_nom,p.id_tabla,p.nom,p.apellido1,p.apellido2,p.nivel_stgr,
                p.situacion,p.f_situacion,
                NULL,NULL,NULL,
                p.ce_lugar,p.ce_ini,p.ce_fin,
                p.sacd,u.nombre_ubi
                FROM $personas p LEFT JOIN u_centros_dl u ON (p.id_ctr = u.id_ubi)
                WHERE (p.situacion='A' AND (p.f_situacion < '$finCurso' OR p.f_situacion IS NULL))
                     OR (p.situacion='D' AND p.f_situacion $curso)
                     OR (p.situacion!='A' AND p.f_situacion > '$finCurso')";

        if ($isRstgr) {
            $whereDl = '';
            if (!empty($delegaciones)) {
                $dlCsv = implode("','", $delegaciones);
                $whereDl = "u.dl IN ('$dlCsv') AND";
            }
            $sqlLlenar = "INSERT INTO $tabla
                SELECT p.id_nom,p.id_tabla,p.nom,p.apellido1,p.apellido2,p.nivel_stgr,
                p.situacion,p.f_situacion,
                NULL,NULL,NULL,
                p.ce_lugar,p.ce_ini,p.ce_fin,
                p.sacd,u.nombre_ubi || ' (' || u.dl || ')'
                FROM $personas p LEFT JOIN u_centros_dl u ON (p.id_ctr = u.id_ubi)
                WHERE $whereDl ( (p.situacion='A' AND (p.f_situacion < '$finCurso' OR p.f_situacion IS NULL))
                     OR (p.situacion='D' AND p.f_situacion $curso)
                     OR (p.situacion!='A' AND p.f_situacion > '$finCurso') )";
        }

        $this->pdo->query($sqlLlenar);
    }

    public function applyNivelUpdates(string $tabla, string $tablaNotas, string $curso, int $nivelBienio, int $nivelCuadrienio): void
    {
        $sql = "UPDATE $tabla SET nivel_stgr=$nivelBienio
                FROM $tablaNotas n
                WHERE $tabla.id_nom=n.id_nom AND n.id_asignatura=9999 AND n.f_acta $curso";
        $this->pdo->query($sql);

        $sql = "UPDATE $tabla SET nivel_stgr=$nivelCuadrienio
                FROM $tablaNotas n
                WHERE $tabla.id_nom=n.id_nom AND n.id_asignatura=9998 AND n.f_acta $curso";
        $this->pdo->query($sql);
    }

    public function rebuildNotasTable(
        string $notas,
        string $tabla,
        string $curso,
        string $notasVf,
        array $situacionesSuperadas
    ): void {
        $sqlCreate = "CREATE TABLE IF NOT EXISTS $notas(
                                        id_nom int4 NOT NULL,
                                        id_asignatura int4 NOT NULL,
                                        id_nivel int4 NOT NULL,
                                        epoca int2,
                                        f_acta  date NOT NULL,
                                        acta  varchar(50),
                                        preceptor bool,
                                        PRIMARY KEY (id_nom,id_asignatura)
                                         )";
        $this->pdo->query($sqlCreate);
        $this->pdo->query("CREATE INDEX IF NOT EXISTS {$notas}_nivel ON $notas (id_nivel)");
        $this->pdo->query("TRUNCATE TABLE $notas");

        $whereSuperada = "AND id_situacion IN (" . implode(',', $situacionesSuperadas) . ")";
        $sqlLlenar = "INSERT INTO $notas
                    SELECT DISTINCT n.id_nom,n.id_asignatura,n.id_nivel,
                           n.epoca,n.f_acta,n.acta,n.preceptor
                    FROM $tabla p, $notasVf n
                    WHERE p.id_nom=n.id_nom AND n.f_acta $curso AND tipo_acta = 1
                        $whereSuperada";

        $this->pdo->query($sqlLlenar);
    }

    public function rebuildAsignaturasTable(string $asignaturas, iterable $asignaturasCollection): void
    {
        $sqlDelete = "DROP TABLE IF EXISTS $asignaturas CASCADE";
        $sqlCreate = "CREATE TABLE $asignaturas(
                        id_asignatura integer,
                        id_nivel integer,
                        nombre_asignatura character varying(100) NOT NULL,
                        nombre_corto character varying(23),
                        creditos numeric(4,2),
                        year character varying(3),
                        id_sector smallint,
                        active boolean DEFAULT true NOT NULL,
                        id_tipo integer
                     )";

        $this->pdo->query($sqlDelete);
        $this->pdo->query($sqlCreate);
        $this->pdo->query("CREATE INDEX IF NOT EXISTS {$asignaturas}_nivel ON $asignaturas (id_nivel)");
        $this->pdo->query("CREATE INDEX IF NOT EXISTS {$asignaturas}_id_asignatura ON $asignaturas (id_asignatura)");

        $prep = $this->pdo->prepare("INSERT INTO $asignaturas VALUES(:id_asignatura, :id_nivel, :nombre_asignatura, :nombre_corto, :creditos, :year, :id_sector, :active, :id_tipo)");
        foreach ($asignaturasCollection as $asignatura) {
            $datos = [];
            $datos['id_asignatura'] = $asignatura->getId_asignatura();
            $datos['id_nivel'] = $asignatura->getId_nivel();
            $datos['nombre_asignatura'] = $asignatura->getNombre_asignatura();
            $datos['nombre_corto'] = $asignatura->getNombre_corto();
            $datos['creditos'] = $asignatura->getCreditos();
            $datos['year'] = $asignatura->getYear();
            $datos['id_sector'] = $asignatura->getId_sector();
            $datos['active'] = $asignatura->isActive();
            $datos['id_tipo'] = $asignatura->getId_tipo();

            $prep->execute($datos);
        }
    }

    public function rebuildProfesorTable(string $tabla, string $personas): void
    {
        $this->pdo->query("DROP TABLE IF EXISTS $tabla");
        $sqlCreate = "CREATE TABLE $tabla AS
                        SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,u.nombre_ubi as ctr
                        FROM $personas p JOIN d_profesor_stgr d USING(id_nom), u_centros_dl u
                        WHERE situacion='A' AND (p.id_ctr = u.id_ubi)
                        ORDER BY id_nom";
        $this->pdo->query($sqlCreate);
    }

    public function query(string $sql): \PDOStatement
    {
        return $this->pdo->query($sql);
    }

    public function prepare(string $sql): \PDOStatement
    {
        return $this->pdo->prepare($sql);
    }
}
