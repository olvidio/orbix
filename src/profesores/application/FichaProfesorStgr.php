<?php

namespace src\profesores\application;

use src\shared\config\ConfigGlobal;
use src\dossiers\application\PermDossier;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\profesores\domain\value_objects\CongresoTipo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\profesores\domain\InfoProfesorAmpliacion;
use src\profesores\domain\InfoProfesorCongreso;
use src\profesores\domain\InfoProfesorDirector;
use src\profesores\domain\InfoProfesorDocenciaStgr;
use src\profesores\domain\InfoProfesorJuramento;
use src\profesores\domain\InfoProfesorLatin;
use src\profesores\domain\InfoProfesorPublicacion;
use src\profesores\domain\InfoProfesorStgr;
use src\profesores\domain\InfoProfesorTituloEst;
use function src\shared\domain\helpers\is_true;

class FichaProfesorStgr
{
    /**
     * @param string $obj_pau p.ej. clase Persona*; se usa en enlaces a tablaDB_lista_ver
     */
    public static function getFichaData(
        int $id_nom,
        string $id_tabla,
        bool $print = false,
        string $obj_pau = '',
        string $permiso = '',
        string $depende = ''
    ): array {
        $a_tipos_dossier = [
            1012 => 'publicaciones',
            1017 => 'curriculum',
            1018 => 'nombramientos',
            1019 => 'ampliacion',
            1020 => 'director',
            1021 => 'juramento',
            1022 => 'latin',
            1024 => 'congresos',
            1025 => 'docencia',
        ];

        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $aPerm = [];
        foreach ($a_tipos_dossier as $id_tipo_dossier => $nom_dossier) {
            $oTipoDossier = $TipoDossierRepository->findById($id_tipo_dossier);
            $permiso_lectura = $oTipoDossier->getPermiso_lectura();
            $permiso_escritura = $oTipoDossier->getPermiso_escritura();
            $depende_modificar = $oTipoDossier->isDepende_modificar();

            $oPermDossier = new PermDossier();
            $aPerm[$nom_dossier] = $oPermDossier->permiso(
                $permiso_lectura,
                $permiso_escritura,
                $depende_modificar,
                'p',
                $id_nom
            );
        }

        $aWhere = ['id_nom' => $id_nom, '_ordre' => 'f_nombramiento'];
        $aOperador = [];
        if ($print) {
            $aWhere['f_cese'] = 'NULL';
            $aOperador['f_cese'] = 'IS NULL';
        }

        $num_txt = '';
        $agd_txt = '';
        switch ($id_tabla) {
            case 'n':
                $num_txt = 'si';
                break;
            case 'a':
                $agd_txt = 'si';
                break;
        }

        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            return ['error' => sprintf("No encuentro a nadie con id_nom: %s", $id_nom)];
        }

        $nom_ap = $oPersona->getNombreApellidosCrSin();
        $sacd_txt = is_true($oPersona->isSacd()) ? 'si' : '';
        $id_ctr = $oPersona->getid_ctr();

        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $oCentro = $GLOBALS['container']->get(CentroRepositoryInterface::class)->findById($id_ctr);
        } else {
            $oCentro = $GLOBALS['container']->get(CentroDlRepositoryInterface::class)->findById($id_ctr);
        }
        $nombre_ubi = $oCentro->getNombre_ubi();

        $latin = $GLOBALS['container']->get(ProfesorLatinRepositoryInterface::class)->findById($id_nom)?->isLatin();
        $latin_txt = is_true($latin) ? 'si' : '';

        $DepartamentoRepository = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);
        $ProfesorTipoRepository = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
        $ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $cProfesores = $ProfesorRepository->getProfesoresStgr($aWhere, $aOperador);

        $a_nombramientos = [];
        $dep = '';
        foreach ($cProfesores as $oProfesor) {
            $id_departamento = $oProfesor->getId_departamento();
            $f_cese = $oProfesor->getF_cese()?->getFromLocal();
            $departamento = $DepartamentoRepository->findById($id_departamento)->getNombreDepartamentoVo()->value();
            $oProfesorTipo = $ProfesorTipoRepository->findById($oProfesor->getId_tipo_profesor());

            $a_nombramientos[] = [
                'departamento' => $departamento,
                'tipo_profesor' => $oProfesorTipo->getTipo_profesor(),
                'f_nombramiento' => $oProfesor->getF_nombramiento()?->getFromLocal(),
                'escrito_nombramiento' => $oProfesor->getEscrito_nombramiento(),
                'f_cese' => $f_cese,
                'escrito_cese' => $oProfesor->getEscrito_cese(),
            ];
            if (empty($f_cese)) {
                $dep .= empty($dep) ? '' : '; ';
                $dep .= $departamento;
            }
        }

        $ProfesorTituloEstRepository = $GLOBALS['container']->get(ProfesorTituloEstRepositoryInterface::class);
        $cTitulosEst = $ProfesorTituloEstRepository->getProfesorTitulosEst(['id_nom' => $id_nom, '_ordre' => 'year']);
        $a_curriculum = [];
        foreach ($cTitulosEst as $oProfesorTituloEst) {
            $a_curriculum[] = [
                'eclesiastico' => $oProfesorTituloEst->isEclesiastico(),
                'titulo' => $oProfesorTituloEst->getTitulo(),
                'centro_dnt' => $oProfesorTituloEst->getCentro_dnt(),
                'year' => $oProfesorTituloEst->getYear(),
            ];
        }

        $ProfesorAmpliacionRepository = $GLOBALS['container']->get(ProfesorAmpliacionRepositoryInterface::class);
        $cProfesorAmpliaciones = $ProfesorAmpliacionRepository->getProfesorAmpliaciones($aWhere, $aOperador);
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $a_ampliacion = [];
        foreach ($cProfesorAmpliaciones as $oProfesorAmpliacion) {
            $id_asignatura = $oProfesorAmpliacion->getId_asignatura();
            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $a_ampliacion[] = [
                'nombre_corto' => $oAsignatura->getNombre_corto(),
                'f_nombramiento' => $oProfesorAmpliacion->getF_nombramiento()?->getFromLocal(),
                'escrito_nombramiento' => $oProfesorAmpliacion->getEscrito_nombramiento(),
                'f_cese' => $oProfesorAmpliacion->getF_cese()?->getFromLocal(),
                'escrito_cese' => $oProfesorAmpliacion->getEscrito_cese(),
            ];
        }

        $ProfesorCongresoRepository = $GLOBALS['container']->get(ProfesorCongresoRepositoryInterface::class);
        $cProfesorCongresos = $ProfesorCongresoRepository->getProfesorCongresos(['id_nom' => $id_nom, '_ordre' => 'f_ini']);
        $a_tipos_congreso = CongresoTipo::getArrayTiposCongreso();
        $a_congresos = [];
        foreach ($cProfesorCongresos as $oProfesorCongreso) {
            $tipo = $oProfesorCongreso->getTipo();
            $a_congresos[] = [
                'tipo' => empty($a_tipos_congreso[$tipo]) ? '' : $a_tipos_congreso[$tipo],
                'congreso' => $oProfesorCongreso->getCongreso(),
                'lugar' => $oProfesorCongreso->getLugar(),
                'f_ini' => $oProfesorCongreso->getF_ini()?->getFromLocal(),
                'f_fin' => $oProfesorCongreso->getF_fin()?->getFromLocal(),
                'organiza' => $oProfesorCongreso->getOrganiza(),
            ];
        }

        $ProfesorDocenciaStgrRepository = $GLOBALS['container']->get(ProfesorDocenciaStgrRepositoryInterface::class);
        $cDocencias = $ProfesorDocenciaStgrRepository->getProfesorDocenciasStgr(['id_nom' => $id_nom, '_ordre' => 'curso_inicio,id_asignatura']);
        $a_tipos_docencia = TipoActividadAsignatura::getTiposActividad();
        $a_docencias = [];
        foreach ($cDocencias as $oDocencia) {
            $id_asignatura = $oDocencia->getId_asignatura();
            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $tipo = $oDocencia->getTipo();
            $curso_inicio = $oDocencia->getCurso_inicio();
            $a_docencias[] = [
                'curso_inicio' => $curso_inicio,
                'curso_fin' => $curso_inicio + 1,
                'nombre_corto' => $oAsignatura->getNombre_corto(),
                'modo' => empty($tipo) ? '' : $a_tipos_docencia[$tipo],
                'acta' => $oDocencia->getActa(),
            ];
        }

        $f_juramento = '';
        $a_director = [];
        $a_publicaciones = [];
        if (!$print) {
            $ProfesorDirectorRepository = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
            $cDirectores = $ProfesorDirectorRepository->getProfesoresDirectores($aWhere, $aOperador);
            foreach ($cDirectores as $oProfesorDirector) {
                $id_departamento = $oProfesorDirector->getId_departamento();
                $a_director[] = [
                    'departamento' => $DepartamentoRepository->findById($id_departamento)->getNombreDepartamentoVo()->value(),
                    'f_nombramiento' => $oProfesorDirector->getF_nombramiento()?->getFromLocal(),
                    'escrito_nombramiento' => $oProfesorDirector->getEscrito_nombramiento(),
                    'f_cese' => $oProfesorDirector->getF_cese()?->getFromLocal(),
                    'escrito_cese' => $oProfesorDirector->getEscrito_cese(),
                ];
            }

            $JuramentoRepository = $GLOBALS['container']->get(ProfesorJuramentoRepositoryInterface::class);
            $cJuramento = $JuramentoRepository->getProfesorJuramentos(['id_nom' => $id_nom]);
            if (!empty($cJuramento[0])) {
                $f_juramento = $cJuramento[0]->getF_juramento()?->getFromLocal();
            }

            $ProfesorPublicacionRepository = $GLOBALS['container']->get(ProfesorPublicacionRepositoryInterface::class);
            $cProfesorPublicaciones = $ProfesorPublicacionRepository->getProfesorPublicaciones(['id_nom' => $id_nom, '_ordre' => 'f_publicacion']);
            foreach ($cProfesorPublicaciones as $oProfesorPublicacion) {
                $a_publicaciones[] = [
                    'pendiente' => $oProfesorPublicacion->getPendiente(),
                    'tipo_publicacion' => $oProfesorPublicacion->getTipo_publicacion(),
                    'titulo' => $oProfesorPublicacion->getTitulo(),
                    'editorial' => $oProfesorPublicacion->getEditorial(),
                    'coleccion' => $oProfesorPublicacion->getColeccion(),
                    'f_publicacion' => $oProfesorPublicacion->getF_publicacion()?->getFromLocal(),
                    'referencia' => $oProfesorPublicacion->getReferencia(),
                    'lugar' => $oProfesorPublicacion->getLugar(),
                    'observ' => $oProfesorPublicacion->getObserv(),
                ];
            }
        }

        $ficha_self_link_spec = [
            'path' => 'frontend/profesores/controller/ficha_profesor_stgr.php',
            'query' => [
                'id_nom' => $id_nom,
                'id_tabla' => $id_tabla,
                'permiso' => $permiso,
                'depende' => $depende,
            ],
        ];

        $tabla_db_query_base = [
            'pau' => 'p',
            'id_pau' => $id_nom,
            'obj_pau' => $obj_pau,
            'permiso' => $permiso,
            'depende' => $depende,
        ];

        $tablaDbSpec = static function (string $clase_info) use ($tabla_db_query_base): array {
            return [
                'path' => 'frontend/shared/controller/tablaDB_lista_ver.php',
                'query' => array_merge($tabla_db_query_base, ['clase_info' => $clase_info]),
            ];
        };

        $go_cosas_link_specs = [
            'print' => [
                'path' => 'frontend/profesores/controller/ficha_profesor_stgr.php',
                'query' => [
                    'id_nom' => $id_nom,
                    'id_tabla' => $id_tabla,
                    'print' => '1',
                ],
            ],
            'latin' => $tablaDbSpec(InfoProfesorLatin::class),
            'curriculum' => $tablaDbSpec(InfoProfesorTituloEst::class),
            'nombramientos' => $tablaDbSpec(InfoProfesorStgr::class),
            'ampliacion' => $tablaDbSpec(InfoProfesorAmpliacion::class),
            'congresos' => $tablaDbSpec(InfoProfesorCongreso::class),
            'docencia' => $tablaDbSpec(InfoProfesorDocenciaStgr::class),
            'director' => $tablaDbSpec(InfoProfesorDirector::class),
            'juramento' => $tablaDbSpec(InfoProfesorJuramento::class),
            'publicaciones' => $tablaDbSpec(InfoProfesorPublicacion::class),
        ];

        return [
            'error' => '',
            'aPerm' => $aPerm,
            'nom_ap' => $nom_ap,
            'nombre_ubi' => $nombre_ubi,
            'dep' => $dep,
            'num_txt' => $num_txt,
            'agd_txt' => $agd_txt,
            'sacd_txt' => $sacd_txt,
            'latin_txt' => $latin_txt,
            'f_juramento' => $f_juramento,
            'a_curriculum' => $a_curriculum,
            'a_nombramientos' => $a_nombramientos,
            'a_director' => $a_director,
            'a_ampliacion' => $a_ampliacion,
            'a_publicaciones' => $a_publicaciones,
            'a_congresos' => $a_congresos,
            'a_docencias' => $a_docencias,
            'go_cosas_link_specs' => $go_cosas_link_specs,
            'ficha_self_link_spec' => $ficha_self_link_spec,
            'use_print_phtml' => $print,
        ];
    }
}
