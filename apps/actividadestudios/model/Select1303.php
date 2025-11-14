<?php

namespace actividadestudios\model;

use actividades\model\entity\ActividadAll;
use asignaturas\model\entity\Asignatura;
use asistentes\model\entity\GestorAsistente;
use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\Persona;
use personas\model\entity\PersonaDl;
use src\asignaturas\application\repositories\AsignaturaRepository;
use web\Hash;
use web\Lista;
use function core\curso_est;
use function core\is_true;

/**
 * Gestiona el dossier 1303: Asignaturas que cursa una persona (matrículas)
 * por actividad.
 *
 *
 * @package    orbix
 * @subpackage    actividadestudios
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select1303
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

    private $todos;
    private $Qid_activ; // ¿? ya tengo una actividad concreta (vengo del dossier de esa actividad).

    private $cAsistencias;

    private $status;
    private string $aviso;
    private mixed $id_activ;
    private string $link_add;

    public function getBotones($ca_num = 1)
    {
        $a_botones = [];
        if ($this->permiso == 3) {
            $a_botones = array(
                array('txt' => _("modificar"), 'click' => "fnjs_modificar(this.form,$ca_num)"),
                array('txt' => _("borrar matrícula"), 'click' => "fnjs_borrar(this.form,$ca_num)")
            );
        }
        return $a_botones;
    }

    public function getCabeceras()
    {
        $a_cabeceras = array(_("preceptor"),
            _("asignatura")
        );
        return $a_cabeceras;
    }


    public function getHtmlCa($oAsistente, $ca_num = 1)
    {
        $this->id_activ = $oAsistente->getId_activ();
        $propio = $oAsistente->getPropio();
        if (!is_true($propio)) {
            echo _("no está como propio, no debería tener plan de estudios");
        }

        $est_ok = $oAsistente->getEst_ok();
        $observ_est = $oAsistente->getObserv_est();
        $oActividad = new ActividadAll(array('id_activ' => $this->id_activ));
        $nom_activ = $oActividad->getNom_activ();

        // el plan de estudios solo puede modificarlo la dl del alumno (a no ser que sea de paso)
        $oAlumno = Persona::NewPersona($this->id_pau);
        $dl_alumno = $oAlumno->getDl();
        $classname = str_replace("personas\\model\\entity\\", '', get_class($oAlumno));
        $this->permiso = 3;
        if ($classname === 'PersonaEx') {
            $this->permiso = 3;
        } elseif ($dl_alumno != ConfigGlobal::mi_delef()) {
            $this->permiso = 2;
        }


        $GesMatriculas = new entity\GestorMatricula();
        $cMatriculas = $GesMatriculas->getMatriculas(array('id_nom' => $this->id_pau, 'id_activ' => $this->id_activ, '_ordre' => 'id_nivel'));

        $form = "seleccionados" . $ca_num;
        if (is_true($est_ok)) {
            $chk_1 = "checked";
            $chk_2 = "";
        } else {
            $chk_1 = "";
            $chk_2 = "checked";
        }

        $i = 0;
        $a_valores = [];
        $msg_err = '';
        foreach ($cMatriculas as $oMatricula) {
            $i++;
            $id_asignatura = $oMatricula->getId_asignatura();
            $preceptor = $oMatricula->getPreceptor();
            $id_preceptor = $oMatricula->getId_preceptor();
            if (is_true($preceptor)) {
                if (!empty($id_preceptor)) {
                    $oPersona = Persona::NewPersona($id_preceptor);
                    if (!is_object($oPersona)) {
                        $msg_err .= "<br>$oPersona con id_nom: $id_preceptor (profesor) en  " . __FILE__ . ": line " . __LINE__;
                        $preceptor = 'x';
                    } else {
                        $preceptor = $oPersona->getPrefApellidosNombre();
                    }
                } else {
                    $preceptor = _("por determinar");
                }
            } else {
                $preceptor = "";
            }

            $oAsignatura = (new AsignaturaRepository())->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();

            $a_valores[$i]['sel'] = "$this->id_activ#$id_asignatura";
            $a_valores[$i][1] = $preceptor;
            $a_valores[$i][2] = $nombre_corto;
        }

        $oTabla = new Lista();
        $oTabla->setId_tabla('sql_1303' . $ca_num);
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones($ca_num));
        $oTabla->setDatos($a_valores);

        $oHashCa = new Hash();
        $oHashCa->setCamposForm('est_ok!observ_est');
        $oHashCa->setCamposNo('sel!mod!scroll_id!refresh');
        $a_camposHiddenCa = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'id_activ' => $this->id_activ,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => $this->permiso,
        );
        $oHashCa->setArraycamposHidden($a_camposHiddenCa);

        // para que genere las variables $aLink
        $this->setLinksInsert();

        $a_campos = ['oTabla' => $oTabla,
            'oHashCa' => $oHashCa,
            'nom_activ' => $nom_activ,
            'form' => $form,
            'ca_num' => $ca_num,
            'chk_1' => $chk_1,
            'chk_2' => $chk_2,
            'link_add' => $this->link_add,
            'bloque' => $this->bloque,
            'observ_est' => $observ_est,
            'permiso' => $this->permiso,
        ];


        if (!empty($msg_err)) {
            echo $msg_err;
        }
        $oView = new ViewPhtml(__NAMESPACE__);
        $oView->renderizar('selectUnCa.phtml', $a_campos);
    }

    public function setLinksInsert()
    {
        $a_dataUrl = array('mod' => 'nuevo',
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'id_activ' => $this->id_activ,
        );
        // el hppt_build_query no pasa los valores null
        if (is_array($a_dataUrl)) {
            array_walk($a_dataUrl, 'core\poner_empty_on_null');
        }
        $this->link_add = Hash::link(ConfigGlobal::getWeb() . '/apps/actividadestudios/controller/form_1303.php?' . http_build_query($a_dataUrl));
        // --------------  boton matricular automáticamente ----------------------
//		$this->link_matricular = Hash::link(ConfigGlobal::getWeb().'/apps/actividadestudios/controller/matricular.php?'.http_build_query($a_dataUrl));	
//		$this->link_matricular = "fnjs_matricular(this.form)";
    }


    public function getAsistencias()
    {
        /* Pongo en la variable $curso el periodo del curso */
        $mes = date('m');
        $fin_m = $_SESSION['oConfig']->getMesFinStgr();
        if ($mes > $fin_m) {
            $any = (int)date('Y') + 1;
        } else {
            $any = date('Y');
        }
        $inicurs_ca = curso_est("inicio", $any)->format('Y-m-d');
        $fincurs_ca = curso_est("fin", $any)->format('Y-m-d');

        $aviso = '';
        // Compruebo si está de repaso...
        $oPersona = new PersonaDl(array('id_nom' => $this->id_pau));
        $stgr = $oPersona->getStgr();
        if ($stgr === 'r') $aviso .= _("está de repaso") . "<br>";

        $aWhere = [];
        $aOperadores = [];
        $GesAsistentes = new GestorAsistente();
        if (!empty($this->Qid_activ)) {  // ¿? ya tengo una actividad concreta (vengo del dossier de esa actividad).
            $aWhere['id_activ'] = $this->Qid_activ;
            $aWhereNom = ['id_nom' => $this->id_pau, 'id_activ' => $this->Qid_activ];
            $aOperadorNom = [];
            $cAsistencias = $GesAsistentes->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhere, $aOperadores, true);
        } else {
            if (empty($this->todos)) {
                $aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $aOperadores['f_ini'] = 'BETWEEN';
            }
            // todas las actividades de estudios (no crt)
            $aWhere['id_tipo_activ'] = '^' . ConfigGlobal::mi_sfsv() . '(122)|(222)|(332)|(123)'; // el 123 correponde al semestre de invierno.
            $aOperadores['id_tipo_activ'] = '~';

            $aWhereNom = ['id_nom' => $this->id_pau, 'propio' => 't'];
            $aOperadorNom = [];
            $cAsistencias = $GesAsistentes->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhere, $aOperadores, true);
        }
        if (is_array($cAsistencias)) {
            $n = count($cAsistencias);
            if ($n == 0 && empty($this->todos)) {
                $oHashA = new Hash();
//				$oHashA->setCamposForm('sel');
                $oHashA->setcamposNo('scroll_id');
                $a_camposHiddenA = array(
                    'pau' => 'p',
                    'id_pau' => $this->id_pau,
                    'obj_pau' => $this->obj_pau,
                    'id_dossier' => 1303,
                    'permiso' => '3',
                    'que' => 'matriculas',
                    'todos' => 1,
                    'mod' => 'xx',
                );
                $oHashA->setArraycamposHidden($a_camposHiddenA);

                $aviso .= _(sprintf(_("No tiene asignado ningún ca como propio este curso: %s - %s."), $inicurs_ca, $fincurs_ca));
                $aviso .= "<form action='apps/dossiers/controller/dossiers_ver.php' method='post'>";
                $aviso .= $oHashA->getCamposHtml();
                $aviso .= "<input type=\"button\" onclick=\"fnjs_enviar_formulario(this.form,'#main')\" value=\"" . _("ver anteriores") . "\">";
                $aviso .= "</form>";
            }

            if ($n == 0 && !empty($this->todos)) {
                $aviso .= _("no tiene asignado ningún ca.");
            }
            if ($n > 1 && empty($this->todos)) {
                $nn = 0;
                $id_sem_inv = (int)ConfigGlobal::mi_sfsv() . '32500';
                foreach ($cAsistencias as $oAsistente) {
                    $id_activ = $oAsistente->getId_activ();
                    $oActividad = new ActividadAll($id_activ);
                    $id_tipo_activ = $oActividad->getId_tipo_activ();
                    if ($id_tipo_activ != $id_sem_inv) $nn++;
                }
                if ($nn > 1) {
                    $aviso .= _(sprintf(_("¡¡ojo!! tiene %s actividades de estudios asignadas como propias."), $n));
                }
            }
        }
        $this->cAsistencias = $cAsistencias;
        $this->aviso = $aviso;
        return $cAsistencias;
    }

    public function getHtml()
    {
        $txt_eliminar = _("¿Está seguro que desea quitar esta asignatura de esta actividad?");

        $this->getAsistencias();

        // Tengo el javascript. Sólo he de ponerlo una vez.
        $a_campos = ['aviso' => $this->aviso,
            'txt_eliminar' => $txt_eliminar,
            'bloque' => $this->bloque,
        ];

        $oView = new ViewPhtml(__NAMESPACE__);
        $html_script = $oView->renderizar('select1303.phtml', $a_campos);

        // para más de un ca
        $ca_num = 0;
        $html = '';
        foreach ($this->cAsistencias as $oAsistente) {
            $ca_num++;
            $html .= $this->getHtmlCa($oAsistente, $ca_num);
        }
        if (count($this->cAsistencias) == 0) {
            $html .= _("no tiene ninguna actividad asignada. O no es de mi dl");
        }
        return $html_script . $html;
    }

    public function getId_dossier()
    {
        return $this->id_dossier;
    }

    public function getPau()
    {
        return $this->pau;
    }

    public function getObj_pau()
    {
        return $this->obj_pau;
    }

    public function getId_pau()
    {
        return $this->id_pau;
    }

    public function getPermiso()
    {
        return $this->permiso;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setId_dossier($Qid_dossier)
    {
        $this->id_dossier = $Qid_dossier;
    }

    public function setPau($Qpau)
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau($Qobj_pau)
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau($Qid_pau)
    {
        $this->id_pau = $Qid_pau;
    }

    public function setPermiso($Qpermiso)
    {
        $this->permiso = $Qpermiso;
    }

    public function setStatus($Qstatus)
    {
        $this->status = $Qstatus;
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

    public function setQId_activ($Qid_activ)
    {
        $this->Qid_activ = $Qid_activ;
    }

}
