<?php

namespace notas\model;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use devel\model\entity\GestorDbSchema;
use dossiers\model\entity\Dossier;
use notas\model\entity\Acta;
use notas\model\entity\GestorPersonaNotaDlDB;
use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;
use notas\model\entity\Nota;
use notas\model\entity\PersonaNotaCertificadoDB;
use notas\model\entity\PersonaNotaDB;
use notas\model\entity\PersonaNotaDlDB;
use notas\model\entity\PersonaNotaOtraRegionStgrDB;
use personas\model\entity\Persona;
use ubis\model\entity\GestorDelegacion;

class EditarPersonaNota
{
    private string $msg_err = '';
    private int $id_nom;
    private int $id_asignatura;
    private int $id_nivel;
    private PersonaNota $personaNota;

    public function __construct(PersonaNota $oPersonaNota)
    {
        $this->personaNota = $oPersonaNota;
        $this->id_nom = $oPersonaNota->getIdNom();
        $this->id_nivel = $oPersonaNota->getIdNivel();
        $this->id_asignatura = $oPersonaNota->getIdAsignatura();
    }

    public function eliminar(): void
    {
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él
        if (!empty($this->id_nom) && !empty($this->id_asignatura) && !empty($this->id_nivel)) {
            $oPersonaNotaDB = new PersonaNotaDB();
            $oPersonaNotaDB->setId_nom($this->id_nom);
            $oPersonaNotaDB->setId_asignatura($this->id_asignatura);
            $oPersonaNotaDB->setId_nivel($this->id_nivel);
            if ($oPersonaNotaDB->DBEliminar() === false) {
                $err = end($_SESSION['errores']);
                throw new \RuntimeException(sprintf(_("No se ha eliminado la Nota: %s"), $err));
            }
        }
    }

    public function nuevo(): array
    {

        $a_ObjetosPersonaNota = $this->getObjetosPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
    }

    /**
     * Se separa de la función 'nuevo' para que los test puedan manipular los objetos
     * @param array $a_ObjetosPersonaNota ['nota' => PersonaNotaDB, 'certificado' => $oPersonaNotaCertificadoDB]
     * @param string $esquema_region_stgr únicamente para los traslados.
     * @return array
     */
    public function crear_nueva_personaNota_para_cada_objeto_del_array(array $a_ObjetosPersonaNota, string $esquema_region_stgr = ''): array
    {
        $rta = [];
        $guardar = TRUE;
        $oPersonaNotaDB = $a_ObjetosPersonaNota['nota'];

        $id_nom = $this->personaNota->getIdNom();
        $id_nivel = $this->personaNota->getIdNivel();
        $id_asignatura = $this->personaNota->getIdAsignatura();
        $id_situacion = $this->personaNota->getIdSituacion();
        $acta = $this->personaNota->getActa();
        $f_acta = $this->personaNota->getFActa();
        $tipo_acta = $this->personaNota->getTipoActa();
        $preceptor = $this->personaNota->isPreceptor();
        $id_preceptor = $this->personaNota->getIdPreceptor();
        $detalle = $this->personaNota->getDetalle();
        $epoca = $this->personaNota->getEpoca();
        $id_activ = $this->personaNota->getIdActiv();
        $nota_num = $this->personaNota->getNotaNum();
        $nota_max = $this->personaNota->getNotaMax();

        // comprobar si existe, para lanzar un aviso y no hacer nada:
        if ($oPersonaNotaDB instanceof PersonaNotaDlDB) {
            $oDbl = $oPersonaNotaDB->getoDbl(); // asegurarme que estoy consultando en el mismo esquema
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            $gesPersonaNota->setoDbl($oDbl);
            $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_nivel' => $id_nivel]);
            if (!empty($cPersonaNota)) {
                $oPersonaNota2 = $cPersonaNota[0];
                if (!is_null($oPersonaNota2)) {
                    $oPersonaNota2->DBCarregar();
                    $err = _("Ya existe esta nota");
                    throw new \RuntimeException($err);
                }
            }
        }


        // En el caso de traslados, si el tipo de acta es un certificado,
        if ($tipo_acta === PersonaNota::FORMATO_CERTIFICADO && !empty($esquema_region_stgr)) {
            // puede ser que haga referencia a e_notas_dl o a e_notas_otra_region_stgr
            if ($oPersonaNotaDB instanceof PersonaNotaOtraRegionStgrDB) {
                // Si es certificado debería de ser de otra región, y por tanto no guardo nada
                // en la tabla e_notas_otra_region_stgr de mi región stgr, ya lo tendrá la original
                $guardar = FALSE;
            }
        }

        $oPersonaNotaDB->setId_nom($id_nom);
        $oPersonaNotaDB->setId_nivel($id_nivel);
        $oPersonaNotaDB->setId_asignatura($id_asignatura);
        $oPersonaNotaDB->setId_situacion($id_situacion);
        $oPersonaNotaDB->setF_acta($f_acta);
        $oPersonaNotaDB->setTipo_acta($tipo_acta);
        // comprobar valor del acta
        if (!empty($acta)) {
            if ($tipo_acta === PersonaNotaDB::FORMATO_CERTIFICADO) {
                $oPersonaNotaDB->setActa($acta);
            }
            if ($tipo_acta === PersonaNotaDB::FORMATO_ACTA) {
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
        if ($guardar && $oPersonaNotaDB->DBGuardar() === false) {
            $err = end($_SESSION['errores']);
            throw new \RuntimeException(sprintf(_("No se ha guardado la Nota: %s"), $err));
        }
        $rta['nota'] = $oPersonaNotaDB;
        // si no está abierto, hay que abrir el dossier para esta persona
        // si es una persona de paso, No hace falta
        if ($id_nom > 0) {
            $oDossier = new Dossier(array('tabla' => 'p', 'id_pau' => $id_nom, 'id_tipo_dossier' => 1303));
            $oDossier->abrir();
            if ($oDossier->DBGuardar() === false) {
                $err = end($_SESSION['errores']);
                throw new \RuntimeException(sprintf(_("No al guardar el dossier: %s"), $err));
            }
        }

        // Pongo las notas en la dl de la persona, esperando al certificado
        if (array_key_exists('certificado', $a_ObjetosPersonaNota)) {
            // en el caso de traslados, comprobar que no se tenga la nota real
            // buscarla en OtraRegionStgr (sobreescribo todos los valores por los originales)
            if ($tipo_acta === PersonaNotaDB::FORMATO_CERTIFICADO && $oPersonaNotaDB instanceof PersonaNotaDB && !empty($esquema_region_stgr)) {
                $gesPersonaNotaOtraRegionDB = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
                $cPersonaNotasOtraRegion = $gesPersonaNotaOtraRegionDB->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
                if (!empty($cPersonaNotasOtraRegion)) {
                    $personaNotaOriginal = $cPersonaNotasOtraRegion[0];
                    $id_nivel = $personaNotaOriginal->getId_nivel();
                    $id_asignatura = $personaNotaOriginal->getId_asignatura();
                    $id_situacion = $personaNotaOriginal->getId_situacion();
                    $acta = $personaNotaOriginal->getActa();
                    $f_acta = $personaNotaOriginal->getF_acta();
                    $tipo_acta = $personaNotaOriginal->getTipo_acta();
                    $preceptor = $personaNotaOriginal->getPreceptor();
                    $id_preceptor = $personaNotaOriginal->getId_preceptor();
                    $detalle = $personaNotaOriginal->getDetalle();
                    $epoca = $personaNotaOriginal->getEpoca();
                    $id_activ = $personaNotaOriginal->getId_activ();
                    $nota_num = $personaNotaOriginal->getNota_num();
                    $nota_max = $personaNotaOriginal->getNota_max();
                }
            } else {
                // valores que se cambian:
                $id_situacion = Nota::FALTA_CERTIFICADO;
                $detalle = empty($detalle) ? "$acta" : "$acta ($detalle)";
                $acta = _("falta certificado");
                $tipo_acta = PersonaNotaDB::FORMATO_CERTIFICADO;
            }

            $oPersonaNotaCertificadoDB = $a_ObjetosPersonaNota['certificado'];
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

            if ($oPersonaNotaCertificadoDB->DBGuardar() === false) {
                throw new \RuntimeException(_("hay un error, no se ha guardado. Nota Certificado"));
            } else {
                // borrar la original (asegurarme que se ha guardado lo anterior)
                if (!empty($personaNotaOriginal)) {
                    $personaNotaOriginal->DBEliminar();
                }
            }
            $rta['certificado'] = $oPersonaNotaCertificadoDB;
        }

        return $rta;
    }

    public function editar(int $id_asignatura_real): array
    {
        $a_ObjetosPersonaNota = $this->getObjetosPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->editar_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota, $id_asignatura_real);
    }

    public function editar_personaNota_para_cada_objeto_del_array(array $a_ObjetosPersonaNota, int $id_asignatura_real): array
    {
        $rta = [];

        $id_nom = $this->personaNota->getIdNom();
        $id_nivel = $this->personaNota->getIdNivel();
        $id_asignatura = $this->personaNota->getIdAsignatura();
        $id_situacion = $this->personaNota->getIdSituacion();
        $acta = $this->personaNota->getActa();
        $f_acta = $this->personaNota->getFActa();
        $tipo_acta = $this->personaNota->getTipoActa();
        $preceptor = $this->personaNota->isPreceptor();
        $id_preceptor = $this->personaNota->getIdPreceptor();
        $detalle = $this->personaNota->getDetalle();
        $epoca = $this->personaNota->getEpoca();
        $id_activ = $this->personaNota->getIdActiv();
        $nota_num = $this->personaNota->getNotaNum();
        $nota_max = $this->personaNota->getNotaMax();

        $oPersonaNotaDB = $a_ObjetosPersonaNota['nota'];
        $oPersonaNotaDB->setId_nom($id_nom);
        $oPersonaNotaDB->setId_nivel($id_nivel);
        if (!empty($id_asignatura_real)) {
            $oPersonaNotaDB->setId_asignatura($id_asignatura_real);
            $oPersonaNotaDB->DBCarregar(); // Para que cargue los valores que ya tiene.
        }

        $oPersonaNotaDB->setId_asignatura($id_asignatura);
        $oPersonaNotaDB->setId_situacion($id_situacion);
        $oPersonaNotaDB->setF_acta($f_acta);
        $oPersonaNotaDB->setTipo_acta($tipo_acta);
        // comprobar valor del acta
        if (!empty($acta)) {
            if ($tipo_acta === PersonaNotaDB::FORMATO_CERTIFICADO) {
                $oPersonaNotaDB->setActa($acta);
            }
            if ($tipo_acta === PersonaNotaDB::FORMATO_ACTA) {
                $valor = Acta::inventarActa($acta, $f_acta);
                $oPersonaNotaDB->setActa($valor);
            }
        }
        if (empty($preceptor)) {
            $oPersonaNotaDB->setPreceptor('');
            $oPersonaNotaDB->setId_preceptor('');
        } else {
            $oPersonaNotaDB->setPreceptor($preceptor);
            $oPersonaNotaDB->setId_preceptor($id_preceptor);
        }
        $oPersonaNotaDB->setDetalle($detalle);
        $oPersonaNotaDB->setEpoca($epoca);
        $oPersonaNotaDB->setId_activ($id_activ);
        $oPersonaNotaDB->setNota_num($nota_num);
        $oPersonaNotaDB->setNota_max($nota_max);

        if ($oPersonaNotaDB->DBGuardar() === false) {
            $err = end($_SESSION['errores']);
            throw new \RuntimeException(sprintf(_("No se ha modificado la Nota: %s"), $err));
        }
        $rta['nota'] = $oPersonaNotaDB;

        // Pongo las notas en la dl de la persona, esperando al certificado
        if (array_key_exists('certificado', $a_ObjetosPersonaNota)) {
            $new_detalle = empty($detalle) ? "$acta" : "$acta ($detalle)";
            $oPersonaNotaCertificadoDB = $a_ObjetosPersonaNota['certificado'];

            // comprobar que no existe con una situación distinta a la 'falta certificado
            if ($oPersonaNotaCertificadoDB instanceof PersonaNotaCertificadoDB) {
                $oDbl = $oPersonaNotaCertificadoDB->getoDbl(); // asegurarme que estoy consultando en el mismo esquema
                $gesPersonaNota = new GestorPersonaNotaDlDB();
                $gesPersonaNota->setoDbl($oDbl);
                $aWhere = ['id_nom' => $id_nom,
                    'id_nivel' => $id_nivel,
                    'id_situacion' => Nota::FALTA_CERTIFICADO,
                ];
                $aOperador = ['id_situacion' => '!='];
                $cPersonaNota = $gesPersonaNota->getPersonaNotas($aWhere, $aOperador);
                if (!empty($cPersonaNota)) {
                    $oPersonaNota2 = $cPersonaNota[0];
                    if (!is_null($oPersonaNota2)) {
                        $oPersona = Persona::NewPersona($id_nom);
                        $nom = $oPersona->getPrefApellidosNombre();

                        $err = sprintf(_("Ya existe una nota para %s en su dl"), $nom);
                        $err .= "\n" . _("debería quitarlo del acta");
                        throw new \RuntimeException($err);
                    }
                }
            }

            $oPersonaNotaCertificadoDB->setId_nom($id_nom);
            $oPersonaNotaCertificadoDB->setId_nivel($id_nivel);
            if (!empty($id_asignatura_real)) {
                $oPersonaNotaDB->setId_asignatura($id_asignatura_real);
                $oPersonaNotaDB->DBCarregar(); // Para que cargue los valores que ya tiene.
            }
            $oPersonaNotaCertificadoDB->setId_asignatura($id_asignatura);
            $oPersonaNotaCertificadoDB->setId_situacion(Nota::FALTA_CERTIFICADO);
            $oPersonaNotaCertificadoDB->setActa(_("falta certificado"));
            $oPersonaNotaCertificadoDB->setDetalle($new_detalle);
            $oPersonaNotaCertificadoDB->setTipo_acta(PersonaNotaDB::FORMATO_CERTIFICADO);

            $oPersonaNotaCertificadoDB->setF_acta($f_acta);
            $oPersonaNotaCertificadoDB->setPreceptor($preceptor);
            $oPersonaNotaCertificadoDB->setId_preceptor($id_preceptor);
            $oPersonaNotaCertificadoDB->setEpoca($epoca);
            $oPersonaNotaCertificadoDB->setId_activ($id_activ);
            $oPersonaNotaCertificadoDB->setNota_num($nota_num);
            $oPersonaNotaCertificadoDB->setNota_max($nota_max);
            if ($oPersonaNotaCertificadoDB->DBGuardar() === false) {
                throw new \RuntimeException(_("hay un error, no se ha guardado. Nota Certificado"));
            }
            $rta['certificado'] = $oPersonaNotaCertificadoDB;
        }

        return $rta;
    }

    public
    function getDatosRegionStgr($dele = '')
    {

        $gesDelegacion = new GestorDelegacion();
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
     * @return PersonaNotaDB[]
     */
    public
    function getObjetosPersonaNota(array $a_mi_region_stgr, int $id_schema_persona): array
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

        /* region que está introduciendo la nota:
         *    a) la que organiza los ca
         *    b) la propia del alumno mediante dossiers.
         */

        $mi_region_stgr = $a_mi_region_stgr['region_stgr'];
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];
        $id_esquema_region_stgr = $a_mi_region_stgr['id_esquema_region_stgr'];
        $mi_id_schema = $a_mi_region_stgr['mi_id_schema'];

        $gesSchemas = new GestorDbSchema();
        $cSchemas = $gesSchemas->getDbSchemas(['id' => $id_schema_persona]);
        $nombre_schema_persona = $cSchemas[0]->getSchema();
        if (empty($nombre_schema_persona)) {
            $msg = sprintf(_("No se encuentra el nombre del esquema de la persona con id_nom: %s"), $id_schema_persona);
            throw new \RuntimeException($msg);
        }

        if ($nombre_schema_persona === 'restov' || $nombre_schema_persona === 'restof') {
            // guardar en e_notas_otra_region_stgr
            $rta['nota'] = new PersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        } else {
            $a_reg = explode('-', $nombre_schema_persona);
            $new_dele = substr($a_reg[1], 0, -1); // quito la v o la f.
            $datos_reg_destino = $this->getDatosRegionStgr($new_dele);

            // para los traslados incluir el caso de que las dos tengan el mismo esquema_region_stgr
            if ($id_schema_persona === $mi_id_schema || $esquema_region_stgr === $datos_reg_destino['esquema_region_stgr']) {
                // normal
                $oPersonasNotaDB = new PersonaNotaDlDB();
                if ($esquema_region_stgr === $datos_reg_destino['esquema_region_stgr']) {
                    // Conectar con la tabla de la dl
                    $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
                    // se debe conectar con la region del stgr padre
                    $oConfigDB = new ConfigDB($db); //de la database sv/sf
                    $config = $oConfigDB->getEsquema($nombre_schema_persona);
                    $oConexion = new DBConnection($config);
                    $oDbl = $oConexion->getPDO();

                    $oPersonasNotaDB->setoDbl($oDbl);
                }
                $rta['nota'] = $oPersonasNotaDB;
            } else {
                // guardar en e_notas_otra_region_stgr
                $rta['nota'] = new PersonaNotaOtraRegionStgrDB($esquema_region_stgr);
                $rta['certificado'] = new PersonaNotaCertificadoDB($nombre_schema_persona);
            }
        }

        return $rta;
    }

    private
    function getId_schema_persona(): int
    {
        // para saber a que schema pertenece la persona
        $oPersona = Persona::NewPersona($this->id_nom);
        if (!is_object($oPersona)) {
            $msg_err = "$oPersona con id_nom: $this->id_nom en  " . __FILE__ . ": line " . __LINE__;
            // exit($msg_err);
            throw new \RuntimeException($msg_err);
        }
        return $oPersona->getId_schema();
    }

}