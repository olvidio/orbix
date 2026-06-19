<?php

namespace src\dbextern\domain;

use PDO;
use PDOStatement;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\IdMatchPersona;
use src\dbextern\domain\entity\PersonaBDU;
use src\dbextern\infrastructure\persistence\postgresql\OdbcDlListasRepository;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaGlobal;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaNax;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSSSC;
use src\personas\domain\entity\TelecoPersona;
use src\permisos\domain\XPermisos;
use src\personas\domain\Trasladar;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\GlobalPdo;

class SincroDB
{
    private string $tipo_persona = '';
    private int $id_tipo = 0;

    /** @var list<PersonaBDU>|null */
    private ?array $cPersonasListas = null;

    private string $region = '';
    private string $dl_listas = '';

    /** @var array<string, int> */
    private array $aCentros = [];

    /** @var array<string, string>|null */
    private ?array $aDlListas2Orbix = null;

    /** @var array<string, string>|null */
    private ?array $aDlOrbix2listas = null;

    private string $path_ini = '';
    private string $tabla;

    public function __construct(
        private PersonaBDURepositoryInterface $personaBDURepository,
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private PersonaDlRepositoryFactoryInterface $personaDlRepositoryFactory,
        private TelecoPersonaDlRepositoryInterface $telecoPersonaDlRepository,
        private OdbcDlListasRepository $dlListasRepository,
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private Trasladar $trasladar,
    ) {
        $this->tabla = 'tmp_bdu';
    }

    public function getTipo_persona(): string
    {
        return $this->tipo_persona;
    }

    public function getId_tipo(): int
    {
        return $this->id_tipo;
    }

    public function setTipo_persona(string $tipo_persona): void
    {
        $this->tipo_persona = $tipo_persona;
        $oPerm = $_SESSION['oPerm'] ?? null;
        $id_tipo = match ($tipo_persona) {
            'n' => ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('sm')) ? 1 : 0,
            'a' => ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('agd')) ? 2 : 0,
            's' => ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('sg')) ? 3 : 0,
            'sssc' => ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('des')) ? 4 : 0,
            default => 0,
        };
        if ($id_tipo === 0 && !in_array($tipo_persona, ['n', 'a', 's', 'sssc'], true)) {
            $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
            exit($err_switch);
        }
        $this->id_tipo = $id_tipo;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getDlListas(): string
    {
        return $this->dl_listas;
    }

    public function setDlListas(string $dl): void
    {
        $this->dl_listas = $dl;
    }

    /**
     * @return array<string, int>
     */
    public function getCentros(): array
    {
        return $this->aCentros;
    }

    /**
     * @param array<string, int> $aCentros
     */
    public function setCentros(array $aCentros): void
    {
        $this->aCentros = $aCentros;
    }

    private function cargarArrayDl(): void
    {
        $cDllistas = $this->dlListasRepository->getDlListas();
        $this->aDlListas2Orbix = [];
        $this->aDlOrbix2listas = [];
        foreach ($cDllistas as $oDlListas) {
            $dl_listas = $oDlListas->getDl();
            $nombre_dl = $oDlListas->getNombre_dl();

            preg_match('/(cr) (\w*)$/', $nombre_dl, $matches);
            if (!empty($matches[1])) {
                $reg = $matches[2];
                $dl_orbix = 'cr' . $reg;
            } else {
                $dl_orbix = 'dl' . $dl_listas;
            }

            $this->aDlListas2Orbix[$dl_listas] = $dl_orbix;
            $this->aDlOrbix2listas[$dl_orbix] = $dl_listas;
        }
    }

    public function dlListas2Orbix(string $dl_listas): string|false
    {
        if ($this->aDlListas2Orbix === null) {
            $this->cargarArrayDl();
        }

        if (empty($this->aDlListas2Orbix[$dl_listas])) {
            $msg = sprintf(_("No se encuentra la dl %s en la tabla Aux de listas"), $dl_listas);
            echo $msg;

            return false;
        }

        return $this->aDlListas2Orbix[$dl_listas];
    }

    public function dlOrbix2Listas(string $dl_orbix): string|false
    {
        if ($this->aDlOrbix2listas === null) {
            $this->cargarArrayDl();
        }

        if (empty($this->aDlOrbix2listas[$dl_orbix])) {
            $msg = sprintf(_("No se encuentra la dl %s en la tabla Aux de listas"), $dl_orbix);
            echo $msg;

            return false;
        }

        return $this->aDlOrbix2listas[$dl_orbix];
    }

    /**
     * @return list<PersonaBDU>
     */
    public function getPersonasBDU(): array
    {
        if ($this->cPersonasListas === null) {
            $Query = "SELECT * FROM $this->tabla 
                        WHERE identif::text LIKE '$this->id_tipo%' AND  Dl='$this->dl_listas' 
                            AND (pertenece_r='$this->region' OR compartida_con_r='$this->region') ";
            $cPersonasBDU = $this->personaBDURepository->getPersonaBDUQuery($Query);

            if (array_key_exists($this->region, ConfigGlobal::REGIONES_CON_DL)) {
                $cPersonasBDU_n = [];
                foreach (ConfigGlobal::REGIONES_CON_DL[$this->region] as $dl_n) {
                    $Query = "SELECT * FROM $this->tabla
                          WHERE identif::text LIKE '$this->id_tipo%' AND  Dl='$dl_n'
                               AND (pertenece_r='$this->region' OR compartida_con_r='$this->region') ";
                    $cPersonasBDU_n[] = $this->personaBDURepository->getPersonaBDUQuery($Query);
                }
                foreach ($cPersonasBDU_n as $chunk) {
                    $cPersonasBDU = array_merge($cPersonasBDU, $chunk);
                }
            }
            $this->cPersonasListas = $cPersonasBDU;
        }

        return $this->cPersonasListas;
    }

    public function union_automatico(PersonaBDU $oPersonaListas): bool
    {
        $id_nom_listas = $oPersonaListas->getIdentif();
        $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
        $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
        $f_nacimiento = $oPersonaListas->getFecha_Naci();
        $f_nacimiento_txt = $f_nacimiento;
        $nombre = $oPersonaListas->getNombre();

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = $this->tipo_persona;
        $aWhere['apellido1'] = $apellido1_sinprep;
        if ($apellido2_sinprep !== '') {
            $aWhere['apellido2'] = $apellido2_sinprep;
        }
        $aWhere['f_nacimiento'] = "'$f_nacimiento_txt'";
        $aWhere['nom'] = trim($nombre);

        $cPersonasDl = $this->personaDlRepository->getPersonas($aWhere, $aOperador);
        if (count($cPersonasDl) === 1) {
            $oPersonaDl = $cPersonasDl[0];
            $id_nom = $oPersonaDl->getId_nom();

            $oIdMatch = new IdMatchPersona();
            $oIdMatch->setId_listas($id_nom_listas);
            $oIdMatch->setId_orbix($id_nom);
            $oIdMatch->setId_tabla($this->tipo_persona);

            if ($this->idMatchRepository->Guardar($oIdMatch) === false) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function posiblesBDU(int $id_nom_orbix): array
    {
        $oPersonaDl = $this->personaDlRepository->findById($id_nom_orbix);
        if ($oPersonaDl === null) {
            return [];
        }

        $apellido1 = str_replace("'", "''", $oPersonaDl->getApellido1());

        $Query = "SELECT * FROM $this->tabla
                        WHERE identif::text LIKE '$this->id_tipo%' AND  ApeNom LIKE '%" . $apellido1 . "%'
                            AND (pertenece_r='$this->region' OR compartida_con_r='$this->region') ";
        $cPersonasBDU = $this->personaBDURepository->getPersonaBDUQuery($Query);

        $a_lista_bdu = [];
        foreach ($cPersonasBDU as $oPersonaBDU) {
            $id_nom_listas = $oPersonaBDU->getIdentif();
            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
            if ($cIdMatch !== []) {
                continue;
            }
            $a_lista_bdu[] = [
                'id_nom' => $id_nom_listas,
                'ape_nom' => $oPersonaBDU->getApenom(),
                'nombre' => $oPersonaBDU->getNombre(),
                'dl_persona' => $oPersonaBDU->getDl(),
                'apellido1' => $oPersonaBDU->getApellido1(),
                'apellido2' => $oPersonaBDU->getApellido2(),
                'f_nacimiento' => empty($oPersonaBDU->getFecha_Naci()) ? '??' : $oPersonaBDU->getFecha_Naci(),
                'pertenece_r' => $oPersonaBDU->getPertenece_r(),
                'compartida_con_r' => $oPersonaBDU->getCompartida_con_r(),
            ];
        }

        return $a_lista_bdu;
    }

    /**
     * @return list<list<array<string, mixed>>>
     */
    public function posiblesOrbixOtrasDl(int $id_nom_listas): array
    {
        $oDBR = GlobalPdo::get('oDBR');
        $qRs = $oDBR->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
        if ($qRs === false) {
            return [];
        }
        $aEsquemas = $qRs->fetchAll(PDO::FETCH_ASSOC);

        $qRs = $oDBR->query('SHOW search_path');
        if ($qRs === false) {
            return [];
        }
        $a_posibles = [];
        foreach ($aEsquemas as $esquemaName) {
            $esquema = $esquemaName['schemaname'];
            if (strpos($esquema, '-')) {
                $a_reg = explode('-', $esquema);
                $reg = $a_reg[0];
                $dl = substr($a_reg[1], 0, -1);
                if ($reg === $dl) {
                    continue;
                }
            }
            if (in_array($esquema, ['global', 'public', 'publicv', 'restov'], true)) {
                continue;
            }
            $a_lista_orbix = $this->posiblesOrbix($id_nom_listas, $esquema);
            if ($a_lista_orbix !== []) {
                $a_posibles[] = $a_lista_orbix;
            }
        }

        return $a_posibles;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function posiblesOrbix(int $id_nom_listas, string $esquema = ''): array
    {
        $oPersonaBDU = $this->personaBDURepository->findById($id_nom_listas);
        if ($oPersonaBDU === null) {
            return [];
        }

        $apellido1_sinprep = $oPersonaBDU->getApellido1_sinprep();
        $tokens = explode(' ', $apellido1_sinprep);
        $apellido1_sinprep_c = $tokens[0];
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = $this->tipo_persona;
        $aWhere['situacion'] = 'A';
        $aWhere['apellido1'] = $apellido1_sinprep_c;
        $aOperador['apellido1'] = 'sin_acentos';
        $aWhere['_ordre'] = 'apellido1, apellido2, nom';

        $personaDlRepository = $this->personaDlRepositoryFactory->create();
        $oDB = null;
        if ($esquema !== '') {
            $oDB = $this->conexion($esquema);
            $personaDlRepository = $this->personaDlRepositoryFactory->createWithConnection($oDB);
        }
        $cPersonasDl = $personaDlRepository->getPersonas($aWhere, $aOperador);
        $a_lista_orbix = [];
        foreach ($cPersonasDl as $oPersonaDl) {
            $id_nom = $oPersonaDl->getId_nom();
            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_orbix' => $id_nom]);
            if ($cIdMatch !== []) {
                continue;
            }
            $f_nacimiento = $oPersonaDl->getF_nacimiento()?->getFromLocal();
            $a_lista_orbix[] = [
                'esquema' => $esquema,
                'id_nom' => $id_nom,
                'ape_nom' => $oPersonaDl->getPrefApellidosNombre(),
                'nombre' => $oPersonaDl->getNom(),
                'dl_persona' => $oPersonaDl->getDl(),
                'apellido1' => $oPersonaDl->getApellido1(),
                'apellido2' => $oPersonaDl->getApellido2(),
                'f_nacimiento' => empty($f_nacimiento) ? '??' : $f_nacimiento,
            ];
        }
        if ($oDB !== null) {
            $this->restaurarConexion($oDB);
        }

        return $a_lista_orbix;
    }

    /**
     * @return string|array{error: string}
     */
    public function syncro(PersonaBDU $oPersonaListas, int $id_orbix): string|array
    {
        $msg = '';
        $oHoy = new DateTimeLocal();
        $a_ctr = $this->aCentros;

        $id_nom_listas = $oPersonaListas->getIdentif();
        $ape_nom = $oPersonaListas->getApenom();
        $nombre = $oPersonaListas->getNombre();
        $nx1 = $oPersonaListas->getNx1();
        $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
        $nx2 = $oPersonaListas->getNx2();
        $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
        $f_nacimiento_raw = $oPersonaListas->getFecha_Naci();
        $lugar_nacimiento = $oPersonaListas->getLugar_Naci();

        $dl_listas = $oPersonaListas->getDl();
        $Ctr = $oPersonaListas->getCtr();
        if (!empty($a_ctr[$Ctr])) {
            $id_ubi = $a_ctr[$Ctr];
        } else {
            $id_ubi = 0;
            if ($Ctr === '') {
                $msg = sprintf(_("parece que %s no tiene puesto el ctr en la BDU"), $ape_nom);
            } else {
                $msg = sprintf(_("no se encuentra el ctr %s en la lista de ctr"), $Ctr);
            }
        }

        $Email = $oPersonaListas->getEmail();
        $Tfno_Movil = $oPersonaListas->getTfno_Movil();

        $ce_num = $this->toNullableIntFromCe($oPersonaListas->getCe_num());
        $ce_lugar = $oPersonaListas->getCe_lugar();
        $ce_ini = $this->toNullableIntFromCe($oPersonaListas->getCe_ini());
        $ce_fin = $this->toNullableIntFromCe($oPersonaListas->getCe_fin());

        $inc = $oPersonaListas->getInc();
        $f_inc = $oPersonaListas->getF_inc();
        $encargos = $oPersonaListas->getEncargos();
        $profesion = $oPersonaListas->getProfesion_cargo();

        $id_tipo_persona = substr((string)$id_nom_listas, 0, 1);
        try {
            [$obj_pau, $repoPersona] = match ($id_tipo_persona) {
                '4' => ['PersonaSSSC', $this->personaRepositoryResolver->personaSSSCRepository()],
                '3' => ['PersonaS', $this->personaRepositoryResolver->personaSRepository()],
                '1' => ['PersonaN', $this->personaRepositoryResolver->personaNRepository()],
                '2' => ['PersonaAgd', $this->personaRepositoryResolver->personaAgdRepository()],
                default => throw new \InvalidArgumentException("id_tipo_persona '$id_tipo_persona' no reconocido"),
            };
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }

        $oPersona = $repoPersona->findById($id_orbix);
        if (!($oPersona instanceof PersonaGlobal)) {
            return ['error' => _("No se encontró la persona en Orbix")];
        }

        if ($oPersona->getSituacion() !== 'A') {
            $oPersona->setSituacion('A');
            $oPersona->setF_situacion($oHoy);
        }
        $oPersona->setNom($nombre);
        $oPersona->setNx1($nx1);
        $oPersona->setApellido1($apellido1_sinprep);
        $oPersona->setNx2($nx2);
        $oPersona->setApellido2($apellido2_sinprep);
        $f_nacimiento_local = $f_nacimiento_raw;
        $f_nacimiento_vo = DateTimeLocal::createFromLocal($f_nacimiento_local);
        $oPersona->setF_nacimiento($f_nacimiento_vo instanceof DateTimeLocal ? $f_nacimiento_vo : null);
        $oPersona->setLugar_nacimiento($lugar_nacimiento);
        if ($id_tipo_persona !== '4') {
            $this->aplicarCe($oPersona, $ce_num, $ce_lugar, $ce_ini, $ce_fin);
        }
        $oPersona->setInc($inc);
        $f_inc_vo = DateTimeLocal::createFromLocal($f_inc);
        $oPersona->setF_inc($f_inc_vo instanceof DateTimeLocal ? $f_inc_vo : null);
        $oPersona->setProfesion($profesion);
        $oPersona->setEap($encargos);

        $dl_orbix = $this->dlListas2Orbix($dl_listas);
        if ($dl_orbix !== false) {
            $oPersona->setDl($dl_orbix);
        }

        $oPersona->setId_ctr($id_ubi);

        if ($this->guardarPersona($repoPersona, $obj_pau, $oPersona) === false) {
            exit(_("hay un error, no se ha guardado"));
        }

        if ($Tfno_Movil !== '') {
            $this->guardarTeleco($id_orbix, 2, 5, $Tfno_Movil);
        }
        if ($Email !== '') {
            $this->guardarTeleco($id_orbix, 3, 13, $Email);
        }

        return $msg;
    }

    private function guardarTeleco(int $id_orbix, int $id_tipo_teleco, int $id_desc_teleco, string $num_teleco): void
    {
        $cTelecos = $this->telecoPersonaDlRepository->getTelecosPersona([
            'id_nom' => $id_orbix,
            'id_tipo_teleco' => $id_tipo_teleco,
            'id_desc_teleco' => $id_desc_teleco,
        ]);
        if ($cTelecos !== []) {
            $oTeleco = $cTelecos[0];
            $oTeleco->setNum_teleco($num_teleco);
            $oTeleco->setObserv('de listas');
        } else {
            $newIdItem = $this->telecoPersonaDlRepository->getNewId();
            $oTeleco = new TelecoPersona();
            $oTeleco->setId_item($newIdItem);
            $oTeleco->setId_nom($id_orbix);
            $oTeleco->setId_tipo_teleco($id_tipo_teleco);
            $oTeleco->setId_desc_teleco($id_desc_teleco);
            $oTeleco->setNum_teleco($num_teleco);
            $oTeleco->setObserv('de listas');
        }
        if ($this->telecoPersonaDlRepository->Guardar($oTeleco) === false) {
            echo(_("hay un error, no se ha guardado"));
        }
    }

    public function buscarEnOrbix(int $id_orbix): string
    {
        $this->trasladar->setId_nom($id_orbix);
        $a_esquemas = $this->trasladar->getEsquemas($id_orbix, $this->tipo_persona);
        $esquema = '';
        foreach ($a_esquemas as $info_eschema) {
            if (($info_eschema['situacion'] ?? '') === 'A') {
                $schemaName = $info_eschema['schemaname'] ?? '';
                $esquema = is_string($schemaName) ? $schemaName : '';
            }
        }

        return $esquema;
    }

    private function aplicarCe(
        PersonaGlobal $oPersona,
        ?int $ce_num,
        string $ce_lugar,
        ?int $ce_ini,
        ?int $ce_fin,
    ): void {
        if ($oPersona instanceof PersonaN) {
            $oPersona->setCe($ce_num);
            $oPersona->setCe_lugar($ce_lugar);
            $oPersona->setCe_ini($ce_ini);
            $oPersona->setCe_fin($ce_fin);
        } elseif ($oPersona instanceof PersonaAgd) {
            $oPersona->setCe($ce_num);
            $oPersona->setCe_lugar($ce_lugar);
            $oPersona->setCe_ini($ce_ini);
            $oPersona->setCe_fin($ce_fin);
        } elseif ($oPersona instanceof PersonaS) {
            $oPersona->setCe($ce_num);
            $oPersona->setCe_lugar($ce_lugar);
            $oPersona->setCe_ini($ce_ini);
            $oPersona->setCe_fin($ce_fin);
        } elseif ($oPersona instanceof PersonaNax) {
            $oPersona->setCe($ce_num);
            $oPersona->setCe_lugar($ce_lugar);
            $oPersona->setCe_ini($ce_ini);
            $oPersona->setCe_fin($ce_fin);
        }
    }

    public function conexion(string $esquema): PDO
    {
        if (ConfigGlobal::mi_region_dl() === $esquema) {
            $oDB = GlobalPdo::get('oDB');
        } else {
            $oDB = GlobalPdo::get('oDBR');
        }
        $qRs = $oDB->query('SHOW search_path');
        if ($qRs === false) {
            return $oDB;
        }
        $aPath = $qRs->fetch(PDO::FETCH_ASSOC);
        if (is_array($aPath) && array_key_exists('search_path', $aPath) && is_string($aPath['search_path'])) {
            $this->path_ini = $aPath['search_path'];
        } else {
            $this->path_ini = '';
        }
        $oDB->exec('SET search_path TO public,"' . $esquema . '"');

        return $oDB;
    }

    public function restaurarConexion(PDO $oDB): void
    {
        $oDB->exec("SET search_path TO $this->path_ini");
    }

    private function toNullableIntFromCe(string|int|null $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }

    /**
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo
     */
    private function guardarPersona(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repo,
        string $obj_pau,
        PersonaGlobal $persona,
    ): bool {
        return match ($obj_pau) {
            'PersonaN' => $repo instanceof PersonaNRepositoryInterface && $persona instanceof PersonaN
                ? $repo->Guardar($persona) : false,
            'PersonaAgd' => $repo instanceof PersonaAgdRepositoryInterface && $persona instanceof PersonaAgd
                ? $repo->Guardar($persona) : false,
            'PersonaS' => $repo instanceof PersonaSRepositoryInterface && $persona instanceof PersonaS
                ? $repo->Guardar($persona) : false,
            'PersonaSSSC' => $repo instanceof PersonaSSSCRepositoryInterface && $persona instanceof PersonaSSSC
                ? $repo->Guardar($persona) : false,
            default => false,
        };
    }
}
