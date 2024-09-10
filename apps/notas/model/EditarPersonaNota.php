<?php

namespace notas\model;

use asignaturas\model\entity\GestorAsignatura;
use core\ConfigGlobal;
use devel\model\entity\GestorDbSchema;
use dossiers\model\entity\Dossier;
use notas\model\entity\Acta;
use notas\model\entity\Nota;
use notas\model\entity\PersonaNota;
use notas\model\entity\PersonaNotaCertificado;
use notas\model\entity\PersonaNotaDl;
use notas\model\entity\PersonaNotaOtraRegionStgr;
use personas\model\entity\Persona;
use ubis\model\entity\GestorDelegacion;

class EditarPersonaNota
{
    private bool $mock = FALSE;

    private string $msg_err = '';
    private int $id_nom;
    private int $id_asignatura;
    private int $id_nivel;

    public function __construct($id_nom, $id_asignatura, $id_nivel)
    {

        $this->id_nom = $id_nom;
        $this->id_asignatura = $id_asignatura;
        $this->id_nivel = $id_nivel;
    }

    public function eliminar(): string
    {
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él
        if (!empty($this->id_nom) && !empty($this->id_asignatura) && !empty($this->id_nivel)) {
            $oPersonaNota = new PersonaNota();
            $oPersonaNota->setId_nom($this->id_nom);
            $oPersonaNota->setId_asignatura($this->id_asignatura);
            $oPersonaNota->setId_nivel($this->id_nivel);
            if ($oPersonaNota->DBEliminar() === false) {
                $this->msg_err .= _("hay un error, no se ha borrado");
            }
        }
        return $this->msg_err;
    }

    public function nuevo(array $camposExtra): array
    {

        $a_ObjetosPersonaNota = $this->getObjetosPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());

        return $this->nuevo2($a_ObjetosPersonaNota, $camposExtra);
    }

    public function nuevo2(array $a_ObjetosPersonaNota, array $camposExtra): array
    {
        $rta = [];
        $oPersonaNota = $a_ObjetosPersonaNota['nota'];

        $id_situacion = $camposExtra['id_situacion'];
        $acta = $camposExtra['acta'];
        $f_acta = $camposExtra['f_acta'];
        $tipo_acta = $camposExtra['tipo_acta'];
        $preceptor = $camposExtra['preceptor'];
        $id_preceptor = $camposExtra['id_preceptor'];
        $detalle = $camposExtra['detalle'];
        $epoca = $camposExtra['epoca'];
        $id_activ = $camposExtra['id_activ'];
        $nota_num = $camposExtra['nota_num'];
        $nota_max = $camposExtra['nota_max'];

        //No es una opcional
        if ($this->id_asignatura === 1) {
            $oGesAsignaturas = new GestorAsignatura();
            $cAsignaturas = $oGesAsignaturas->getAsignaturas(array('id_nivel' => $this->id_nivel));
            if (!is_array($cAsignaturas) || count($cAsignaturas) === 0) {
                $msg_err = sprintf(_("No se encuentra una asignatura para le nivel: %s"),$this->id_nivel);
                exit ($msg_err);
            }
            $oAsignatura = $cAsignaturas[0]; // sólo debería haber una
            $id_asignatura = $oAsignatura->getId_asignatura();
        } else {//es una opcional
            $id_asignatura = $this->id_asignatura;
        }
        $oPersonaNota->setId_nivel($this->id_nivel);
        $oPersonaNota->setId_asignatura($id_asignatura);
        $oPersonaNota->setId_nom($this->id_nom);
        //$oPersonaNota->setId_schema($id_schema);

        $oPersonaNota->setId_situacion($id_situacion);
        $oPersonaNota->setF_acta($f_acta);
        $oPersonaNota->setTipo_acta($tipo_acta);
        // comprobar valor del acta
        if (!empty($acta)) {
            if ($tipo_acta === PersonaNota::FORMATO_CERTIFICADO) {
                $oPersonaNota->setActa($acta);
            }
            if ($tipo_acta === PersonaNota::FORMATO_ACTA) {
                $oActa = new Acta();
                $valor = trim($acta);
                $reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
                if (!preg_match($reg_exp, $valor)) {
                    // inventar acta.
                    $valor = $oActa->inventarActa($valor, $f_acta);
                }
                $oPersonaNota->setActa($valor);
            }
        }
        $oPersonaNota->setPreceptor($preceptor);
        $oPersonaNota->setId_preceptor($id_preceptor);
        $oPersonaNota->setDetalle($detalle);
        $oPersonaNota->setEpoca($epoca);
        $oPersonaNota->setId_activ($id_activ);
        $oPersonaNota->setNota_num($nota_num);
        $oPersonaNota->setNota_max($nota_max);
        if ($oPersonaNota->DBGuardar() === false) {
            throw new \RuntimeException(_("hay un error, no se ha guardado. Nota"));
        }
        $rta['nota'] =$oPersonaNota;
        // si no está abierto, hay que abrir el dossier para esta persona
        //abrir_dossier('p',$_POST['id_pau'],'1303',$oDB);
        $oDossier = new Dossier(array('tabla' => 'p', 'id_pau' => $this->id_nom, 'id_tipo_dossier' => 1303));
        $oDossier->abrir();
        $oDossier->DBGuardar();

        // Pongo las notas en la dl de la persona, esperando al certificado
        if (array_key_exists('certificado', $a_ObjetosPersonaNota)) {
            $oPersonaNotaCertificado = $a_ObjetosPersonaNota['certificado'];

            $oPersonaNotaCertificado->setId_nivel($this->id_nivel);
            $oPersonaNotaCertificado->setId_asignatura($id_asignatura);
            $oPersonaNotaCertificado->setId_nom($this->id_nom);

            $oPersonaNotaCertificado->setId_situacion(Nota::FALTA_CERTIFICADO);
            $oPersonaNotaCertificado->setActa(_("falta certificado"));
            $oPersonaNotaCertificado->setDetalle($acta);
            $oPersonaNotaCertificado->setTipo_acta(PersonaNota::FORMATO_CERTIFICADO);

            $oPersonaNotaCertificado->setF_acta($f_acta);
            $oPersonaNotaCertificado->setPreceptor($preceptor);
            $oPersonaNotaCertificado->setId_preceptor($id_preceptor);
            $oPersonaNotaCertificado->setEpoca($epoca);
            $oPersonaNotaCertificado->setId_activ($id_activ);
            $oPersonaNotaCertificado->setNota_num($nota_num);
            $oPersonaNotaCertificado->setNota_max($nota_max);
            if ($oPersonaNotaCertificado->DBGuardar() === false) {
                throw new \RuntimeException(_("hay un error, no se ha guardado. Nota Certificado"));
            }
            $rta['certificado'] =$oPersonaNotaCertificado;
        }

        return $rta;
    }

    public function editar($camposExtra): string
    {
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él

        $id_situacion = $camposExtra['id_situacion'];
        $acta = $camposExtra['acta'];
        $f_acta = $camposExtra['f_acta'];
        $tipo_acta = $camposExtra['tipo_acta'];
        $preceptor = $camposExtra['preceptor'];
        $id_preceptor = $camposExtra['id_preceptor'];
        $detalle = $camposExtra['detalle'];
        $epoca = $camposExtra['epoca'];
        $id_activ = $camposExtra['id_activ'];
        $nota_num = $camposExtra['nota_num'];
        $nota_max = $camposExtra['nota_max'];
        $id_asignatura_real = $camposExtra['id_asignatura_real'];


        $a_ObjetosPersonaNota = $this->getObjetosPersonaNota($this->getDatosRegionStgr(), $this->getId_schema_persona());
        $oPersonaNota = $a_ObjetosPersonaNota['nota'];

        // puede devolver mensaje de error
        if (!empty($this->msg_err)) {
            return $this->msg_err;
        }

        if (!empty($this->id_nom) && !empty($id_asignatura_real)) {
            $oPersonaNota->setId_nom($this->id_nom);
            $oPersonaNota->setId_nivel($this->id_nivel);
            $oPersonaNota->DBCarregar(); // Para que cargue los valores que ya tiene.
        }

        $oPersonaNota->setId_situacion($id_situacion);
        $oPersonaNota->setF_acta($f_acta);
        $oPersonaNota->setTipo_acta($tipo_acta);
        // comprobar valor del acta
        if (!empty($acta)) {
            if ($tipo_acta === PersonaNota::FORMATO_CERTIFICADO) {
                $oPersonaNota->setActa($acta);
            }
            if ($tipo_acta === PersonaNota::FORMATO_ACTA) {
                $oActa = new Acta();
                $valor = trim($acta);
                $reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
                if (!preg_match($reg_exp, $valor)) {
                    // inventar acta.
                    $valor = $oActa->inventarActa($valor, $f_acta);
                }
                $oPersonaNota->setActa($valor);
            }
        }
        if (empty($preceptor)) {
            $oPersonaNota->setPreceptor('');
            $oPersonaNota->setId_preceptor('');
        } else {
            $oPersonaNota->setPreceptor($preceptor);
            $oPersonaNota->setId_preceptor($id_preceptor);
        }
        $oPersonaNota->setDetalle($detalle);
        $oPersonaNota->setEpoca($epoca);
        $oPersonaNota->setId_activ($id_activ);
        $oPersonaNota->setNota_num($nota_num);
        $oPersonaNota->setNota_max($nota_max);

        if ($oPersonaNota->DBGuardar() === false) {
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
     * @return PersonaNota[]
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
            $rta['nota'] = new PersonaNotaOtraRegionStgr($esquema_region_stgr, $this->mock);
        } else {
            if ($id_schema_persona === $mi_id_schema) {
                // normal
                $rta['nota'] = new PersonaNotaDl();
            } else {
                // guardar en e_notas_otra_region_stgr
                $rta['nota'] = new PersonaNotaOtraRegionStgr($esquema_region_stgr, $this->mock);
                $rta['certificado'] = new PersonaNotaCertificado($nombre_schema_persona, $this->mock);
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

    public function setMock(bool $mock): void
    {
        $this->mock = $mock;
    }

}