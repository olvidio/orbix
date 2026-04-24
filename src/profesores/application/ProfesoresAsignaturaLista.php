<?php

namespace src\profesores\application;

use src\shared\config\ConfigGlobal;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\services\TelecoPersonaService;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\services\ProfesorAsignaturaService;

class ProfesoresAsignaturaLista
{
    public static function getTablaData(int $id_asignatura): array
    {
        $ProfesorAsignaturaService = $GLOBALS['container']->get(ProfesorAsignaturaService::class);
        $cProfesores = $ProfesorAsignaturaService->getArrayProfesoresAsignatura(new AsignaturaId($id_asignatura));

        $a_cabeceras = [];
        $a_cabeceras[] = ['name' => ucfirst(_("apellidos, nombre")), 'formatter' => 'clickFormatter'];
        $a_cabeceras[] = ucfirst(_("centro"));
        $a_cabeceras[] = ucfirst(_("docencia"));
        $a_cabeceras[] = ucfirst(_("teléfono"));
        $a_cabeceras[] = ucfirst(_("mail"));

        $a_valores = [];
        $i = 0;
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $ProfesorDocenciaStgrRepository = $GLOBALS['container']->get(ProfesorDocenciaStgrRepositoryInterface::class);
        $TelecoPersonaDlRepository = $GLOBALS['container']->get(TelecoPersonaDlRepositoryInterface::class);
        $telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);

        foreach ($cProfesores['departamento'] as $id_nom => $ap_nom) {
            $i++;
            $oPersonaDl = $PersonaDlRepository->findById($id_nom);
            $centro = $oPersonaDl->getCentro_o_dl();
            if (ConfigGlobal::mi_ambito() === 'rstgr') {
                $centro = $oPersonaDl->getDl() . " - " . $centro;
            }

            $aWhere = [
                'id_nom' => $id_nom,
                'id_asignatura' => $id_asignatura,
                '_ordre' => 'curso_inicio DESC',
            ];
            $cDocencia = $ProfesorDocenciaStgrRepository->getProfesorDocenciasStgr($aWhere);
            $txt_docencia = self::buildTextoDocencia($cDocencia);

            $mails = $telecoService->getTelecosPorTipo($id_nom, 'e-mail', ' / ');
            $telfs = $telecoService->getTelecosPorTipo($id_nom, 'telf', " / ", "*");
            $telfs .= $telecoService->getTelecosPorTipo($id_nom, 'móvil', " / ", "*");

            $a_valores[$i]['sel'] = (string)$id_nom;
            $a_valores[$i][1] = ['ira' => '', 'valor' => $ap_nom];
            $a_valores[$i][2] = $centro;
            $a_valores[$i][3] = $txt_docencia;
            $a_valores[$i][4] = $telfs;
            $a_valores[$i][5] = $mails;
        }

        foreach ($cProfesores['ampliacion'] as $id_nom => $ap_nom) {
            $i++;
            $oPersonaDl = $PersonaDlRepository->findById($id_nom);
            $aWhere = [
                'id_nom' => $id_nom,
                'id_asignatura' => $id_asignatura,
                '_ordre' => 'curso_inicio DESC',
            ];
            $cDocencia = $ProfesorDocenciaStgrRepository->getProfesorDocenciasStgr($aWhere);
            $txt_docencia = self::buildTextoDocencia($cDocencia);

            $telfs = '';
            $mails = '';
            $cTelecoPersona = $TelecoPersonaDlRepository->getTelecosPersona(['id_nom' => $id_nom]);
            foreach ($cTelecoPersona as $oTelecoPersona) {
                $tipo = $oTelecoPersona->getId_tipo_teleco();
                switch ($tipo) {
                    case 3:
                        $mails .= $oTelecoPersona->getNum_teleco();
                        break;
                    case 1:
                    case 2:
                        $telfs .= $oTelecoPersona->getNum_teleco();
                        break;
                }
            }

            $a_valores[$i]['sel'] = (string)$id_nom;
            $a_valores[$i][1] = ['ira' => '', 'valor' => $ap_nom];
            $a_valores[$i][2] = $oPersonaDl->getCentro_o_dl();
            $a_valores[$i][3] = $txt_docencia;
            $a_valores[$i][4] = $telfs;
            $a_valores[$i][5] = $mails;
        }

        return [
            'id_tabla' => 'list_profe_asig',
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'a_botones' => [],
        ];
    }

    private static function buildTextoDocencia(array $cDocencia): string
    {
        $txt_docencia = '';
        foreach ($cDocencia as $oProfesorDocenciaStgr) {
            $inicio_curso = $oProfesorDocenciaStgr->getCurso_inicio();
            $curso = $inicio_curso . '-' . ($inicio_curso + 1);
            $txt_docencia .= empty($txt_docencia) ? $curso : '; ' . $curso;
        }

        return $txt_docencia;
    }
}
