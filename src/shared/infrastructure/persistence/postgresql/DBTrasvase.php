<?php

namespace src\shared\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;
use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\domain\entity\DBAbstract;
use src\utils_database\domain\entity\MapId;
use src\utils_database\domain\value_objects\MapIdDl;
use src\utils_database\domain\value_objects\MapIdResto;

class DBTrasvase extends DBAbstract
{

    private string $sdbname;
    private string $sregion;
    private string $sdir;
    private string $sdl;
    private string $sEsquema;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private \PDO $oDbResto;
    private string $serror;

    function __construct()
    {
        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        if (!empty($esquema_sfsv)) {
            $this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        }
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setDbName($dbname)
    {
        $this->sdbname = $dbname;

        $oDbl = $this->getConexionPDO();
        $this->setoDbl($oDbl);
    }

    public function getDbName()
    {
        return $this->sdbname;
    }

    private function getConfigConexion($esquema = '')
    {
        if (empty($esquema)) {
            $esquema = $this->getEsquema();
        }
        switch ($this->getDbName()) {
            case 'comun':
                $oConfigDB = new ConfigDB('comun'); //de la database comun
                $config = $oConfigDB->getEsquema($esquema); //de la database comun
                break;
            case 'sv':
                $oConfigDB = new ConfigDB('sv'); //de la database sv
                $config = $oConfigDB->getEsquema($esquema); //de la database sv
                break;
            case 'sf':
                $oConfigDB = new ConfigDB('sf'); //de la database sf
                $config = $oConfigDB->getEsquema($esquema); //de la database sf
                break;
            case 'sv-e':
                $oConfigDB = new ConfigDB('sv-e'); //de la database sv
                $config = $oConfigDB->getEsquema($esquema); //de la database sv
                break;
            case 'sf-e':
                $oConfigDB = new ConfigDB('sf-e'); //de la database sf
                $config = $oConfigDB->getEsquema($esquema); //de la database sf
                break;
        }

        return $config;
    }

    private function getConexionPDO($esquema = '')
    {
        $config = $this->getConfigConexion($esquema);

        $oConnection = new DBConnection($config);
        return $oConnection->getPDO();
    }


    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    private function setoDbl($oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    private function getoDbl(): PDO
    {
        return $this->oDbl;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbResto
     */
    private function getoDbResto()
    {
        if (empty($this->oDbResto)) {
            $this->oDbResto = $this->getConexionPDO('resto');
        }
        return $this->oDbResto;
    }


    public function getDir()
    {
        $this->sdir = empty($this->sdir) ? ConfigGlobal::$directorio . '/log/db' : $this->sdir;
        return $this->sdir;
    }

    public function setDir($dir)
    {
        $this->sdir = $dir;
    }

    public function getRegion()
    {
        return $this->sregion;
    }

    public function setRegion($region)
    {
        $this->sregion = $region;
    }

    public function getDl()
    {
        return $this->sdl;
    }

    public function setDl($dl)
    {
        $this->sdl = $dl;
    }

    public function getEsquema()
    {
        switch ($this->getDbName()) {
            case 'sv':
            case 'sv-e':
                $seccion = 'v';
                break;
            case 'sf':
                $seccion = 'f';
                break;
            case 'comun':
                $seccion = '';
                break;
        }
        $this->sEsquema = $this->getRegion() . '-' . $this->getDl() . $seccion;
        return $this->sEsquema;
    }

    public function getResto()
    {
        switch ($this->getDbName()) {
            case 'sv':
            case 'sv-e':
                $seccion = 'v';
                break;
            case 'sf':
                $seccion = 'f';
                break;
            case 'comun':
                $seccion = '';
                break;
        }
        $this->sEsquema = 'resto' . $seccion;
        return $this->sEsquema;
    }

    /**
     * Fija las secuencias de un esquema
     * Busca todas las secuencias del esquema New, busca su valor máximo y cambia la secuencia a este valor
     *
     */
    public function fix_seq()
    {
        $oDbl = $this->getoDbl();
        $esquema = $this->getEsquema();
        // buscar todas las secuencias del esquema y crear la instruccion sql para poner el valor MAX.
        // Guardo las instrucciones en un fichero <== No se puede si no soy superusuario: Lo hago una por una:
        $sql = "SELECT  'SELECT SETVAL(' ||quote_literal(quote_ident(PGT.schemaname)|| '.'||quote_ident(S.relname))|| ', MAX(' ||quote_ident(C.attname)|| ') ) FROM ' ||quote_ident(PGT.schemaname)|| '.'||quote_ident(T.relname)|| ';'
			FROM pg_class AS S, pg_depend AS D, pg_class AS T, pg_attribute AS C, pg_tables AS PGT
			WHERE S.relkind = 'S'
				AND S.oid = D.objid
				AND D.refobjid = T.oid
				AND D.refobjid = C.attrelid
				AND D.refobjsubid = C.attnum
				AND T.relname = PGT.tablename
			AND PGT.schemaname='$esquema'
			ORDER BY S.relname
			";
        foreach ($oDbl->query($sql) as $row) {
            $oDbl->query($row[0]);
        }
    }

    // COMUN
    //-------------- Actividades ----------------------
    public function actividades($que)
    {
        // Conexión DB comun
        $oDbl = $this->getoDbl();

        $region = $this->getRegion();
        $dl = $this->getDl();

        switch ($que) {
            case 'resto2dl':
                // via objetos, para no dar permisos especiales a las tablas:
                if ($dl === 'cr') {
                    $dl_org = $region;
                } else {
                    $dl_org = $dl;
                }
                $ActividadExRepository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
                $cActividades = $ActividadExRepository->getActividades(['dl_org' => $dl_org]);
                $error = '';
                if (!empty($cActividades)) {
                    $MapIdRepository = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
                    $MapIdRepository->setoDbl($oDbl);
                    $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
                    foreach ($cActividades as $oActividad) {
                        //TODO: $oActividadDl->setNoGenerarProceso(TRUE);
                        if ($ActividadDlRepository->Guardar($oActividad, false) === false) { // Pongo el param registrarCambios=false para que no anote cambios.
                            $error .= '<br>' . _("no se ha guardado la actividad");
                            exit($error);
                        }
                        //borrar la origen:
                        $ActividadExRepository->Eliminar($oActividad);
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            case 'dl2resto':
                $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
                $ActividadDlRepository->setoDbl($oDbl);
                $cActividades = $ActividadDlRepository->getActividades(['dl_org' => $dl]);
                $error = '';
                if (!empty($cActividades)) {
                    $ActividadExRepository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
                    foreach ($cActividades as $oActividad) {
                        // TODO: $oActividadEx->setNoGenerarProceso(TRUE);

                        if ($ActividadExRepository->Guadar($oActividad, false) === false) { // Pongo el param registrarCambios=false para que no anote cambios.
                            $error .= '<br>' . _("no se ha guardado la actividad");
                            exit($error);
                        }
                        //borrar la origen:
                        $ActividadDlRepository->Eliminar($oActividad);
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            default:
                return false;
        }
    }

//---------------- CDC --------------------
//---------------- Direcciones CDC --------------------
//---------------- Teleco CDC --------------------
    public function cdc($que)
    {
        // Conexión DB comun
        $oDbl = $this->getoDbl();

        $esquema = $this->getEsquema();
        $dl = $this->getDl();
        $region = $this->getRegion();
        $tipoUbicacion = substr($dl, 0, 2); // puede ser: cr => comisión, dl => delegación, ci => centro interregional.

        $MapIdRepository = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
        $MapIdRepository->setoDbl($oDbl);
        switch ($que) {
            case 'resto2dl':
                if ($tipoUbicacion === 'cr') { //no hay delegaciones.
                    $aWhere = ['dl' => '', 'region' => $region];
                    $aOperador = ['dl' => 'IS NULL'];
                } else {
                    $aWhere = ['dl' => $dl, 'region' => $region];
                    $aOperador = [];
                }
                $CasaExRepository = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
                $cCasasEx = $CasaExRepository->getCasas($aWhere, $aOperador);
                $error = '';
                $CasaDlRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
                $RelacionCasaDlDireccion = $GLOBALS['container']->get(RelacionCasaDlDireccionRepositoryInterface::class);
                $RelacionCasaExDireccion = $GLOBALS['container']->get(RelacionCasaExDireccionRepositoryInterface::class);
                foreach ($cCasasEx as $oCasaEx) {
                    $aDades = $oCasaEx->toArrayForDatabase();
                    $oCasaDl = Casa::fromArray($aDades);
                    // actualizar el tipo_ubi.
                    $oCasaDl->setTipo_casa('cdcdl');
                    $CasaDlRepository->setoDbl($oDbl);
                    $newIdCasa = $CasaDlRepository->getNewId();
                    $oCasaDl->setId_ubi($newIdCasa);
                    if ($CasaDlRepository->Guardar($oCasaDl) === FALSE) {
                        $error .= '<br>' . _("no se ha guardado la casa");
                    } else {
                        $id_ubi_old = $aDades['id_ubi'];
                        $oMapId = $MapIdRepository->findById('Casa', $id_ubi_old);
                        if ($oMapId === null) {
                            $oMapId = new MapId();
                            $oMapId->setObjeto('Casa');
                            $oMapId->setIdRestoVo(MapIdResto::fromString($id_ubi_old));
                        }
                        $oMapId->setIdDlVo(MapIdDl::fromString($newIdCasa));
                        $MapIdRepository->Guardar($oMapId);
                        // Buscar la dirección
                        $aIdDirecciones = $RelacionCasaExDireccion->getDireccionesProUbi($id_ubi_old);
                        $DireccionCasaDlRepository = $GLOBALS['container']->get(DireccionCasaDlRepositoryInterface::class);
                        $DireccionCasaExRepository = $GLOBALS['container']->get(DireccionCasaExRepositoryInterface::class);
                        foreach ($aIdDirecciones as $aDireccion) {
                            $id_direccion_old = $aDireccion['id_direccion'];
                            $principal = $aDireccion['principal'];
                            $oDireccionEx = $DireccionCasaExRepository->findById($id_direccion_old);
                            $newIdDireccion = $DireccionCasaDlRepository->getNewId();
                            $oDireccionDl = clone $oDireccionEx;
                            $oDireccionDl->setId_direccion($newIdDireccion);
                            $DireccionCasaDlRepository->Guardar($oDireccionDl);
                            // Map
                            $oMapId = $MapIdRepository->findById('Direccion', $id_direccion_old);
                            $oMapId->setIdDlVo(MapIdDl::fromString($newIdDireccion));
                            $MapIdRepository->Guardar($oMapId);
                            // cross Direccion
                            $RelacionCasaDlDireccion->asociarDireccion($newIdCasa, $newIdDireccion, $principal);
                            // Eliminar el cross y la direccion
                            $DireccionCasaExRepository->Eliminar($oDireccionEx);
                            // delete cross (deberia borrarse sólo; por el foreign key).
                            $RelacionCasaExDireccion->desasociarDireccion($id_ubi_old, $id_direccion_old);
                        }
                        // Buscar las telecos
                        $TelecoCdcExRepository = $GLOBALS['container']->get(TelecoCdcExRepositoryInterface::class);
                        $cTelecos = $TelecoCdcExRepository->getTelecos(['id_ubi' => $id_ubi_old]);
                        $TelecoCdcDlRepository = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
                        foreach ($cTelecos as $oTelecoCdcEx) {
                            $newId = $TelecoCdcDlRepository->getNewId();
                            $oTelecoCdcDl = clone $oTelecoCdcEx;
                            $oTelecoCdcDl->setId_teleco($newId);
                            if ($TelecoCdcDlRepository->Guardar($oTelecoCdcDl) === FALSE) {
                                $error .= '<br>' . _("no se ha guardado la teleco de la casa");
                            } else {
                                // Eliminar la teleco
                                $TelecoCdcExRepository->Eliminar($oTelecoCdcEx);
                            }
                        }
                        //borrar la origen:
                        $CasaExRepository->Eliminar($oCasaEx);
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            case 'dl2resto':
                // actualizar el tipo_ubi.
                /* no hace falta si despues se borra toda la tabla
                $sql = "UPDATE \"$esquema\".u_cdc_dl SET tipo_ubi='cdcex';";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBTrasvase.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }
                */

                $this->addPermisoGlobal('comun');
                $this->addPermisoRole('comun', $esquema);
                $a_sql = [];
                //cdc
                $a_sql[] = "INSERT INTO resto.u_cdc_ex SELECT * FROM \"$esquema\".u_cdc_dl ;";
                // primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
                $a_sql[] = "INSERT INTO resto.u_dir_cdc_ex SELECT * FROM  \"$esquema\".u_dir_cdc_dl ;";
                $a_sql[] = "INSERT INTO  resto.u_cross_cdc_ex_dir  SELECT * FROM \"$esquema\".u_cross_cdc_dl_dir ;";
                // delete cdc
                $a_sql[] = "TRUNCATE \"$esquema\".u_cdc_dl RESTART IDENTITY CASCADE;";
                // delete dir
                $a_sql[] = "TRUNCATE \"$esquema\".u_dir_cdc_dl RESTART IDENTITY CASCADE;";
                // delete cross (debería borrarse sólo; por el foreign key).
                $this->executeSql($a_sql);
                $this->delPermisoRole('comun', $esquema);
                $this->delPermisoGlobal('comun');
                break;
            default:
                return false;
        }
    }

// SV o SF
//---------------- Ctr --------------------
//---------------- Direcciones Ctr --------------------
//---------------- Teleco Ctr --------------------
    public function ctr($que)
    {
        // Conexión DB SV/SF
        $oDbl = $this->getoDbl();
        $esquema = $this->getEsquema();
        $resto = $this->getResto();

        // Conexión DB comun
        $this->setDbName('comun');
        $oDblC = $this->getoDbl();

        $dl = $this->getDl();
        $region = $this->getRegion();
        $tipoUbicacion = substr($dl, 0, 2); // puede ser: cr => cominsión, dl => delegacion, ci => centro interregional.

        $MapIdRepository = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
        $MapIdRepository->setoDbl($oDbl);
        switch ($que) {
            case 'resto2dl':
                if ($tipoUbicacion === 'cr') { //no hay delegaciones.
                    $aWhere = ['dl' => '', 'region' => $region];
                    $aOperador = ['dl' => 'IS NULL'];
                } else {
                    $aWhere = ['dl' => $dl, 'region' => $region];
                    $aOperador = [];
                }
                $CentroExRepository = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
                $cCentroEx = $CentroExRepository->getCentros($aWhere, $aOperador);
                $error = '';
                $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $CentroEllosRepository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
                $RelacionCentroDlDireccion = $GLOBALS['container']->get(RelacionCentroDlDireccionRepositoryInterface::class);
                $RelacionCentroExDireccion = $GLOBALS['container']->get(RelacionCentroExDireccionRepositoryInterface::class);
                foreach ($cCentroEx as $oCentroEx) {
                    $aDades = $oCentroEx->toArrayForDatabase();
                    // actualizar el tipo_ubi.
                    $aDades['tipo_ubi'] = 'ctrdl';
                    // Ahora uso la nomenclatura para dl tipo 'crA'
                    $aDades['dl'] = $dl;
                    $oCentroDl = Centro::fromArray($aDades);
                    $CentroDlRepository->setoDbl($oDbl);
                    $newIdCentro = $CentroDlRepository->getNewId();
                    $oCentroDl->setId_ubi($newIdCentro);
                    if ($CentroDlRepository->Guardar($oCentroDl) === FALSE) {
                        $error .= '<br>' . _("no se ha guardado el centro");
                    } else {
                        // Al hacer INSERT se genera un id_ubi nuevo. Para conservar el original:
                        $id_ubi_old = $aDades['id_ubi'];
                        $oMapId = $MapIdRepository->findById('Centro', $id_ubi_old);
                        if ($oMapId === null) {
                            $oMapId = new MapId();
                            $oMapId->setObjeto('Centro');
                            $oMapId->setIdRestoVo(MapIdResto::fromString($id_ubi_old));
                        }
                        $oMapId->setIdDlVo(MapIdDl::fromString($newIdCentro));
                        $MapIdRepository->Guardar($oMapId);
                        // Además hay que añadirlo a la copia en DB comun:
                        // para la sf (comienza por 2).
                        if (substr($newIdCentro, 0, 1) == 2) {
                            $oCentroEllas = new CentroEllas();
                            $oCentroEllas->setId_ubi($newIdCentro);
                            $oCentroEllas->setAllAttributes($aDades, TRUE);
                            $CentroEllasRepository->setoDbl($oDblC);
                            $CentroEllasRepository->Guardar($oCentroEllas);
                        } else {
                            $oCentroEllos = new CentroEllos();
                            $oCentroEllos->setId_ubi($newIdCentro);
                            $oCentroEllos->setAllAttributes($aDades);
                            $CentroEllosRepository->setoDbl($oDblC);
                            $CentroEllosRepository->Guardar($oCentroEllos);
                        }
                        // Buscar la dirección
                        $aIdDirecciones = $RelacionCentroExDireccion->getDireccionesProUbi($id_ubi_old);
                        $DireccionCentroDlRepository = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
                        $DireccionCentroExRepository = $GLOBALS['container']->get(DireccionCentroExRepositoryInterface::class);
                        foreach ($aIdDirecciones as $aIdDireccion) {
                            $id_direccion_old = $aIdDireccion ['id_direccion'];
                            $propietario = $aIdDireccion ['propietario'];
                            $principal = $aIdDireccion['principal'];
                            $oDireccionCentroEx = $DireccionCentroExRepository->findBuId($id_direccion_old);
                            $newIdDireccionCentro = $DireccionCentroDlRepository->getNewId();
                            $oDireccionCentroDl = clone $oDireccionCentroEx;
                            $oDireccionCentroDl->setId_direccion($newIdDireccionCentro);
                            $DireccionCentroDlRepository->Guardar($oDireccionCentroDl);
                            // Map
                            $oMapId = $MapIdRepository->findById('Direccion', $id_direccion_old);
                            $oMapId->setIdDlVo(MapIdDl::fromString($newIdDireccionCentro));
                            $MapIdRepository->Guardar($oMapId);
                            // cross Direccion
                            $RelacionCentroDlDireccion->asociarDireccion($newIdCentro, $newIdDireccionCentro, $principal, $propietario);
                            // Eliminar el cross y la dirección
                            $DireccionCentroExRepository->Eliminar($oDireccionCentroEx);
                            // delete cross (debería borrarse sólo; por el foreign key).
                            $RelacionCentroExDireccion->desasociarDireccion($id_ubi_old, $id_direccion_old);
                        }
                        // Buscar las telecos
                        $TelecoCtrExRepository = $GLOBALS['container']->get(TelecoCtrExRepositoryInterface::class);
                        $cTelecos = $TelecoCtrExRepository->getTelecos(['id_ubi' => $id_ubi_old]);
                        $TelecoCtrDlRepository = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
                        foreach ($cTelecos as $oTelecoCtrEx) {
                            $newId = $TelecoCtrDlRepository->getNewId();
                            $oTelecoCtrDl = clone $oTelecoCtrEx;
                            $oTelecoCtrDl->setId_teleco($newId);
                            if ($TelecoCtrDlRepository->Guardar($oTelecoCtrDl) === FALSE) {
                                $error .= '<br>' . _("no se ha guardado la teleco el ctr");
                            } else {
                                // Eliminar la teleco
                                $TelecoCtrExRepository->Eliminar($oTelecoCtrEx);
                            }
                        }
                        //borrar la origen:
                        $oCentroEx->DBEliminar();
                    }
                }
                if (empty($error)) {
                    return true;
                }

                $this->serror = $error;
                return false;
            case 'dl2resto':
                // Si ya se trasladó/borró previamente, evitamos fallar al reintentar.
                $sql = "SELECT to_regclass('\"$esquema\".u_centros_dl') AS tabla";
                if (($oDblSt = $oDbl->query($sql)) === false) {
                    $sClauError = 'DBTrasvase.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }
                $aDades = $oDblSt->fetch(PDO::FETCH_ASSOC);
                if (empty($aDades['tabla'])) {
                    // No hay nada que trasladar en sv/sf para ese esquema.
                    return true;
                }

                // actualizar el tipo_ubi.
                $sql = "UPDATE \"$esquema\".u_centros_dl SET tipo_ubi='ctrex'";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBTrasvase.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }

                $this->addPermisoGlobal('sfsv');
                $this->addPermisoRole('sfsv', $esquema);

                $sql = "INSERT INTO \"$resto\".u_centros_ex SELECT tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,active,f_active,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre,id_auto FROM \"$esquema\".u_centros_dl";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBEliminar.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    $this->delPermisoRole('sfsv', $esquema);
                    $this->delPermisoGlobal('sfsv');
                    return false;
                } else {
                    // primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
                    $sql = "INSERT INTO \"$resto\".u_dir_ctr_ex SELECT * FROM  \"$esquema\".u_dir_ctr_dl";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    $sql = "INSERT INTO \"$resto\".u_cross_ctr_ex_dir SELECT * FROM \"$esquema\".u_cross_ctr_dl_dir ";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    // delete ctr
                    $sql = "TRUNCATE \"$esquema\".u_centros_dl RESTART IDENTITY CASCADE";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    // delete dir
                    $sql = "TRUNCATE \"$esquema\".u_dir_ctr_dl RESTART IDENTITY CASCADE";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        $this->delPermisoRole('sfsv', $esquema);
                        $this->delPermisoGlobal('sfsv');
                        return false;
                    }
                    // delete cross (debería borrarse sólo; por el foreign key).
                }
                $this->delPermisoRole('sfsv', $esquema);
                $this->delPermisoGlobal('sfsv');
                break;
            default:
                return false;
        }
    }

}