<?php

namespace src\ubis\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;

class PgTrasladoUbiRepository extends ClaseRepository implements TrasladoUbiRepositoryInterface
{
    use HandlesPdoErrors;

    public function trasladoCdc(int $id_ubi, string $esquema_org, string $esquema_dst): bool
    {
        $db = $this->importConnection('public');

        $aInserts = [
            ['tabla' => 'du_gastos_dl', 'campos' => 'id_ubi, f_gasto, tipo, cantidad'],
            ['tabla' => 'du_grupos_dl', 'campos' => 'id_ubi_padre, id_ubi_hijo'],
            ['tabla' => 'du_periodos', 'campos' => 'id_ubi, f_ini, f_fin, sfsv'],
            ['tabla' => 'd_teleco_cdc_dl', 'campos' => 'id_ubi, tipo_teleco, id_desc_teleco, num_teleco, observ'],
        ];

        foreach ($aInserts as $cambio) {
            if (!$this->executeInsert($db, $id_ubi, $esquema_org, $esquema_dst, $cambio['tabla'], $cambio['campos'], '')) {
                return false;
            }
        }

        return $this->executeDirecciones($db, $id_ubi, $esquema_org, $esquema_dst, '');
    }

    public function trasladoCtr(int $id_ubi, string $esquema_org, string $esquema_dst): bool
    {
        $db = $this->importConnection('publicv');

        $aInserts = [
            ['tabla' => 'd_teleco_ctr_dl', 'campos' => 'id_ubi, tipo_teleco, id_desc_teleco, num_teleco, observ'],
            ['tabla' => 'du_presentacion_dl', 'campos' => 'id_direccion, id_ubi, pres_nom, pres_telf, pres_mail, zona, observ'],
        ];

        foreach ($aInserts as $cambio) {
            if (!$this->executeInsert($db, $id_ubi, $esquema_org, $esquema_dst, $cambio['tabla'], $cambio['campos'], 'v')) {
                return false;
            }
        }

        return $this->executeDirecciones($db, $id_ubi, $esquema_org, $esquema_dst, 'v');
    }

    private function importConnection(string $schemaKey): PDO
    {
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema($schemaKey);
        $oConexion = new DBConnection($config);
        return $oConexion->getPDO();
    }

    private function executeDirecciones(PDO $db, int $id_ubi, string $esquema_org, string $esquema_dst, string $sv): bool
    {
        if ($sv === 'v') {
            $tabla_ubi = 'u_centros_dl';
            $tabla_dir = 'u_dir_ctr_dl';
            $tabla_cross = 'u_cross_ctr_dl_dir';
            $constrain = 'u_cross_ctr_dl_dir_pkey';
            $campos_ubi = 'tipo_ubi, id_ubi, nombre_ubi, pais, active, f_active, sv, sf, tipo_ctr, tipo_labor, cdc, id_ctr_padre, n_buzon, num_pi, num_cartas, observ, num_habit_indiv, plazas, sede, num_cartas_mensuales';
        } else {
            $tabla_ubi = 'u_cdc_dl';
            $tabla_dir = 'u_dir_cdc_dl';
            $tabla_cross = 'u_cross_cdc_dl_dir';
            $constrain = 'u_cross_cdc_dl_dir_pkey';
            $campos_ubi = 'tipo_ubi, id_ubi, nombre_ubi, pais, active, f_active, sv, sf, tipo_casa, plazas, plazas_min, num_sacd, biblioteca, observ';
        }

        $full_name_ubi_org = "\"$esquema_org$sv\".$tabla_ubi";
        $full_name_ubi_dst = "\"$esquema_dst$sv\".$tabla_ubi";
        $full_name_org = "\"$esquema_org$sv\".$tabla_dir";
        $full_name_dst = "\"$esquema_dst$sv\".$tabla_dir";
        $full_name_cross_org = "\"$esquema_org$sv\".$tabla_cross";
        $full_name_cross_dst = "\"$esquema_dst$sv\".$tabla_cross";

        $insert_ubi = "INSERT INTO $full_name_ubi_dst ($campos_ubi)
                SELECT $campos_ubi FROM $full_name_ubi_org WHERE id_ubi = $id_ubi
                ON CONFLICT (id_ubi) DO NOTHING";
        if (!$this->pdoExec($db, $insert_ubi, __METHOD__, __FILE__, __LINE__)) {
            return false;
        }

        $sql = "SELECT id_direccion FROM $full_name_cross_org WHERE id_ubi = $id_ubi";
        $stmt = $this->pdoQuery($db, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
            $id_direccion = (int)$row[0];
            $campos = 'id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ, cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede';
            $insert = "INSERT INTO $full_name_dst ($campos)
                    (SELECT $campos FROM $full_name_org d WHERE id_direccion = $id_direccion)
                    ON CONFLICT (id_direccion) DO NOTHING";
            if (!$this->pdoExec($db, $insert, __METHOD__, __FILE__, __LINE__)) {
                return false;
            }

            $campos = 'id_ubi, id_direccion, propietario, principal';
            $sql2 = "INSERT INTO $full_name_cross_dst ($campos)
                        SELECT $campos FROM $full_name_cross_org WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion
                        ON CONFLICT ON CONSTRAINT $constrain DO NOTHING";
            if (!$this->pdoExec($db, $sql2, __METHOD__, __FILE__, __LINE__)) {
                return false;
            }

            $sql_del = "DELETE FROM $full_name_org WHERE id_direccion = $id_direccion";
            if (!$this->pdoExec($db, $sql_del, __METHOD__, __FILE__, __LINE__)) {
                return false;
            }
        }

        $sql_del = "DELETE FROM $full_name_ubi_org WHERE id_ubi = $id_ubi";
        return $this->pdoExec($db, $sql_del, __METHOD__, __FILE__, __LINE__);
    }

    private function executeInsert(PDO $db, int $id_ubi, string $esquema_org, string $esquema_dst, string $tabla, string $campos, string $sv): bool
    {
        $full_name_org = "\"$esquema_org$sv\".$tabla";
        $full_name_dst = "\"$esquema_dst$sv\".$tabla";

        if (!$this->existeTabla($db, $full_name_org) || !$this->existeTabla($db, $full_name_dst)) {
            return true;
        }

        switch ($tabla) {
            case 'du_presentacion_dl':
                $sql = "INSERT INTO $full_name_dst ($campos)
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi
                        ON CONFLICT ON CONSTRAINT du_presentacion_dl_pkey DO NOTHING";
                break;
            case 'du_periodos':
                $sql = "INSERT INTO $full_name_dst ($campos)
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi
                        ON CONFLICT (id_ubi, f_ini) DO NOTHING";
                break;
            default:
                $sql = "INSERT INTO $full_name_dst ($campos)
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi
                        ON CONFLICT (id_ubi) DO NOTHING";
                break;
        }

        if (!$this->pdoExec($db, $sql, __METHOD__, __FILE__, __LINE__)) {
            return false;
        }

        $sql_del = "DELETE FROM $full_name_org WHERE id_ubi = $id_ubi";
        return $this->pdoExec($db, $sql_del, __METHOD__, __FILE__, __LINE__);
    }

    public function existeCdcDl(string $esquema): bool
    {
        if (!$this->esIdentificadorEsquemaValido($esquema)) {
            return false;
        }
        $db = $this->importConnection('public');

        return $this->existeTabla($db, '"' . $esquema . '".u_cdc_dl');
    }

    public function trasladoCdcDesdeResto(int $id_ubi, string $esquema_dst): bool
    {
        if (!$this->esIdentificadorEsquemaValido($esquema_dst)) {
            return false;
        }

        $db = $this->importConnection('public');
        $dst = '"' . $esquema_dst . '"';
        if (!$this->existeTabla($db, $dst . '.u_cdc_dl')) {
            return false;
        }

        $db->beginTransaction();
        try {
            $campos_ubi = 'tipo_ubi, id_ubi, nombre_ubi, dl, pais, region, active, f_active, sv, sf, tipo_casa, plazas, plazas_min, num_sacd, biblioteca, observ';
            $insert_ubi = "INSERT INTO $dst.u_cdc_dl ($campos_ubi)
                SELECT 'cdcdl', id_ubi, nombre_ubi, dl, pais, region, active, f_active, sv, sf, tipo_casa, plazas, plazas_min, num_sacd, biblioteca, observ
                FROM resto.u_cdc_ex WHERE id_ubi = $id_ubi
                ON CONFLICT (id_ubi) DO UPDATE SET
                    tipo_ubi = EXCLUDED.tipo_ubi,
                    nombre_ubi = EXCLUDED.nombre_ubi,
                    dl = EXCLUDED.dl,
                    pais = EXCLUDED.pais,
                    region = EXCLUDED.region,
                    active = EXCLUDED.active,
                    f_active = EXCLUDED.f_active,
                    sv = EXCLUDED.sv,
                    sf = EXCLUDED.sf,
                    tipo_casa = EXCLUDED.tipo_casa,
                    plazas = EXCLUDED.plazas,
                    plazas_min = EXCLUDED.plazas_min,
                    num_sacd = EXCLUDED.num_sacd,
                    biblioteca = EXCLUDED.biblioteca,
                    observ = EXCLUDED.observ";
            if (!$this->pdoExec($db, $insert_ubi, __METHOD__, __FILE__, __LINE__)) {
                $db->rollBack();
                return false;
            }

            $puedeCopiarDirs = $this->existeTabla($db, 'resto.u_dir_cdc_ex')
                && $this->existeTabla($db, 'resto.u_cross_cdc_ex_dir')
                && $this->existeTabla($db, $dst . '.u_dir_cdc_dl')
                && $this->existeTabla($db, $dst . '.u_cross_cdc_dl_dir');
            if ($puedeCopiarDirs) {
                $sql = "SELECT id_direccion FROM resto.u_cross_cdc_ex_dir WHERE id_ubi = $id_ubi";
                $stmt = $this->pdoQuery($db, $sql, __METHOD__, __FILE__, __LINE__);
                if ($stmt === false) {
                    $db->rollBack();
                    return false;
                }
                $campos_dir = 'id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ, cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede';
                foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
                    $id_direccion = (int) $row[0];
                    $insert_dir = "INSERT INTO $dst.u_dir_cdc_dl ($campos_dir)
                        SELECT $campos_dir FROM resto.u_dir_cdc_ex WHERE id_direccion = $id_direccion
                        ON CONFLICT (id_direccion) DO NOTHING";
                    if (!$this->pdoExec($db, $insert_dir, __METHOD__, __FILE__, __LINE__)) {
                        $db->rollBack();
                        return false;
                    }
                    $insert_cross = "INSERT INTO $dst.u_cross_cdc_dl_dir (id_ubi, id_direccion, propietario, principal)
                        SELECT id_ubi, id_direccion, propietario, principal
                        FROM resto.u_cross_cdc_ex_dir
                        WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion
                        ON CONFLICT (id_ubi, id_direccion) DO NOTHING";
                    if (!$this->pdoExec($db, $insert_cross, __METHOD__, __FILE__, __LINE__)) {
                        $db->rollBack();
                        return false;
                    }
                    if (!$this->pdoExec($db, "DELETE FROM resto.u_cross_cdc_ex_dir WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion", __METHOD__, __FILE__, __LINE__)) {
                        $db->rollBack();
                        return false;
                    }
                    if (!$this->pdoExec($db, "DELETE FROM resto.u_dir_cdc_ex WHERE id_direccion = $id_direccion", __METHOD__, __FILE__, __LINE__)) {
                        $db->rollBack();
                        return false;
                    }
                }
            }

            if ($this->existeTabla($db, 'resto.u_cross_cdc_ex_dir')) {
                if (!$this->pdoExec($db, "DELETE FROM resto.u_cross_cdc_ex_dir WHERE id_ubi = $id_ubi", __METHOD__, __FILE__, __LINE__)) {
                    $db->rollBack();
                    return false;
                }
            }

            if ($this->existeTabla($db, 'resto.d_teleco_cdc_ex') && $this->existeTabla($db, $dst . '.d_teleco_cdc_dl')) {
                $insert_teleco = "INSERT INTO $dst.d_teleco_cdc_dl (id_ubi, id_tipo_teleco, id_desc_teleco, num_teleco, observ)
                    SELECT id_ubi, id_tipo_teleco, id_desc_teleco, num_teleco, observ
                    FROM resto.d_teleco_cdc_ex WHERE id_ubi = $id_ubi";
                if (!$this->pdoExec($db, $insert_teleco, __METHOD__, __FILE__, __LINE__)) {
                    $db->rollBack();
                    return false;
                }
                if (!$this->pdoExec($db, "DELETE FROM resto.d_teleco_cdc_ex WHERE id_ubi = $id_ubi", __METHOD__, __FILE__, __LINE__)) {
                    $db->rollBack();
                    return false;
                }
            }

            if (!$this->pdoExec($db, "DELETE FROM resto.u_cdc_ex WHERE id_ubi = $id_ubi", __METHOD__, __FILE__, __LINE__)) {
                $db->rollBack();
                return false;
            }

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    private function esIdentificadorEsquemaValido(string $esquema): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,31}$/', $esquema)
            && str_contains($esquema, '-');
    }

    private function existeTabla(PDO $db, string $full_name): bool
    {
        $sql = "SELECT to_regclass('$full_name')";
        $stmt = $this->pdoQuery($db, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        return !empty($stmt->fetchColumn());
    }
}
