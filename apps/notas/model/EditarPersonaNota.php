<?php

namespace notas\model;

use asignaturas\model\entity\GestorAsignatura;
use core\ConfigGlobal;
use devel\model\entity\GestorDbSchema;
use dossiers\model\entity\Dossier;
use notas\model\entity\Acta;
use notas\model\entity\Nota;
use notas\model\entity\PersonaNotaDB;
use notas\model\entity\PersonaNotaCertificadoDB;
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

    public function eliminar(): string
    {
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él
        if (!empty($this->id_nom) && !empty($this->id_asignatura) && !empty($this->id_nivel)) {
            $oPersonaNota = new PersonaNotaDB();
            $oPersonaNota->setId_nom($this->id_nom);
            $oPersonaNota->setId_asignatura($this->id_asignatura);
            $oPersonaNota->setId_nivel($this->id_nivel);
            if ($oPersonaNota->DBEliminar() === false) {
                $this->msg_err .= _("hay un error, no se ha borrado");
            }
        }
        return $this->msg_err;
    }

    public function nuevo(): array
    {

        $a_ObjetosPersonaNota = $this->getObjetosPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->nuevo2($a_ObjetosPersonaNota);
    }

    public function nuevo2(array $a_ObjetosPersonaNota): array
    {
        $rta = [];
        $oPersonaNotaDB = $a_ObjetosPersonaNota['nota'];

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

        //No es una opcional TODO: en otro sitio
        /*
        if ($this->personaNota->getIdAsignatura() === 1) {
            $oGesAsignaturas = new GestorAsignatura();
            $cAsignaturas = $oGesAsignaturas->getAsignaturas(array('id_nivel' => $this->personaNota->getIdNivel()));
            if (!is_array($cAsignaturas) || count($cAsignaturas) === 0) {
                $msg_err = sprintf(_("No se encuentra una asignatura para le nivel: %s"),$this->personaNota->getIdNivel());
                exit ($msg_err);
            }
            $oAsignatura = $cAsignaturas[0]; // sólo debería haber una
            $id_asignatura = $oAsignatura->getId_asignatura();
        } else {//es una opcional
            $id_asignatura = $this->personaNota->getIdAsignatura();
        }
        */
        $id_asignatura = $this->personaNota->getIdAsignatura();

        $oPersonaNotaDB->setId_nivel($this->personaNota->getIdNivel());
        $oPersonaNotaDB->setId_asignatura($id_asignatura);
        $oPersonaNotaDB->setId_nom($this->personaNota->getIdNom());
        //$oPersonaNotaDB->setId_schema($id_schema);

        $oPersonaNotaDB->setId_situacion($id_situacion);
        $oPersonaNotaDB->setF_acta($f_acta);
        $oPersonaNotaDB->setTipo_acta($tipo_acta);
        // comprobar valor del acta
        if (!empty($acta)) {
            if ($tipo_acta === PersonaNotaDB::FORMATO_CERTIFICADO) {
                $oPersonaNotaDB->setActa($acta);
            }
            if ($tipo_acta === PersonaNotaDB::FORMATO_ACTA) {
                $oActa = new Acta();
                $valor = trim($acta);
                $reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
                if (!preg_match($reg_exp, $valor)) {
                    // inventar acta.
                    $valor = $oActa->inventarActa($valor, $f_acta);
                }
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
        if ($oPersonaNotaDB->DBGuardar() === false) {
            $err = end($_SESSION['errores']);
            throw new \RuntimeException(sprintf(_("No se ha guardado la Nota: %s"), $err));
        }
        $rta['nota'] =$oPersonaNotaDB;
        // si no está abierto, hay que abrir el dossier para esta persona
        //No hace falta si es una persona de paso
        if ($this->personaNota->getIdNom() > 0) {
            $oDossier = new Dossier(array('tabla' => 'p', 'id_pau' => $this->personaNota->getIdNom(), 'id_tipo_dossier' => 1303));
            $oDossier->abrir();
            if ($oDossier->DBGuardar() === false) {
                $err = end($_SESSION['errores']);
                throw new \RuntimeException(sprintf(_("No al guardar el dossier: %s"), $err));
            }
        }

        // Pongo las notas en la dl de la persona, esperando al certificado
        if (array_key_exists('certificado', $a_ObjetosPersonaNota)) {
            $oPersonaNotaCertificadoDB = $a_ObjetosPersonaNota['certificado'];

            $oPersonaNotaCertificadoDB->setId_nivel($this->personaNota->getIdNivel());
            $oPersonaNotaCertificadoDB->setId_asignatura($id_asignatura);
            $oPersonaNotaCertificadoDB->setId_nom($this->personaNota->getIdNom());

            $oPersonaNotaCertificadoDB->setId_situacion(Nota::FALTA_CERTIFICADO);
            $oPersonaNotaCertificadoDB->setActa(_("falta certificado"));
            $oPersonaNotaCertificadoDB->setDetalle($acta);
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
            $rta['certificado'] =$oPersonaNotaCertificadoDB;
        }

        return $rta;
    }

    public function editar(int $id_asignatura_real): string
    {
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él

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


        $a_ObjetosPersonaNota = $this->getObjetosPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());
        $personaNotaDB = $a_ObjetosPersonaNota['nota'];

        // puede devolver mensaje de error
        if (!empty($this->msg_err)) {
            return $this->msg_err;
        }

        if (!empty($this->id_nom) && !empty($id_asignatura_real)) {
            $personaNotaDB->setId_nom($this->personaNota->getIdNom());
            $personaNotaDB->setId_nivel($this->personaNota->getIdNivel());
            $personaNotaDB->setId_asignatura($id_asignatura_real);
            $personaNotaDB->DBCarregar(); // Para que cargue los valores que ya tiene.
        }

        $personaNotaDB->setId_situacion($id_situacion);
        $personaNotaDB->setF_acta($f_acta);
        $personaNotaDB->setTipo_acta($tipo_acta);
        // comprobar valor del acta
        if (!empty($acta)) {
            if ($tipo_acta === PersonaNotaDB::FORMATO_CERTIFICADO) {
                $personaNotaDB->setActa($acta);
            }
            if ($tipo_acta === PersonaNotaDB::FORMATO_ACTA) {
                $oActa = new Acta();
                $valor = trim($acta);
                $reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
                if (!preg_match($reg_exp, $valor)) {
                    // inventar acta.
                    $valor = $oActa->inventarActa($valor, $f_acta);
                }
                $personaNotaDB->setActa($valor);
            }
        }
        if (empty($preceptor)) {
            $personaNotaDB->setPreceptor('');
            $personaNotaDB->setId_preceptor('');
        } else {
            $personaNotaDB->setPreceptor($preceptor);
            $personaNotaDB->setId_preceptor($id_preceptor);
        }
        $personaNotaDB->setDetalle($detalle);
        $personaNotaDB->setEpoca($epoca);
        $personaNotaDB->setId_activ($id_activ);
        $personaNotaDB->setNota_num($nota_num);
        $personaNotaDB->setNota_max($nota_max);

        if ($personaNotaDB->DBGuardar() === false) {
            $this->msg_err .= _("hay un error, no se ha guardado");
        }
        return $this->msg_err;
    }

    public function getDatosRegionStgr() {

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $a_mi_region_stgr['mi_id_schema'] = ConfigGlobal::mi_id_schema();

        return $a_mi_region_stgr;
    }

    /**
     * Se lo paso por constructor para poder hacer test con otra información
     * @return PersonaNotaDB[]
     */
    public function getObjetosPersonaNota(array $a_mi_region_stgr, int $id_schema_persona): array
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

        if ($nombre_schema_persona === 'restov' || $nombre_schema_persona === 'restof') {
            // guardar en e_notas_otra_region_stgr
            $rta['nota'] = new PersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        } else {
            if ($id_schema_persona === $mi_id_schema) {
                // normal
                $rta['nota'] = new PersonaNotaDlDB();
            } else {
                // guardar en e_notas_otra_region_stgr
                $rta['nota'] = new PersonaNotaOtraRegionStgrDB($esquema_region_stgr);
                $rta['certificado'] = new PersonaNotaCertificadoDB($nombre_schema_persona);
            }
        }

        return $rta;
    }

    private function getId_schema_persona(): int
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