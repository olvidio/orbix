<?php

namespace notas\model;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use RuntimeException;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\notas\domain\contracts\PersonaNotaCertificadoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\PersonaNotaPk;
use src\notas\domain\value_objects\TipoActa;
use src\notas\infrastructure\repositories\PgPersonaNotaCertificadoRepository;
use src\personas\domain\entity\Persona;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

class EditarPersonaNota
{
    private string $msg_err = '';
    private int $id_nom;
    private int $id_asignatura;
    private int $id_nivel;
    private int $tipo_acta;
    private PersonaNota $personaNota;

    public function __construct(PersonaNota $oPersonaNota)
    {
        $this->personaNota = $oPersonaNota;
        $this->id_nom = $oPersonaNota->getId_nom();
        $this->id_nivel = $oPersonaNota->getId_nivel();
        $this->id_asignatura = $oPersonaNota->getId_asignatura();
        $this->tipo_acta = $oPersonaNota->getTipo_acta();
    }

    public function eliminar(): void
    {
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él
        if (!empty($this->id_nom) && !empty($this->id_asignatura) && !empty($this->id_nivel) && !empty($this->tipo_acta)) {
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
            $oPersonaNotaDB = $PersonaNotaDBRepository->findById($this->id_nom, $this->id_nivel, $this->tipo_acta);
            if ($PersonaNotaDBRepository->Eliminar($oPersonaNotaDB) === false) {
                $err = end($_SESSION['errores']);
                throw new RunTimeException(sprintf(_("No se ha eliminado la Nota: %s"), $err));
            }
        }
    }

    public function nuevoSolamenteDl(): array
    {
        $a_ReposPersonaNota = $this->getReposPersonaNota((array)$this->getDatosRegionStgr(), $this->getId_schema_persona());
        unset($a_ReposPersonaNota['nota_real']);

        return $this->crear_nueva_personaNota_para_cada_objeto_del_array($a_ReposPersonaNota);
    }

    public function nuevo(): array
    {
        $a_ReposPersonaNota = $this->getReposPersonaNota((array)$this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->crear_nueva_personaNota_para_cada_objeto_del_array($a_ReposPersonaNota);
    }

    /**
     * Se separa de la función 'nuevo' para que los test puedan manipular los objetos
     * @param array $a_ReposPersonaNota ['nota_real' => PersonaNotaDB, 'nota_certificado' => $oPersonaNotaCertificadoDB]
     * @param string $esquema_region_stgr únicamente para los traslados.
     * @return array
     */
    public function crear_nueva_personaNota_para_cada_objeto_del_array(array $a_ReposPersonaNota, string $esquema_region_stgr = ''): array
    {
        $rta = [];
        $guardar = TRUE;

        $id_nom = $this->personaNota->getId_nom();
        $id_nivel = $this->personaNota->getId_nivel();
        $id_asignatura = $this->personaNota->getIdAsignaturaVo()->value();
        $id_situacion = $this->personaNota->getIdSituacionVo()->value();
        $acta = $this->personaNota->getActa();
        $f_acta = $this->personaNota->getF_acta();
        $tipo_acta = $this->personaNota->getTipoActaVo()?->value();
        $preceptor = $this->personaNota->isPreceptor();
        $id_preceptor = $this->personaNota->getId_preceptor();
        $detalle = $this->personaNota->getDetalle();
        $epoca = $this->personaNota->getEpoca();
        $id_activ = $this->personaNota->getId_activ();
        $nota_num = $this->personaNota->getNotaNumVo()?->value();
        $nota_max = $this->personaNota->getNotaMaxVo()?->value();

        // Pongo las notas en la dl de la persona, esperando al certificado
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        if (array_key_exists('repo_real', $a_ReposPersonaNota)) {
            $PersonaNotaRepository = $a_ReposPersonaNota['repo_real'];
            // comprobar si existe, para lanzar un aviso y no hacer nada:
            if ($PersonaNotaRepository instanceof PersonaNotaDlRepositoryInterface) {
                //$oDbl = $oPersonaNotaDB->getoDbl(); // asegurarme que estoy consultando en el mismo esquema
                //$gesPersonaNota = new GestorPersonaNotaDlDB();
                //$gesPersonaNota->setoDbl($oDbl);
                $cPersonaNota = $PersonaNotaRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_nivel' => $id_nivel]);
                if (!empty($cPersonaNota)) {
                    $oPersonaNota2 = $cPersonaNota[0];
                    if (!is_null($oPersonaNota2)) {
                        $err = sprintf(_("Ya existe esta nota. id_nom: %s, id_asignatura: %s, acta: %s, tipo_acta: %s"), $oPersonaNota2->getId_nom(), $oPersonaNota2->getId_asignatura(), $oPersonaNota2->getActa(), $oPersonaNota2->getTipo_acta());
                        throw new RunTimeException($err);
                    }
                }
            }

            // En el caso de traslados, si el tipo de acta es un certificado,
            if ($tipo_acta === TipoActa::FORMATO_CERTIFICADO && !empty($esquema_region_stgr)) {
                // puede ser que haga referencia a e_notas_dl o a e_notas_otra_region_stgr
                if ($PersonaNotaRepository instanceof PersonaNotaOtraRegionStgrRepositoryInterface) {
                    // Si es certificado debería de ser de otra región, y por tanto no guardo nada
                    // en la tabla e_notas_otra_region_stgr de mi región stgr, ya lo tendrá la original
                    $guardar = FALSE;
                }
            }

            if ($PersonaNotaRepository instanceof PersonaNotaDlRepositoryInterface) {
                $oPersonaNotaDB = new PersonaNota();
            }
            if ($PersonaNotaRepository instanceof PersonaNotaOtraRegionStgrRepositoryInterface) {
                $oPersonaNotaDB = new PersonaNotaOtraRegionStgr();
            }

            $oPersonaNotaDB->setId_nom($id_nom);
            $oPersonaNotaDB->setId_nivel($id_nivel);
            $oPersonaNotaDB->setId_asignatura($id_asignatura);
            $oPersonaNotaDB->setId_situacion($id_situacion);
            $oPersonaNotaDB->setF_acta($f_acta);
            $oPersonaNotaDB->setTipo_acta($tipo_acta);
            // comprobar valor del acta
            if (!empty($acta)) {
                if ($tipo_acta === TipoActa::FORMATO_CERTIFICADO) {
                    $oPersonaNotaDB->setActa($acta);
                }
                if ($tipo_acta === TipoActa::FORMATO_ACTA) {
                    $valor = Acta::inventarActa($acta, $f_acta);
                    $oPersonaNotaDB->setActa($valor);
                }
            }
            $oPersonaNotaDB->setPreceptor($preceptor);
            $oPersonaNotaDB->setId_preceptor($id_preceptor);
            $oPersonaNotaDB->setDetalle($detalle);
            $oPersonaNotaDB->setEpoca($epoca);
            $oPersonaNotaDB->setId_activ($id_activ);
            $oPersonaNotaDB->setNota_num($nota_num);
            $oPersonaNotaDB->setNota_max($nota_max);
            if ($guardar) {
                $PersonaNotaRepository->Guardar($oPersonaNotaDB);
            }

            // lo recupero de la base de datos, porque falta el id_schema
            $rta['nota_real'] = $PersonaNotaRepository->findByPk(PersonaNotaPk::fromArray(['id_nom' => $id_nom, 'id_nivel' => $id_nivel, 'tipo_acta' => $tipo_acta]));

            // si no está abierto, hay que abrir el dossier para esta persona
            // si es una persona de paso, No hace falta
            if ($id_nom > 0) {
                $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
                $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $id_nom, 'id_tipo_dossier' => 1303]));
                $oDossier->abrir();
                $DosierRepository->Guardar($oDossier);
            }
        }

        // Pongo las notas en la dl de la persona, esperando al certificado
        if (array_key_exists('repo_certificado', $a_ReposPersonaNota)) {
            $PersonaNotaRepository = $a_ReposPersonaNota['repo_certificado'];
            // en el caso de traslados, comprobar que no se tenga la nota real
            // buscarla en OtraRegionStgr (sobreescribo todos los valores por los originales)
            //???if ($tipo_acta === TipoActa::FORMATO_CERTIFICADO && $oPersonaNotaDB instanceof PersonaNotaDB && !empty($esquema_region_stgr)) {
            if ($tipo_acta === TipoActa::FORMATO_CERTIFICADO && !empty($esquema_region_stgr)) {
                $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
                $cPersonaNotasOtraRegion = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
                if (!empty($cPersonaNotasOtraRegion)) {
                    $personaNotaOriginal = $cPersonaNotasOtraRegion[0];
                    $id_nivel = $personaNotaOriginal->getId_nivel();
                    $id_asignatura = $personaNotaOriginal->getId_asignatura();
                    $id_situacion = $personaNotaOriginal->getId_situacion();
                    $acta = $personaNotaOriginal->getActa();
                    $f_acta = $personaNotaOriginal->getF_acta();
                    $tipo_acta = $personaNotaOriginal->getTipo_acta();
                    $preceptor = $personaNotaOriginal->isPreceptor();
                    $id_preceptor = $personaNotaOriginal->getId_preceptor();
                    $detalle = $personaNotaOriginal->getDetalle();
                    $epoca = $personaNotaOriginal->getEpoca();
                    $id_activ = $personaNotaOriginal->getId_activ();
                    $nota_num = $personaNotaOriginal->getNota_num();
                    $nota_max = $personaNotaOriginal->getNota_max();
                }
            } else {
                // valores que se cambian:
                $id_situacion = NotaSituacion::FALTA_CERTIFICADO;
                $detalle = empty($detalle) ? "$acta" : "$acta ($detalle)";
                $acta = _("falta certificado");
                $tipo_acta = TipoActa::FORMATO_CERTIFICADO;
            }

            if ($PersonaNotaRepository instanceof PersonaNotaDlRepositoryInterface) {
                $oPersonaNotaCertificadoDB = new PersonaNota();
            }
            if ($PersonaNotaRepository instanceof PersonaNotaCertificadoRepositoryInterface) {
                $oPersonaNotaCertificadoDB = new PersonaNota();
            }
            if ($PersonaNotaRepository instanceof PersonaNotaOtraRegionStgrRepositoryInterface) {
                $oPersonaNotaCertificadoDB = new PersonaNotaOtraRegionStgr();
            }

            $oPersonaNotaCertificadoDB->setId_nom($id_nom);
            $oPersonaNotaCertificadoDB->setId_nivel($id_nivel);
            $oPersonaNotaCertificadoDB->setId_asignatura($id_asignatura);
            $oPersonaNotaCertificadoDB->setId_situacion($id_situacion);
            $oPersonaNotaCertificadoDB->setActa($acta);
            $oPersonaNotaCertificadoDB->setDetalle($detalle);
            $oPersonaNotaCertificadoDB->setTipo_acta($tipo_acta);
            $oPersonaNotaCertificadoDB->setF_acta($f_acta);
            $oPersonaNotaCertificadoDB->setPreceptor($preceptor);
            $oPersonaNotaCertificadoDB->setId_preceptor($id_preceptor);
            $oPersonaNotaCertificadoDB->setEpoca($epoca);
            $oPersonaNotaCertificadoDB->setId_activ($id_activ);
            $oPersonaNotaCertificadoDB->setNota_num($nota_num);
            $oPersonaNotaCertificadoDB->setNota_max($nota_max);

            $PersonaNotaRepository->Guardar($oPersonaNotaCertificadoDB);

            // borrar la original (asegurarme que se ha guardado lo anterior)
            if (!empty($personaNotaOriginal)) {
                $PersonaNotaRepository->Eliminar($personaNotaOriginal);
            }
            // lo recupero de la base de datos, porque falta el id_schema
            $rta['nota_certificado'] = $PersonaNotaRepository->findByPk(PersonaNotaPk::fromArray(['id_nom' => $id_nom, 'id_nivel' => $id_nivel, 'tipo_acta' => $tipo_acta]));
        }

        return $rta;
    }

    public function editar(int $id_asignatura_real): array
    {
        $a_ObjetosPersonaNota = $this->getReposPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->editar_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota, $id_asignatura_real);
    }

    public function editar_personaNota_para_cada_objeto_del_array(array $a_ObjetosPersonaNota, int $id_asignatura_real): array
    {
        $rta = [];

        $id_nom = $this->personaNota->getId_nom();
        $id_nivel = $this->personaNota->getId_nivel();
        $id_asignatura = $this->personaNota->getIdAsignaturaVo()->value();
        $id_situacion = $this->personaNota->getIdSituacionVo()->value();
        $acta = $this->personaNota->getActa();
        $f_acta = $this->personaNota->getF_acta();
        $tipo_acta = $this->personaNota->getTipoActavo()?->value();
        $preceptor = $this->personaNota->isPreceptor();
        $id_preceptor = $this->personaNota->getId_preceptor();
        $detalle = $this->personaNota->getDetalle();
        $epoca = $this->personaNota->getEpoca();
        $id_activ = $this->personaNota->getIdActivVo()->value();
        $nota_num = $this->personaNota->getNotaNumVo()?->value();
        $nota_max = $this->personaNota->getNotaMaxVo()?->value();

        if (array_key_exists('repo_real', $a_ObjetosPersonaNota)) {
            $PersonaNotaRepository = $a_ObjetosPersonaNota['repo_real'];
            if ($PersonaNotaRepository instanceof PersonaNotaDlRepositoryInterface) {
                $oPersonaNotaDB = new PersonaNota();
            }
            if ($PersonaNotaRepository instanceof PersonaNotaCertificadoRepositoryInterface) {
                $oPersonaNotaDB = new PersonaNota();
            }
            if ($PersonaNotaRepository instanceof PersonaNotaOtraRegionStgrRepositoryInterface) {
                $oPersonaNotaDB = new PersonaNotaOtraRegionStgr();
            }

            $oPersonaNotaDB->setId_nom($id_nom);
            $oPersonaNotaDB->setId_nivel($id_nivel);
            if (!empty($id_asignatura_real)) {
                $oPersonaNotaDB->setId_asignatura($id_asignatura_real);
            }

            $oPersonaNotaDB->setId_asignatura($id_asignatura);
            $oPersonaNotaDB->setId_situacion($id_situacion);
            $oPersonaNotaDB->setF_acta($f_acta);
            $oPersonaNotaDB->setTipo_acta($tipo_acta);
            // comprobar valor del acta
            if (!empty($acta)) {
                if ($tipo_acta === TipoActa::FORMATO_CERTIFICADO) {
                    $oPersonaNotaDB->setActa($acta);
                }
                if ($tipo_acta === TipoActa::FORMATO_ACTA) {
                    $valor = Acta::inventarActa($acta, $f_acta);
                    $oPersonaNotaDB->setActa($valor);
                }
            }
            if (empty($preceptor)) {
                $oPersonaNotaDB->setPreceptor(false);
                $oPersonaNotaDB->setId_preceptor(null);
            } else {
                $oPersonaNotaDB->setPreceptor($preceptor);
                $oPersonaNotaDB->setId_preceptor($id_preceptor);
            }
            $oPersonaNotaDB->setDetalle($detalle);
            $oPersonaNotaDB->setEpoca($epoca);
            $oPersonaNotaDB->setId_activ($id_activ);
            $oPersonaNotaDB->setNota_num($nota_num);
            $oPersonaNotaDB->setNota_max($nota_max);

            $PersonaNotaRepository->Guardar($oPersonaNotaDB);

            // lo recupero de la base de datos, porque falta el id_schema
            $rta['nota_real'] = $PersonaNotaRepository->findByPk(PersonaNotaPk::fromArray(['id_nom' => $id_nom, 'id_nivel' => $id_nivel, 'tipo_acta' => $tipo_acta]));
        }

        // Pongo las notas en la dl de la persona, esperando al certificado
        if (array_key_exists('repo_certificado', $a_ObjetosPersonaNota)) {
            $new_detalle = empty($detalle) ? "$acta" : "$acta ($detalle)";
            $PersonaNotaCertificadoRepository = $a_ObjetosPersonaNota['repo_certificado'];

           if ($PersonaNotaCertificadoRepository instanceof PersonaNotaDlRepositoryInterface) {
                $oPersonaNotaCertificadoDB = new PersonaNota();
            }
            if ($PersonaNotaCertificadoRepository instanceof PersonaNotaCertificadoRepositoryInterface) {
                $oPersonaNotaCertificadoDB = new PersonaNota();
            }
            if ($PersonaNotaCertificadoRepository instanceof PersonaNotaOtraRegionStgrRepositoryInterface) {
                $oPersonaNotaCertificadoDB = new PersonaNotaOtraRegionStgr();
            }

            // comprobar que no existe con una situación distinta a la 'falta certificado
            if ($PersonaNotaCertificadoRepository instanceof PgPersonaNotaCertificadoRepository) {
                $oDbl = $PersonaNotaCertificadoRepository->getoDbl(); // asegurarme que estoy consultando en el mismo esquema
                $aWhere = ['id_nom' => $id_nom,
                    'id_nivel' => $id_nivel,
                    'id_situacion' => NotaSituacion::FALTA_CERTIFICADO,
                ];
                $aOperador = ['id_situacion' => '!='];
                $cPersonaNota = $PersonaNotaCertificadoRepository->getPersonaNotas($aWhere, $aOperador);
                if (!empty($cPersonaNota)) {
                    $oPersonaNota2 = $cPersonaNota[0];
                    if (!is_null($oPersonaNota2)) {
                        $oPersona = Persona::findPersonaEnGlobal($id_nom);
                        $nom = $oPersona?->getPrefApellidosNombre() ?? _("No encuentro");

                        $err = sprintf(_("%s ya tiene puesta nota para esta asignatura en su r/dl."), $nom);
                        $err .= "\n" . _("Si ha guardado este acta anteriormente puede ignorar este aviso.");
                        $err .= "\n" . _("Si es la primera vez que 'guarda las notas en tessera' sería conveniente
                            revisar con su r la situación de esta persona");
                        throw new RunTimeException($err);
                    }
                }
            }

            $oPersonaNotaCertificadoDB->setId_nom($id_nom);
            $oPersonaNotaCertificadoDB->setId_nivel($id_nivel);
            if (!empty($id_asignatura_real)) {
                $oPersonaNotaCertificadoDB->setId_asignatura($id_asignatura_real);
            }
            $oPersonaNotaCertificadoDB->setId_asignatura($id_asignatura);
            $oPersonaNotaCertificadoDB->setId_situacion(NotaSituacion::FALTA_CERTIFICADO);
            $oPersonaNotaCertificadoDB->setActa(_("falta certificado"));
            $oPersonaNotaCertificadoDB->setDetalle($new_detalle);
            $oPersonaNotaCertificadoDB->setTipo_acta(TipoActa::FORMATO_CERTIFICADO);

            $oPersonaNotaCertificadoDB->setF_acta($f_acta);
            $oPersonaNotaCertificadoDB->setPreceptor($preceptor);
            $oPersonaNotaCertificadoDB->setId_preceptor($id_preceptor);
            $oPersonaNotaCertificadoDB->setEpoca($epoca);
            $oPersonaNotaCertificadoDB->setId_activ($id_activ);
            $oPersonaNotaCertificadoDB->setNota_num($nota_num);
            $oPersonaNotaCertificadoDB->setNota_max($nota_max);

            $PersonaNotaCertificadoRepository->Guardar($oPersonaNotaCertificadoDB);
            // lo recupero de la base de datos, porque falta el id_schema
            $rta['nota_certificado'] = $PersonaNotaCertificadoRepository->findByPk(PersonaNotaPk::fromArray(['id_nom' => $id_nom, 'id_nivel' => $id_nivel, 'tipo_acta' => TipoActa::FORMATO_CERTIFICADO]));
        }

        return $rta;
    }

    public function getDatosRegionStgr(string $dele = ''): array
    {
        $gesDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        try {
            $a_mi_region_stgr = $gesDelegacion->mi_region_stgr($dele);
        } catch (\RuntimeException $e) {
            $msg = _("Problema con los datos en la tabla de delegaciones");
            $msg .= "\r\n";
            $msg .= $e->getMessage();
            die($msg);
        }

        return $a_mi_region_stgr;
    }

    /**
     * Se lo paso por constructor para poder hacer test con otra información
     * @return PersonaNota[]
     */
    public function getReposPersonaNota(array $a_mi_region_stgr, int $id_schema_persona): array
    {
        /* Hace falta saber en que esquema está la persona, para poner la nota en la tabla del
         * esquema correspondiente.
         *    - si es de paso en la tabla 'e_notas_otra_region_stgr' de mi region stgr
         *    - si es de otra region del stgr, en 'e_notas_otra_region_stgr' de mi region stgr
         *          y una copia en 'e_notas_dl' de la region de la persona, con:
         *              + acta = 'falta certificado de '
         *              + id_situacion = 13 (falta certificado)
         *              + tipo_acta = 2 = certificado
         *     - si es de mi región del stgr, en la tabla 'e_notas_dl' de mi dl/region
         */

        /* región que está introduciendo la nota:
         *    a) la que organiza los ca
         *    b) la propia del alumno mediante dossiers.
         */

        $mi_region_stgr = $a_mi_region_stgr['region_stgr'];
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];
        //$id_esquema_region_stgr = $a_mi_region_stgr['id_esquema_region_stgr'];
        $mi_id_schema = $a_mi_region_stgr['mi_id_schema'];

        $gesSchemas = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);
        $cSchemas = $gesSchemas->getDbSchemas(['id' => $id_schema_persona]);
        $nombre_schema_persona = $cSchemas[0]->getSchema();
        if (empty($nombre_schema_persona)) {
            $msg = sprintf(_("No se encuentra el nombre del esquema de la persona con id_nom: %s"), $id_schema_persona);
            throw new RunTimeException($msg);
        }

        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        if ($nombre_schema_persona === 'restov' || $nombre_schema_persona === 'restof') {
            // guardar en e_notas_otra_region_stgr
            $rta['repo_real'] = $PersonaNotaOtraRegionStgrRepository;
        } else {
            $a_reg = explode('-', $nombre_schema_persona);
            $new_dele = substr($a_reg[1], 0, -1); // quito la v o la f.
            $datos_reg_destino = $this->getDatosRegionStgr($new_dele);

            // para los traslados incluir el caso de que las dos tengan el mismo esquema_region_stgr
            if ($id_schema_persona === $mi_id_schema || $esquema_region_stgr === $datos_reg_destino['esquema_region_stgr']) {
                // normal
                $PersonasNotaDlRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
                if ($esquema_region_stgr === $datos_reg_destino['esquema_region_stgr']) {
                    // Conectar con la tabla de la dl
                    $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
                    // se debe conectar con la region del stgr padre
                    $oConfigDB = new ConfigDB($db); //de la database sv/sf
                    $config = $oConfigDB->getEsquema($nombre_schema_persona);
                    $oConexion = new DBConnection($config);
                    $oDbl = $oConexion->getPDO();

                    $PersonasNotaDlRepository->setoDbl($oDbl);
                }
                $rta['repo_real'] = $PersonasNotaDlRepository;
            } else {
                // guardar en e_notas_otra_region_stgr
                $rta['repo_real'] = $PersonaNotaOtraRegionStgrRepository;
                $rta['repo_certificado'] =  $GLOBALS['container']->make(PersonaNotaCertificadoRepositoryInterface::class, ['nombre_schema' => $nombre_schema_persona]);
            }
        }

        return $rta;
    }

    private function getId_schema_persona(): int
    {
        // para saber a que schema pertenece la persona
        $oPersona = Persona::findPersonaEnGlobal($this->id_nom);
        if ($oPersona === null) {
            $msg_err = "No encuentro a nadie con id_nom: $this->id_nom en  " . __FILE__ . ": line " . __LINE__;
            throw new RunTimeException($msg_err);
        }
        return $oPersona->getId_schema();
    }

}