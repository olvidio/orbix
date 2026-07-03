<?php

namespace src\cambios\application\legacy;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\shared\infrastructure\DependencyResolver;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\CambioAnotado;
use src\cambios\domain\entity\CambioUsuario;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Legacy: anota los avisos de cambio para cada usuario.
 *
 * Consumida **solo** por `src\cambios\application\AvisosGenerarTabla`. El
 * resto del modulo (frontend, otros use cases) no debe importar esta
 * clase — vease `refactor.md`, seccion "Bloques heredados encapsulados en
 * `src/<modulo>/application/legacy/`".
 *
 * Se conservan intactos:
 *   - `echo` a stdout en `fn_apuntar` y `anotado` (util en el log de cron
 *     `log/avisos.out`/`log/avisos.err`).
 *   - Escritura directa al fichero PID `log/avisos.<esquema>.pid` para
 *     controlar concurrencia desde distintos procesos lanzados por
 *     `Cambio::generarTabla()` en background.
 *   - `exit` dentro de `crear_pid` cuando detecta otro proceso en marcha
 *     (es la semantica que corta el flujo cuando ya hay un generador
 *     trabajando). Cambiarlo a excepcion requiere tambien adaptar el
 *     use case para traducirla; queda como deuda.
 */
class Avisos
{
    private int $id_schema_cmb = 0;
    private int $id_item_cmb = 0;
    private int $id_usuario = 0;
    private string $sObjeto = '';
    /** @var list<int|string> */
    private array $aFases_cmb = [];

    public function __construct(
        private CambioUsuarioRepositoryInterface $cambioUsuarioRepository,
        private CambioAnotadoRepositoryInterface $cambioAnotadoRepository,
        private UsuarioRepositoryInterface $usuarioRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ZonaRepositoryInterface $zonaRepository,
        private ZonaSacdRepositoryInterface $zonaSacdRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
    ) {
    }

    public function crear_pid(string $username, string $esquema): void
    {
        if ($username === '') {
            return;
        }

        // Si he lanzado el proceso automaticamente, escribo el id del proceso.
        // Si ya existe un proceso en marcha, salgo del proceso.
        $filename = ConfigGlobal::$directorio . "/log/avisos.$esquema.pid";

        if (file_exists($filename)) {
            $fileContent = file_get_contents($filename);
            if ($fileContent !== false && $fileContent !== '') {
                // Comprobar que no sea por que el anterior ha dado un error y
                // no se ha borrado. Miramos que sea de hace mas de 15 min.
                $delta = 15;
                $matches = [];
                preg_match('@(\d+/\d+/\d+ \d+:\d+:\d+) -- .*@', $fileContent, $matches);
                if (!isset($matches[1])) {
                    exit;
                }
                $f_iso = $matches[1];

                $oDiaFichero = new DateTimeLocal($f_iso);
                $oAhora = new DateTimeLocal('now');

                $interval = $oDiaFichero->diff($oAhora);
                $a = $interval->format('%i');

                if ((int) $a > $delta) {
                    $ahora = date("Y/m/d H:i:s");
                    echo "$ahora ";
                    echo sprintf(_("El fichero %s no està vacio."), $filename);
                    echo " ";
                    echo _("Posiblemente la anterior operación finalizó con error");
                } else {
                    exit;
                }
            }
        }
        $ahora = date("Y/m/d H:i:s");
        $mensaje = "$ahora -- $username \n";
        file_put_contents($filename, $mensaje, LOCK_EX);
    }

    public function borrar_pid(string $username, string $esquema): void
    {
        // al finalizar borro el pid
        if ($username === '') {
            return;
        }

        // Si he lanzado el proceso automaticamente.
        // Borro el pid, para que empieze el siguiente proceso.
        // Hay que asegurarse que se han acabado de escribir todos los anotados, para que no los vuelva a escribir.
        // Por esto espero 7 segundos (con 3 no basta...)
        $filename = ConfigGlobal::$directorio . "/log/avisos.$esquema.pid";

        if (file_exists($filename)) {
            file_put_contents($filename, '', LOCK_EX);
        }
    }

    /**
     * @param int|string $aviso_tipo
     */
    public function fn_apuntar(int|string $aviso_tipo): string
    {
        $archivo_log = ConfigGlobal::$directorio . "/log/errores.log";

        $sfsv = ConfigGlobal::mi_sfsv();

        // Log de entrada
        $msg = "fn_apuntar: schema={$this->id_schema_cmb}, item={$this->id_item_cmb}, usuario={$this->id_usuario}, tipo={$aviso_tipo}, sfsv={$sfsv}";
        error_log($msg . "\n", 3, $archivo_log);

        // Asegurar que no existe:
        $aWhere = [
            'id_schema_cambio' => $this->id_schema_cmb,
            'id_item_cambio' => $this->id_item_cmb,
            'sfsv' => $sfsv,
            'id_usuario' => $this->id_usuario,
            'aviso_tipo' => $aviso_tipo,
        ];

        $cCambioUsuario = $this->cambioUsuarioRepository->getCambiosUsuario($aWhere);

        // Log del resultado de busqueda
        $msg = "fn_apuntar: Encontrados " . count($cCambioUsuario) . " registros existentes";
        error_log($msg . "\n", 3, $archivo_log);

        $err_fila = '';
        if (count($cCambioUsuario) > 0) {
            $msg = "fn_apuntar: DUPLICADO DETECTADO - No se insertara";
            error_log($msg . "\n", 3, $archivo_log);
            $err_fila .= "<tr>";
            $err_fila .= "<td>" . $this->id_schema_cmb . "</td>";
            $err_fila .= "<td>" . $this->id_item_cmb . "</td>";
            $err_fila .= "<td>" . $this->id_usuario . "</td>";
            $err_fila .= "<td>" . $aviso_tipo . "</td>";
            $err_fila .= "</tr>";
        } else {
            $msg = "fn_apuntar: Insertando nuevo registro";
            error_log($msg . "\n", 3, $archivo_log);
            $newIdItem = $this->cambioUsuarioRepository->getNewId();
            $oCambioUsuario = new CambioUsuario();
            $oCambioUsuario->setId_item($newIdItem);
            $oCambioUsuario->setId_schema_cambio($this->id_schema_cmb);
            $oCambioUsuario->setId_item_cambio($this->id_item_cmb);
            $oCambioUsuario->setSfsv($sfsv);
            $oCambioUsuario->setId_usuario($this->id_usuario);
            $oCambioUsuario->setAviso_tipo((int) $aviso_tipo);

            $resultado = $this->cambioUsuarioRepository->Guardar($oCambioUsuario);
            $msg = "fn_apuntar: Resultado Guardar: " . ($resultado ? 'SUCCESS' : 'FAILED');
            error_log($msg . "\n", 3, $archivo_log);

            if ($resultado === false) {
                echo ConfigGlobal::$web_server . '-->' . date('c') . " " . _("Hay un error, no se ha guardado");
                echo "<br>id_item_cmb: $this->id_item_cmb, id_usuario: $this->id_usuario, aviso_tipo: $aviso_tipo <br>\n";
            }
        }

        return $err_fila;
    }

    public function anotado(): void
    {
        $ubicacion = getenv('UBICACION');
        $serverRaw = getenv('DB_SERVER');
        $server = is_numeric($serverRaw) ? (int) $serverRaw : 0;

        // marcar como apuntado
        $aWhere = [
            'id_schema_cambio' => $this->id_schema_cmb,
            'id_item_cambio' => $this->id_item_cmb,
            'server' => $server,
        ];
        $this->cambioAnotadoRepository->setTabla((string) $ubicacion);
        $cCambiosAnotados = $this->cambioAnotadoRepository->getCambiosAnotados($aWhere);
        // deberia ser unico
        if (count($cCambiosAnotados) > 0) {
            $oCambioAnotado = $cCambiosAnotados[0];
        } else {
            $newIdItem = $this->cambioAnotadoRepository->getNewId();
            $oCambioAnotado = new CambioAnotado();
            $oCambioAnotado->setId_item($newIdItem);
            $oCambioAnotado->setId_item_cambio($this->id_item_cmb);
            $oCambioAnotado->setId_schema_cambio($this->id_schema_cmb);
            $oCambioAnotado->setServer($server);
        }

        $oCambioAnotado->setAnotado(true);
        if ($this->cambioAnotadoRepository->Guardar($oCambioAnotado) === false) {
            echo _("Hay un error, no se ha guardado");
            echo _("anotado");
            echo "<br>";
        }
    }

    public function comparar(mixed $valor_cmb, string $operador, mixed $valor): bool
    {
        switch ($operador) {
            case '=':
                if (is_string($valor) && strpos($valor, ',') !== false) { // es una lista de valores
                    $a_val = explode(',', $valor);
                    $rta = false;
                    foreach ($a_val as $val) {
                        $rta = $rta || ($valor_cmb == $val);
                    }
                    return $rta;
                }
                // ojo con los boolean.
                if (FuncTablasSupport::isTrue($valor_cmb) || FuncTablasSupport::isTrue($valor)) {
                    return ((bool) $valor_cmb === (bool) $valor);
                }
                return ($valor_cmb == $valor);
            case '>':
                return ($valor_cmb >= $valor);
            case '<':
                return ($valor_cmb <= $valor);
            case 'regexp':
                // funcionalidad nunca implementada; se conserva el case para no
                // romper preferencias guardadas con `operador = 'regexp'`.
                return false;
        }
        return false;
    }

    public function me_afecta(
        string $propiedad,
        int $id_activ,
        mixed $valor_old_cmb,
        mixed $valor_new_cmb,
        ?string $id_pau,
        string $sObjeto
    ): bool {
        // Si el usuario es una casa o un sacd, solo ve los cambios que le afectan:
        $oMiUsuario = $this->usuarioRepository->findById($this->id_usuario);
        if ($oMiUsuario === null) {
            return true;
        }
        $oRole = new Role();
        $oRole->setId_role($oMiUsuario->getId_role() ?? 0);

        //casa
        if ($oRole->isRolePau(PauType::PAU_CDC)) {
            $mis_id_ubis = $oMiUsuario->getCsvIdPauAsString() ?? '';
            if ($mis_id_ubis !== '') {
                $a_mis_id_ubis = explode(',', $mis_id_ubis);

                $oActividad = $this->actividadAllRepository->findById($id_activ);
                if ($oActividad === null) {
                    return false;
                }
                $id_ubi = $oActividad->getId_ubi(); // id ubi actual.

                // si lo que cambia es el id_ubi, compruebo que el valor old o new sean de la casa.
                if ($propiedad === 'id_ubi') {
                    return (in_array($valor_old_cmb, $a_mis_id_ubis, true) || in_array((string) $id_ubi, $a_mis_id_ubis, true));
                }
                // si cambia cualquier otra cosa en mi id_ubi.
                if (in_array((string) $id_ubi, $a_mis_id_ubis, true)) {
                    switch ($this->sObjeto) {
                        case 'ActividadCargoNoSacd':
                        case 'ActividadCargoSacd':
                            // si lo que cambia es el campo observaciones, no hace falta informar.
                            if ($propiedad === 'observ') {
                                return false;
                            }
                    }
                    return true;
                }
                return false;
            }
        }
        // si soy un sacd.
        if ($oRole->isRolePau(PauType::PAU_SACD)) {
            $id_nom_usuario = $oMiUsuario->getCsvIdPauAsString() ?? '';
            // soy jefe zona?
            // si soy jefe de zona me afectan todos los sacd de la zona.
            $rta = false;
            $cZonas = $this->zonaRepository->getZonas(['id_nom' => $id_nom_usuario]);
            if (count($cZonas) > 0) {
                // sacd de mi zona
                foreach ($cZonas as $oZona) {
                    $id_zona = $oZona->getId_zona();
                    $a_id_nom = $this->zonaSacdRepository->getIdSacdsDeZona($id_zona);
                    foreach ($a_id_nom as $id_nom_sacd) {
                        $rta = $this->tengoPermiso($propiedad, $id_activ, (string) $id_nom_sacd, $valor_old_cmb, $valor_new_cmb);
                        if ($rta === true) {
                            // no hace falta seguir mirando todos, con uno basta para avisar.
                            return true;
                        }
                    }
                }
            } else { // No soy jefe de zona
                $rta = $this->tengoPermiso($propiedad, $id_activ, $id_nom_usuario, $valor_old_cmb, $valor_new_cmb);
            }
            return $rta;
        }
        // En el caso de no ser casa ni sacd
        // comparar si el aviso corresponde a la casa (id_pau)
        if ($id_pau !== null && $id_pau !== '') { //casa o un listado de ubis en la preferencia del aviso.
            $a_id_pau = explode(',', $id_pau);

            $oActividad = $this->actividadAllRepository->findById($id_activ);
            if ($oActividad === null) {
                return false;
            }
            $id_ubi = $oActividad->getId_ubi(); // id ubi actual.
            // si lo que cambia es el id_ubi, compruebo que el valor old o new sean de la casa.
            if ($propiedad === 'id_ubi') {
                return (in_array($valor_old_cmb, $a_id_pau, true) || in_array((string) $id_ubi, $a_id_pau, true));
            }
            // si cambia cualquier otra cosa en mi id_ubi.
            if (in_array((string) $id_ubi, $a_id_pau, true)) {
                switch ($this->sObjeto) {
                    case 'ActividadCargoNoSacd':
                    case 'ActividadCargoSacd':
                        // si lo que cambia es el campo observaciones, no hace falta informar.
                        if ($propiedad === 'observ') {
                            return false;
                        }
                }
                return true;
            }
            return false;
        }
        // En el caso de no tener ninguna casa: retornar true
        return true;
    }

    /**
     * Mira si el cambio afecta a uno de los sacd de la zona y si tengo permiso para ver.
     * El id_nom puede ser cualquiera de la zona, no el que origina el cambio.
     */
    private function tengoPermiso(
        string $propiedad,
        int $id_activ,
        ?string $id_nom,
        mixed $valor_old_cmb,
        mixed $valor_new_cmb
    ): bool {
        switch ($this->sObjeto) {
            case 'Actividad':
                // busco los datos de las actividades
                $aWhereAct = ['id_activ' => $id_activ];
                $aOperadorAct = [];
                $aWhere = ['id_nom' => $id_nom];
                $aOperador = [];

                $permiso_ver = false;
                $cAsistentes = $this->actividadCargoRepository->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
                if ($cAsistentes !== [] && isset($cAsistentes[$id_activ])) {
                    $aAsistente = $cAsistentes[$id_activ];
                    $propio = FuncTablasSupport::isTrue($aAsistente['propio'] ?? false) === true;
                    $id_cargoRaw = $aAsistente['id_cargo'] ?? null;

                    if ($id_cargoRaw !== null && $id_cargoRaw !== '' && is_numeric($id_cargoRaw)) {
                        $oPermActividades = $this->makePermisosActividades();
                        $oPermActividades->setActividad($id_activ);
                        $permiso_ver = $oPermActividades->havePermisoSacd((int) $id_cargoRaw, $propio);
                    }
                }
                return $permiso_ver;
            case 'ActividadCargoNoSacd':
                return ($propiedad === 'id_nom'
                    && (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom))
                    && $this->permCargo($id_activ));
            case 'ActividadCargoSacd':
                return ($propiedad === 'id_nom'
                    && (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom))
                    && $this->permCargoSacd($id_activ));
            case 'Asistente':
                return ($propiedad === 'id_nom'
                    && (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom))
                    && $this->permAsiste($id_activ));
        }
        return false;
    }

    private function permCargo(int $id_activ): bool
    {
        $oPermActividades = $this->makePermisosActividades();
        $oPermActividades->setActividad($id_activ);
        $oPermActividades->setFasesCompletadas($this->fasesAsIntList());
        $oPermSacd = $oPermActividades->getPermisoOn('cargos');
        return $oPermSacd->have_perm_activ('ver');
    }

    private function permCargoSacd(int $id_activ): bool
    {
        $oPermActividades = $this->makePermisosActividades();
        $oPermActividades->setActividad($id_activ);
        $oPermActividades->setFasesCompletadas($this->fasesAsIntList());
        $oPermSacd = $oPermActividades->getPermisoOn('sacd');
        return $oPermSacd->have_perm_activ('ver');
    }

    private function permAsiste(int $id_activ): bool
    {
        $oPermActividades = $this->makePermisosActividades();
        $oPermActividades->setActividad($id_activ);
        $oPermActividades->setFasesCompletadas($this->fasesAsIntList());
        $oPermAsisSacd = $oPermActividades->getPermisoOn('asistentesSacd');
        return $oPermAsisSacd->have_perm_activ('ver');
    }

    private function makePermisosActividades(): PermisosActividades
    {
        $resolved = DependencyResolver::make(PermisosActividades::class, ['idUsuario' => $this->id_usuario]);
        if (!$resolved instanceof PermisosActividades) {
            throw new \RuntimeException('PermisosActividades could not be resolved');
        }

        return $resolved;
    }

    public function setId_schema_cmb(int $id_schema_cmb): void
    {
        $this->id_schema_cmb = $id_schema_cmb;
    }

    public function setId_item_cmb(int $id_item_cmb): void
    {
        $this->id_item_cmb = $id_item_cmb;
    }

    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    public function setObjeto(string $sObjeto): void
    {
        $this->sObjeto = $sObjeto;
    }

    /**
     * @param list<int|string> $aFases_cmb
     */
    public function setFasesCmb(array $aFases_cmb): void
    {
        $this->aFases_cmb = $aFases_cmb;
    }

    /**
     * @return list<int>
     */
    private function fasesAsIntList(): array
    {
        $result = [];
        foreach ($this->aFases_cmb as $fase) {
            if (is_numeric($fase)) {
                $result[] = (int) $fase;
            }
        }

        return $result;
    }
}
