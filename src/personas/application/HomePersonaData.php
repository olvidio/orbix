<?php

namespace src\personas\application;

use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\application\support\PersonaSeleccionInput;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\RegionStgrAviso;

/**
 * Caso de uso detras del endpoint `/src/personas/home_persona_data`.
 *
 * Devuelve los datos para la pantalla de cabecera de persona
 * (`home_persona.phtml`): datos basicos, telecos, centro, nivel_stgr traducido
 * y la normalizacion de `Qobj_pau` cuando la entidad es un `PersonaDl` generico.
 */
final class HomePersonaData
{
    /**
     * @param array<string,mixed> $input habitualmente `$_POST`
     * @return array{
     *     error?: string,
     *     Qobj_pau?: string,
     *     id_nom?: int,
     *     titulo?: string,
     *     dl?: string,
     *     f_nacimiento?: string,
     *     situacion?: string,
     *     f_situacion?: string,
     *     profesion?: string,
     *     stgr?: string,
     *     observ?: string,
     *     ctr?: string,
     *     telfs?: string,
     *     mails?: string,
     *     aviso?: string
     * }
     */
    public static function build(array $input): array
    {
        $a_sel = self::normalizeSel($input['sel'] ?? null);
        if (!empty($a_sel)) {
            $id_nom = (int)strtok((string)$a_sel[0], '#');
            $id_tabla = (string)strtok('#');
        } else {
            $seleccion = PersonaSeleccionInput::idNomYTabla($input);
            $id_nom = $seleccion['id_nom'];
            $id_tabla = $seleccion['id_tabla'];
        }

        $Qobj_pau = (string)($input['obj_pau'] ?? '');

        $resolver = new PersonaRepositoryResolver();
        $problemasRegionStgr = [];
        try {
            if ($Qobj_pau === 'PersonaEx') {
                $marcaAviso = false;
                $oPersona = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class)
                    ->findByIdParaListado($id_nom, $problemasRegionStgr, $marcaAviso);
            } else {
                $repoPersona = $resolver->repositorio($Qobj_pau);
                $oPersona = $repoPersona->findById($id_nom);
            }
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }
        if ($oPersona === null) {
            if ($id_nom <= 0) {
                return ['aviso' => RegionStgrAviso::mensajePersonaNoValida()];
            }

            return ['error' => _("No se encuentra la persona")];
        }

        $titulo = (string)$oPersona->getNombreApellidos();
        $dl = (string)($oPersona->getDl() ?? '');
        $f_nacimiento = (string)($oPersona->getF_nacimiento()?->getFromLocal() ?? '');
        $situacion = (string)($oPersona->getSituacion() ?? '');
        $f_situacion = (string)($oPersona->getF_situacion()?->getFromLocal() ?? '');
        $profesion = (string)($oPersona->getProfesion() ?? '');
        $id_nivel_stgr = $oPersona->getNivel_stgr();
        $a_niveles_stgr = NivelStgrId::getArrayNivelStgr();
        $stgr = (string)($a_niveles_stgr[$id_nivel_stgr] ?? '');
        $observ = (string)($oPersona->getObserv() ?? '');

        // PersonaDl generico => subclase real segun id_tabla.
        if (get_class($oPersona) === 'src\\personas\\domain\\entity\\PersonaDl') {
            $map = [
                'n' => 'PersonaN',
                'a' => 'PersonaAgd',
                's' => 'PersonaS',
                'sssc' => 'PersonaSSSC',
            ];
            $Qobj_pau = $map[$oPersona->getId_tabla()] ?? $Qobj_pau;
        }

        $ctr = '';
        if ($Qobj_pau !== 'PersonaEx' && $Qobj_pau !== 'PersonaIn') {
            $id_ctr = $oPersona->getId_ctr();
            if (!empty($id_ctr)) {
                $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $oCentroDl = $CentroDlRepository->findById($id_ctr);
                $ctr = (string)($oCentroDl?->getNombre_ubi() ?? '');
            }
        }

        $telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
        $telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, 'telf', " / ", "*");
        $telfs_movil = $telecoService->getTelecosPorTipo($id_nom, 'móvil', " / ", "*");
        if (!empty($telfs_fijo) && !empty($telfs_movil)) {
            $telfs = $telfs_fijo . " / " . $telfs_movil;
        } else {
            $telfs = ($telfs_fijo ?? '') . ($telfs_movil ?? '');
        }
        $mails = (string)$telecoService->getTelecosPorTipo($id_nom, 'e-mail', " / ", "*");

        $result = [
            'Qobj_pau' => $Qobj_pau,
            'id_nom' => $id_nom,
            'id_tabla' => $id_tabla,
            'titulo' => $titulo,
            'dl' => $dl,
            'f_nacimiento' => $f_nacimiento,
            'situacion' => $situacion,
            'f_situacion' => $f_situacion,
            'profesion' => $profesion,
            'stgr' => $stgr,
            'observ' => $observ,
            'ctr' => $ctr,
            'telfs' => (string)$telfs,
            'mails' => $mails,
        ];
        if ($problemasRegionStgr !== []) {
            $result['aviso'] = RegionStgrAviso::formatear($problemasRegionStgr);
        }

        return $result;
    }

    /**
     * @param mixed $sel
     * @return array<int,string>
     */
    private static function normalizeSel(mixed $sel): array
    {
        if (is_array($sel)) {
            return array_values(array_filter(array_map('strval', $sel), static fn(string $v): bool => $v !== ''));
        }
        if (is_string($sel) && $sel !== '') {
            return [$sel];
        }
        return [];
    }
}
