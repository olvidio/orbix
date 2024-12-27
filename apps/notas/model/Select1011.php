<?php

namespace notas\model;

use actividades\model\entity as actividades;
use actividadestudios\model\entity\GestorMatricula as actividadestudios;
use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use core\View;
use notas\model\entity as notas;
use notas\model\entity\Nota;
use personas\model\entity as personas;
use web\Hash;
use web\Lista;
use actividadestudios\model\entity\GestorMatricula;
use function core\is_true;

class Select1011
{

    /* @var $mwg_err string */
    private $msg_err;
    /* @var $a_valores array */
    private $a_valores;
    /**
     * Para pasar a la vista, aparece como alerta antes de ejecutarse
     * @var string $txt_eliminar
     */
    private $txt_eliminar;
    /* @var $bloque string  necesario para el script */
    private $bloque;

    // ---------- Variables requeridas
    /* @var $queSel integer */
    private $queSel;
    /* @var $id_dossier integer */
    private $id_dossier;
    /* @var $pau string */
    private $pau;
    /* @var $obj_pau string */
    private $obj_pau;
    /* @var $id_pau integer */
    private $id_pau;
    /**
     * 3: para todo, 2, 1:solo lectura
     * @var integer $permiso
     */
    private $permiso;

    // ------ Variables para mantener la selección de la grid al volver atras
    private $Qid_sel;
    private $Qscroll_id;
    private string $LinkInsert;

    private function getBotones()
    {
        $a_botones = array(
            array('txt' => _("modificar nota"), 'click' => "fnjs_modificar(this.form)"),
            array('txt' => _("borrar asignatura"), 'click' => "fnjs_borrar(this.form)")
        );
        return $a_botones;
    }


    private function getCabeceras()
    {
        $a_cabeceras = array(
            _("asignatura"),
            _("nota"),
            _("acta"),
            array('name' => ucfirst(_("fecha acta")), 'class' => 'fecha'),
            _("preceptor"),
            _("época"),
            _("detalle"),
            _("cursada en")
        );
        return $a_cabeceras;
    }

    private function getValores()
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla()
    {

        // Aviso si le faltan notas por poner
        $gesMatriculas = new GestorMatricula();
        $cMatriculasPendientes = $gesMatriculas->getMatriculasPendientes($this->id_pau);
        if (count($cMatriculasPendientes) > 0) {
            $msg = '';
            foreach ($cMatriculasPendientes as $oMatricula) {
                $id_activ = $oMatricula->getId_activ();
                $id_asignatura = $oMatricula->getId_asignatura();
                $oActividad = new actividades\ActividadAll($id_activ);
                $nom_activ = $oActividad->getNom_activ();
                $oAsignatura = new asignaturas\Asignatura($id_asignatura);
                $nombre_corto = $oAsignatura->getNombre_corto();
                $msg .= empty($msg) ? '' : '<br>';
                $msg .= sprintf(_("ca: %s, asignatura: %s"), $nom_activ, $nombre_corto);
            }
            if (!empty($msg)) {
                $msg = _("tiene pendiente de poner las notas de:") . '<br>' . $msg;
            }
        }


        $gesPersonaNotas = new notas\GestorPersonaNotaDlDB();
        // Que si muestre el "fin bienio, fin cuadrienio".
        //$cPersonaNotas = $gesPersonaNotas->getPersonaNotas(array('id_nom'=>  $this->id_pau,'id_asignatura'=>9000,'_ordre'=>'id_nivel'),array('id_asignatura'=>'<'));
        $cPersonaNotas = $gesPersonaNotas->getPersonaNotas(array('id_nom' => $this->id_pau, '_ordre' => 'id_nivel'), array('id_asignatura' => '<'));

        $gesNotas = new notas\GestorNota();
        $cNotas = $gesNotas->getNotas();
        $a_notas = array();
        foreach ($cNotas as $oNota) {
            $id_situacion = $oNota->getId_situacion();
            $breve = $oNota->getBreve();
            $a_notas[$id_situacion] = $breve;
        }


        //Según el tipo de persona: n, agd, s
        $oPersona = personas\Persona::NewPersona($this->id_pau);
        if (!is_object($oPersona)) {
            $msg_err = "<br>$oPersona con id_nom: $this->id_pau en  " . __FILE__ . ": line " . __LINE__;
            exit($msg_err);
        }
        $id_tabla = $oPersona->getId_Tabla();

        $i = 0;
        $a_valores = array();
        foreach ($cPersonaNotas as $oPersonaNota) {
            $i++;
            $id_nivel = $oPersonaNota->getId_nivel();
            $id_asignatura = $oPersonaNota->getId_asignatura();

            $id_situacion = $oPersonaNota->getId_situacion();
            $f_acta = $oPersonaNota->getF_acta()->getFromLocal();
            $acta = $oPersonaNota->getActa();
            $preceptor = $oPersonaNota->getPreceptor();
            $id_preceptor = $oPersonaNota->getId_preceptor();
            $epoca = $oPersonaNota->getEpoca();
            $detalle = $oPersonaNota->getDetalle();
            $id_activ = $oPersonaNota->getId_activ();

            $nom_activ = '';
            if (!empty($id_activ)) {
                $oActividad = new actividades\ActividadAll($id_activ);
                $nom_activ = $oActividad->getNom_activ();
            }
            //$nota = $a_notas[$id_situacion];
            $nota = $oPersonaNota->getNota_txt();
            if ($acta == Nota::CURSADA) {
                $acta = '';
            }

            $oAsignatura = new asignaturas\Asignatura($id_asignatura);
            $nombre_corto = $oAsignatura->getNombre_corto();
            $id_sector = $oAsignatura->getId_sector();

            // opcionales
            if ($id_asignatura > 3000) {
                $gesOpcionales = new asignaturas\GestorAsignatura();
                $cOpcionales = $gesOpcionales->getAsignaturas(array('id_nivel' => $id_nivel));
                if (empty($cOpcionales)) {
                    $nombre_corto = _("opcional de sobra");
                } else {
                    $nom_op = $cOpcionales[0]->getNombre_corto();
                    $nombre_corto = $nom_op . " (" . $nombre_corto . ")";
                }
            }


            $preceptor = is_true($preceptor)? _("sí") : _("no");
            // preceptor
            if ($id_preceptor && is_true($preceptor)) {
                $oPersonaDl = new personas\PersonaDl($id_preceptor);
                $nom_precptor = $oPersonaDl->getPrefApellidosNombre();
                if (empty($nom_precptor)) {
                    $nom_precptor = _("no lo encuentro");
                }
                $preceptor .= " (" . $nom_precptor . ")";
            }

            if ($this->permiso == 3) {
                $a_valores[$i]['sel'] = "$id_nivel#$id_asignatura";
            } else {
                $a_valores[$i]['sel'] = "";
            }
            $a_valores[$i][1] = "$nombre_corto";
            $a_valores[$i][2] = $nota;
            $a_valores[$i][3] = $acta;
            $a_valores[$i][4] = $f_acta;
            $a_valores[$i][5] = $preceptor;
            $a_valores[$i][6] = $epoca;
            $a_valores[$i][7] = $detalle;
            $a_valores[$i][8] = $nom_activ;
        }
        if (!empty($a_valores)) {
            // Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
            // las pongo al final, porque al contar los valores del array se despista.
            if (isset($this->Qid_sel) && !empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (isset($this->Qscroll_id) && !empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }
        $this->a_valores = $a_valores;
    }

    public function getHtml()
    {
        $this->txt_eliminar = _("¿Está seguro que desea borrar la nota de esta asignatura?");

        $oHashSelect = new Hash();
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $a_camposHidden = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => '1011',
            'permiso' => $this->permiso,
        );
        $oHashSelect->setArraycamposHidden($a_camposHidden);

        $oTabla = new Lista();
        $oTabla->setId_tabla('select1011');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        // para que genere las variables $aLink
        $this->setLinksInsert();

        $a_campos = ['oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'link_insert' => $this->LinkInsert,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
        ];

        $oView = new View(__NAMESPACE__);
        $oView->renderizar('select1011.phtml', $a_campos);
    }

    public function setLinksInsert()
    {
        $this->LinkInsert = '';
        if ($this->permiso == 3) {
            $aQuery = array('mod' => 'nuevo',
                'pau' => $this->pau,
                'id_pau' => $this->id_pau,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier, //Para que al volver a la pagina 'dossiers_ver' sepa cual mostrar.
            );
            // el hppt_build_query no pasa los valores null
            if (is_array($aQuery)) {
                array_walk($aQuery, 'core\poner_empty_on_null');
            }
            $this->LinkInsert = Hash::link(ConfigGlobal::getWeb() . "/apps/notas/controller/form_1011.php?" . http_build_query($aQuery));
        }

    }

    public function setId_dossier($id_dossier)
    {
        $this->id_dossier = $id_dossier;
    }

    public function setPau($pau)
    {
        $this->pau = $pau;
    }

    public function setObj_pau($obj_pau)
    {
        $this->obj_pau = $obj_pau;
    }

    public function setId_pau($id_pau)
    {
        $this->id_pau = $id_pau;
    }

    public function setPermiso($permiso)
    {
        $this->permiso = $permiso;
    }

    public function setQid_sel($Qid_sel)
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id($Qscroll_id)
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque($bloque)
    {
        $this->bloque = $bloque;
    }

    public function setQueSel($queSel)
    {
        $this->queSel = $queSel;
    }


}
