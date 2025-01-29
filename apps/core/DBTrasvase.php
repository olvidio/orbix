<?php

namespace core;

use actividades\model\entity\ActividadDl;
use actividades\model\entity\ActividadEx;
use actividades\model\entity\GestorActividadDl;
use actividades\model\entity\GestorActividadEx;
use devel\model\DBAbstract;
use devel\model\entity\MapId;
use ubis\model\entity\CasaDl;
use ubis\model\entity\CdcDlxDireccion;
use ubis\model\entity\CentroDl;
use ubis\model\entity\CentroEllas;
use ubis\model\entity\CentroEllos;
use ubis\model\entity\CtrDlxDireccion;
use ubis\model\entity\DireccionCdcDl;
use ubis\model\entity\DireccionCdcEx;
use ubis\model\entity\DireccionCtrDl;
use ubis\model\entity\DireccionCtrEx;
use ubis\model\entity\GestorCasaEx;
use ubis\model\entity\GestorCdcExxDireccion;
use ubis\model\entity\GestorCentroEx;
use ubis\model\entity\GestorCtrExxDireccion;
use ubis\model\entity\GestorTelecoCdcEx;
use ubis\model\entity\GestorTelecoCtrEx;
use ubis\model\entity\TelecoCdcDl;

class DBTrasvase extends DBAbstract
{

    private $sdbname;
    private $sregion;
    private $sdir;
    private $sdl;
    private $sEsquema;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private \PDO $oDbResto;
    private string $serror;

    function __construct()
    {
        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        $this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        $this->role = '"' . $this->esquema . '"';
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
    private function setoDbl($oDbl)
    {
        $this->oDbl = $oDbl;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    private function getoDbl()
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
                if ($dl == 'cr') {
                    $dl_org = $region;
                } else {
                    $dl_org = $dl;
                }
                $GesActividadesEx = new GestorActividadEx();
                $cActividades = $GesActividadesEx->getActividades(['dl_org' => $dl_org]);
                $error = '';
                if (!empty($cActividades)) {
                    foreach ($cActividades as $oActividad) {
                        $oActividad->DBCarregar();
                        $aDades = $oActividad->getTot();
                        //print_r($oActividad);
                        $oActividadDl = new ActividadDl();
                        $oActividadDl->setoDbl($oDbl);
                        $oActividadDl->setAllAtributes($aDades, TRUE);
                        $oActividadDl->setNoGenerarProceso(TRUE);

                        if ($oActividadDl->DBGuardar(1) === false) { // Pongo el param quiet=1 para que no anote cambios.
                            $error .= '<br>' . _("no se ha guardado la actividad");
                        } else {
                            // Al hacer INSERT se genera un id_activ nuevo. Para conservar el original:
                            $id_old = $aDades['id_activ'];
                            $pkey = ['objeto' => 'Actividad', 'id_resto' => $id_old];
                            $oMapId = new MapId($pkey);
                            $oMapId->setId_dl($oActividadDl->getId_activ());
                            $oMapId->DBGuardar();
                            //borrar la origen:
                            $oActividad->DBEliminar();
                        }
                    }
                }
                if (empty($error)) {
                    return true;
                } else {
                    $this->serror = $error;
                    return false;
                }
                break;
            case 'dl2resto':
                $GesActividadesDl = new GestorActividadDl();
                $GesActividadesDl->setoDbl($oDbl);
                $cActividades = $GesActividadesDl->getActividades(['dl_org' => $dl]);
                $error = '';
                if (!empty($cActividades)) {
                    foreach ($cActividades as $oActividad) {
                        $oActividad->DBCarregar();
                        $aDades = $oActividad->getTot();
                        //print_r($oActividad);
                        $oActividadEx = new ActividadEx();
                        $oActividadEx->setoDbl($oDbl);
                        $oActividadEx->setAllAtributes($aDades, TRUE);
                        $oActividadEx->setNoGenerarProceso(TRUE);

                        if ($oActividadEx->DBGuardar(1) === false) { // Pongo el param quiet=1 para que no anote cambios.
                            $error .= '<br>' . _("no se ha guardado la actividad");
                        } else {
                            // Al hacer INSERT se genera un id_activ nuevo. Para conservar el original:
                            $id_new = $aDades['id_activ'];
                            $oActividadEx->DBCambioId($id_new);
                            //borrar la origen:
                            $oActividad->DBEliminar();
                        }
                    }
                }
                if (empty($error)) {
                    return true;
                } else {
                    $this->serror = $error;
                    return false;
                }
                break;
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

        switch ($que) {
            case 'resto2dl':
                if ($tipoUbicacion === 'cr') { //no hay delegaciones.
                    $aWhere = ['dl' => '', 'region' => $region];
                    $aOperador = ['dl' => 'IS NULL'];
                } else {
                    $aWhere = ['dl' => $dl, 'region' => $region];
                    $aOperador = [];
                }
                $gesCasaEx = new GestorCasaEx();
                $cCasasEx = $gesCasaEx->getCasas($aWhere, $aOperador);
                $error = '';
                foreach ($cCasasEx as $oCasaEx) {
                    $oCasaEx->DBCarregar();
                    $aDades = $oCasaEx->getTot();
                    $oCasaDl = new CasaDl();
                    $oCasaDl->setoDbl($oDbl);
                    $oCasaDl->setAllAtributes($aDades, TRUE);
                    // actualizar el tipo_ubi.
                    $oCasaDl->setTipo_ubi('cdcdl');
                    if ($oCasaDl->DBGuardar() === FALSE) {
                        $error .= '<br>' . _("no se ha guardado la casa");
                    } else {
                        // Al hacer INSERT se genera un id_ubi nuevo. Para conservar el original:
                        $id_ubi = $oCasaDl->getId_ubi();
                        $id_ubi_old = $aDades['id_ubi'];
                        $pkey = ['objeto' => 'Casa', 'id_resto' => $id_ubi_old];
                        $oMapId = new MapId($pkey);
                        $oMapId->setId_dl($id_ubi);
                        $oMapId->DBGuardar();
                        // Buscar la dirección
                        $gesCdcExxDireccion = new GestorCdcExxDireccion();
                        $cUbixDirecciones = $gesCdcExxDireccion->getCdcxDirecciones(['id_ubi' => $id_ubi_old]);
                        foreach ($cUbixDirecciones as $oUbixDireccion) {
                            $id_direccion_old = $oUbixDireccion->getId_direccion();
                            $oDireccion = new DireccionCdcEx($id_direccion_old);
                            $oDireccion->DBCarregar();
                            $aDades = $oDireccion->getTot();
                            $oDireccionCdcDl = new DireccionCdcDl();
                            $oDireccionCdcDl->setoDbl($oDbl);
                            $oDireccionCdcDl->setAllAtributes($aDades, FALSE);
                            $oDireccionCdcDl->DBGuardar();
                            $id_direccion = $oDireccionCdcDl->getId_direccion();
                            $pkey = ['objeto' => 'Direccion', 'id_resto' => $id_direccion_old];
                            $oMapId = new MapId($pkey);
                            $oMapId->setId_dl($id_direccion);
                            $oMapId->DBGuardar();
                            // cross Direccion
                            $pkey = ['id_ubi' => $id_ubi, 'id_direccion' => $id_direccion];
                            $oCrosDireccion = new CdcDlxDireccion($pkey);
                            $propietario = $oUbixDireccion->getPropietario();
                            $principal = $oUbixDireccion->getPrincipal();
                            $oCrosDireccion->setoDbl($oDbl);
                            $oCrosDireccion->setPropietario($propietario);
                            $oCrosDireccion->setPrincipal($principal);
                            $oCrosDireccion->DBGuardar();
                            // Eliminar el cross y la direccion
                            $oDireccion->DBEliminar();
                            // delete cross (deberia borrarse sólo; por el foreign key).
                            $oUbixDireccion->DBEliminar();
                        }
                        // Buscar las telecos
                        $gesTelecoCdcEx = new GestorTelecoCdcEx();
                        $cTelecos = $gesTelecoCdcEx->getTelecos(['id_ubi' => $id_ubi_old]);
                        foreach ($cTelecos as $oTelecoCdcEx) {
                            $oTelecoCdcEx->DBCarregar();
                            $aDades = $oTelecoCdcEx->getTot();
                            $oTelecoCdcDl = new TelecoCdcDl();
                            $oTelecoCdcDl->setoDbl($oDbl);
                            $oTelecoCdcDl->setAllAtributes($aDades, TRUE);
                            if ($oTelecoCdcDl->DBGuardar() === FALSE) {
                                $error .= '<br>' . _("no se ha guardado la casa");
                            } else {
                                // Eliminar la teleco
                                $oTelecoCdcEx->DBEliminar();
                            }
                        }
                        //borrar la origen:
                        $oCasaEx->DBEliminar();
                    }
                }
                if (empty($error)) {
                    return true;
                } else {
                    $this->serror = $error;
                    return false;
                }
                break;
            case 'dl2resto':
                // actualizar el tipo_ubi.
                $sql = "UPDATE \"$esquema\".u_cdc_dl SET tipo_ubi='cdcex';";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBTrasvase.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }

                $this->addPermisoGlobal('comun');
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
                $this->delPermisoGlobal('comun');
                break;
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
        // Conexión DB comun
        $this->setDbName('comun');
        $oDblC = $this->getoDbl();

        $esquema = $this->getEsquema();
        $resto = $this->getResto();
        $dl = $this->getDl();
        $region = $this->getRegion();
        $tipoUbicacion = substr($dl, 0, 2); // puede ser: cr => cominsión, dl => delegacion, ci => centro interregional.

        switch ($que) {
            case 'resto2dl':
                if ($tipoUbicacion === 'cr') { //no hay delegaciones.
                    $aWhere = ['dl' => '', 'region' => $region];
                    $aOperador = ['dl' => 'IS NULL'];
                } else {
                    $aWhere = ['dl' => $dl, 'region' => $region];
                    $aOperador = [];
                }
                $gesCentroEx = new GestorCentroEx();
                $cCentroEx = $gesCentroEx->getCentros($aWhere, $aOperador);
                $error = '';
                foreach ($cCentroEx as $oCentroEx) {
                    $oCentroEx->DBCarregar();
                    $aDades = $oCentroEx->getTot();
                    // actualizar el tipo_ubi.
                    $aDades['tipo_ubi'] = 'ctrdl';
                    // Ahora uso la nomenclatura para dl tipo 'crA'
                    $aDades['dl'] = $dl;

                    $oCentroDl = new CentroDl();
                    $oCentroDl->setoDbl($oDbl);
                    $oCentroDl->setAllAtributes($aDades, TRUE);
                    if ($oCentroDl->DBGuardar() === FALSE) {
                        $error .= '<br>' . _("no se ha guardado la casa");
                    } else {
                        // Al hacer INSERT se genera un id_ubi nuevo. Para conservar el original:
                        $id_ubi = $oCentroDl->getId_ubi();
                        $id_ubi_old = $aDades['id_ubi'];
                        $pkey = ['objeto' => 'Centro', 'id_resto' => $id_ubi_old];
                        $oMapId = new MapId($pkey);
                        $oMapId->setId_dl($id_ubi);
                        $oMapId->DBGuardar();
                        // Además hay que añadirlo a la copia en DB comun:
                        // para la sf (comienza por 2).
                        if (substr($id_ubi, 0, 1) == 2) {
                            $oCentroEllas = new CentroEllas($id_ubi);
                            $oCentroEllas->setAllAtributes($aDades);
                            $oCentroEllas->setoDbl($oDblC);
                            $oCentroEllas->DBGuardar();
                        } else {
                            $oCentroEllos = new CentroEllos($id_ubi);
                            $oCentroEllos->setAllAtributes($aDades);
                            $oCentroEllos->setoDbl($oDblC);
                            $oCentroEllos->DBGuardar();
                        }
                        // Buscar la dirección
                        $gesCtrExxDireccion = new GestorCtrExxDireccion();
                        $cUbixDirecciones = $gesCtrExxDireccion->getCtrxDirecciones(['id_ubi' => $id_ubi_old]);
                        foreach ($cUbixDirecciones as $oUbixDireccion) {
                            $id_direccion_old = $oUbixDireccion->getId_direccion();
                            $oDireccion = new DireccionCtrEx($id_direccion_old);
                            $oDireccion->DBCarregar();
                            $aDades = $oDireccion->getTot();
                            $oDireccionCtrDl = new DireccionCtrDl();
                            $oDireccionCtrDl->setoDbl($oDbl);
                            $oDireccionCtrDl->setAllAtributes($aDades, FALSE);
                            $oDireccionCtrDl->DBGuardar();
                            $id_direccion = $oDireccionCtrDl->getId_direccion();
                            $pkey = ['objeto' => 'Direccion', 'id_resto' => $id_direccion_old];
                            $oMapId = new MapId($pkey);
                            $oMapId->setId_dl($id_direccion);
                            $oMapId->DBGuardar();
                            // cross Direccion
                            $pkey = ['id_ubi' => $id_ubi, 'id_direccion' => $id_direccion];
                            $oCrosDireccion = new CtrDlxDireccion($pkey);
                            $propietario = $oUbixDireccion->getPropietario();
                            $principal = $oUbixDireccion->getPrincipal();
                            $oCrosDireccion->setoDbl($oDbl);
                            $oCrosDireccion->setPropietario($propietario);
                            $oCrosDireccion->setPrincipal($principal);
                            $oCrosDireccion->DBGuardar();
                            // Eliminar el cross y la dirección
                            $oDireccion->DBEliminar();
                            // delete cross (debería borrarse sólo; por el foreign key).
                            $oUbixDireccion->DBEliminar();
                        }
                        // Buscar las telecos
                        $gesTelecoCtrEx = new GestorTelecoCtrEx();
                        $cTelecos = $gesTelecoCtrEx->getTelecos(['id_ubi' => $id_ubi_old]);
                        foreach ($cTelecos as $oTelecoCtrEx) {
                            $oTelecoCtrEx->DBCarregar();
                            $aDades = $oTelecoCtrEx->getTot();
                            $oTelecoCdcDl = new TelecoCdcDl();
                            $oTelecoCdcDl->setoDbl($oDbl);
                            $oTelecoCdcDl->setAllAtributes($aDades);
                            if ($oTelecoCdcDl->DBGuardar() === FALSE) {
                                $error .= '<br>' . _("no se ha guardado la casa");
                            } else {
                                // Eliminar la teleco
                                $oTelecoCtrEx->DBEliminar();
                            }
                        }
                        //borrar la origen:
                        $oCentroEx->DBEliminar();
                    }
                }
                if (empty($error)) {
                    return true;
                } else {
                    $this->serror = $error;
                    return false;
                }
                break;
            case 'dl2resto':
                // actualizar el tipo_ubi.
                $sql = "UPDATE \"$esquema\".u_centros_dl SET tipo_ubi='ctrex'";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBTrasvase.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }
                $sql = "INSERT INTO \"$resto\".u_centros_ex SELECT tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre,id_auto FROM \"$esquema\".u_centros_dl";
                if ($oDbl->query($sql) === false) {
                    $sClauError = 'DBEliminar.ctr.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                } else {
                    // primero las direcciones porque 'u_cross' tiene como foreign key id_direccion e id_ubi.
                    $sql = "INSERT INTO \"$resto\".u_dir_ctr_ex SELECT * FROM  \"$esquema\".u_dir_ctr_dl";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        return false;
                    }
                    $sql = "INSERT INTO \"$resto\".u_cross_ctr_ex_dir SELECT * FROM \"$esquema\".u_cross_ctr_dl_dir ";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        return false;
                    }
                    // delete ctr
                    $sql = "TRUNCATE \"$esquema\".u_centros_dl RESTART IDENTITY CASCADE";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        return false;
                    }
                    // delete dir
                    $sql = "TRUNCATE \"$esquema\".u_dir_ctr_dl RESTART IDENTITY CASCADE";
                    if ($oDbl->query($sql) === false) {
                        $sClauError = 'DBTrasvase.ctr.execute';
                        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                        return false;
                    }
                    // delete cross (debería borrarse sólo; por el foreign key).
                }
                break;
        }
    }

}