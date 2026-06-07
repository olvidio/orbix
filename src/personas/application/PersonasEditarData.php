<?php

namespace src\personas\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\value_objects\IncCode;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

/**
 * Caso de uso detras del endpoint `/src/personas/personas_editar_data`.
 *
 * Prepara los datos necesarios para la ficha de persona
 * (`persona_form.phtml`, `persona_sss_form.phtml`, `p_public_personas.phtml`,
 * `persona_de_paso.phtml`):
 *
 *  - valores de la persona (o defaults si `nuevo=1`),
 *  - `id_tabla` canonico segun `obj_pau`,
 *  - opciones para los `<select>` de delegaciones, centros, situacion, lengua,
 *    nivel_stgr e incorporacion.
 *
 * El frontend sigue siendo responsable del `switch($Qobj_pau)` que decide
 * `presentacion` y `ok/ok_txt/botones` porque depende de `$_SESSION['oPerm']`
 * y de la plantilla a renderizar. Aqui no hay HTML.
 */
final class PersonasEditarData
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroRepositoryInterface $centroRepository,
        private DelegacionRepositoryInterface $delegacionRepository,
        private SituacionRepositoryInterface $situacionRepository,
        private LocalRepositoryInterface $localRepository,
    ) {
    }

    /**
     * @param array<string,mixed> $input habitualmente `$_POST`
     * @return array<string,mixed>
     */
    public function execute(array $input): array
    {
        $Qnuevo = input_int($input, 'nuevo');
        $Qobj_pau = input_string($input, 'obj_pau');

        try {
            $repoPersona = $this->personaRepositoryResolver->repositorio($Qobj_pau);
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }

        $out = [
            'Qobj_pau' => $Qobj_pau,
            'trato' => '',
            'nom' => '',
            'apel_fam' => '',
            'nx1' => '',
            'apellido1' => '',
            'nx2' => '',
            'apellido2' => '',
            'lugar_nacimiento' => '',
            'f_nacimiento' => '',
            'f_situacion' => '',
            'profesion' => '',
            'sacd' => '',
            'eap' => '',
            'inc' => '',
            'f_inc' => '',
            'ce' => '',
            'ce_lugar' => '',
            'ce_ini' => '',
            'ce_fin' => '',
            'observ' => '',
            'titulo' => '',
            'nom_ctr' => '',
            'id_ctr' => '',
            'id_tabla' => input_string($input, 'tabla'),
            'dl' => '',
            'idioma_preferido' => '',
            'situacion' => '',
            'nivel_stgr' => '',
            'edad' => '',
        ];

        if (!empty($Qnuevo)) {
            $Qapellido1 = input_string($input, 'apellido1');
            $out['apellido1'] = urldecode($Qapellido1);
            $out['f_situacion'] = (new DateTimeLocal())->getFromLocal();
            $out['situacion'] = 'A';
            $out['idioma_preferido'] = ConfigGlobal::mi_Idioma();
            $out['dl'] = ConfigGlobal::mi_dele();

            $newIdAuto = $repoPersona->getNewId();
            $out['id_nom'] = $repoPersona->getNewIdNom($newIdAuto);
            $out['nivel_stgr'] = '';
            $out['titulo'] = $out['apellido1'];
        } else {
            $Qid_nom = self::resolveIdNom($input);
            if ($Qid_nom === 0) {
                return ['error' => _("No se ha pasado el id_nom")];
            }

            $oPersona = $repoPersona->findById($Qid_nom);
            if ($oPersona === null) {
                return ['error' => _("No se encuentra la persona")];
            }

            $out['id_nom'] = $Qid_nom;
            $out['id_tabla'] = (string)$oPersona->getId_tabla();
            $out['dl'] = (string)($oPersona->getDl() ?? '');
            $out['nivel_stgr'] = (string)($oPersona->getNivel_stgr() ?? '');
            $out['id_ctr'] = $oPersona instanceof \src\personas\domain\entity\PersonaDl ? (string)($oPersona->getId_ctr() ?? '') : '';
            $out['situacion'] = $oPersona->getSituacion();
            $out['idioma_preferido'] = (string)($oPersona->getIdioma_preferido() ?? '');
            $out['trato'] = (string)($oPersona->getTrato() ?? '');
            $out['nom'] = (string)($oPersona->getNom() ?? '');
            $out['apel_fam'] = (string)($oPersona->getApel_fam() ?? '');
            $out['nx1'] = (string)($oPersona->getNx1() ?? '');
            $out['apellido1'] = $oPersona->getApellido1();
            $out['nx2'] = (string)($oPersona->getNx2() ?? '');
            $out['apellido2'] = (string)($oPersona->getApellido2() ?? '');
            $out['lugar_nacimiento'] = (string)($oPersona->getLugar_nacimiento() ?? '');
            $out['f_nacimiento'] = (string)($oPersona->getF_nacimiento()?->getFromLocal() ?? '');
            $out['f_situacion'] = (string)($oPersona->getF_situacion()?->getFromLocal() ?? '');
            $out['profesion'] = (string)($oPersona->getProfesion() ?? '');
            $out['sacd'] = (string)($oPersona->isSacd() ? 't' : 'f');
            $out['eap'] = (string)($oPersona->getEap() ?? '');
            $out['inc'] = (string)($oPersona->getInc() ?? '');
            $out['f_inc'] = (string)($oPersona->getF_inc()?->getFromLocal() ?? '');
            $out['ce'] = method_exists($oPersona, 'getCe') ? (string)($oPersona->getCe() ?? '') : '';
            $out['ce_lugar'] = method_exists($oPersona, 'getCe_lugar') ? (string)($oPersona->getCe_lugar() ?? '') : '';
            $out['ce_ini'] = method_exists($oPersona, 'getCe_ini') ? (string)($oPersona->getCe_ini() ?? '') : '';
            $out['ce_fin'] = method_exists($oPersona, 'getCe_fin') ? (string)($oPersona->getCe_fin() ?? '') : '';
            $out['observ'] = (string)($oPersona->getObserv() ?? '');
            $out['edad'] = method_exists($oPersona, 'getEdad') && $oPersona->getEdad() !== null
                ? (string)$oPersona->getEdad()
                : '';

            if (!empty($out['id_ctr'])) {
                $centroRepository = ConfigGlobal::mi_ambito() === 'rstgr'
                    ? $this->centroRepository
                    : $this->centroDlRepository;
                $oCentroDl = $centroRepository->findById((int)$out['id_ctr']);
                $out['nom_ctr'] = (string)($oCentroDl?->getNombre_ubi() ?? '');
            }

            $out['titulo'] = (string)$oPersona->getNombreApellidos();
        }

        // id_tabla canonico segun obj_pau (equivalente al switch del controller).
        $id_tabla_map = [
            'PersonaAgd' => 'a',
            'PersonaN' => 'n',
            'PersonaNax' => 'x',
            'PersonaS' => 's',
            'PersonaSSSC' => 'sssc',
        ];
        if (isset($id_tabla_map[$Qobj_pau])) {
            $out['id_tabla'] = $id_tabla_map[$Qobj_pau];
        } elseif ($Qobj_pau === 'PersonaEx' && empty($out['id_tabla'])) {
            $out['id_tabla'] = 'pn';
        }

        // Opciones: delegaciones.
        $repoDl = $this->delegacionRepository;
        $cDeleg = $repoDl->getDelegaciones(['active' => true, '_ordre' => 'dl']);
        $a_dl_todas = [];
        foreach ($cDeleg as $oDeleg) {
            $dl_sigla = (string)$oDeleg->getDlVo()->value();
            $a_dl_todas[$dl_sigla] = $dl_sigla;
        }
        if ($Qnuevo === 1 && $Qobj_pau === 'PersonaEx') {
            $oDBPropiedades = new DBPropiedades();
            $a_dl_esquemas = $oDBPropiedades->array_posibles_dl_de_esquemas(true);
            $opciones_dl = array_diff_key($a_dl_todas, $a_dl_esquemas);
        } else {
            $opciones_dl = $a_dl_todas;
        }
        $out['opciones_dl'] = $opciones_dl;

        // Opciones: centros (solo si no se conoce nom_ctr todavia).
        if (empty($out['nom_ctr'])) {
            $out['opciones_centros'] = $this->centroDlRepository->getArrayCentros();
        } else {
            $out['opciones_centros'] = [];
        }

        // Opciones: situacion.
        $out['opciones_situacion'] = $this->situacionRepository->getArraySituaciones();

        // Opciones: idioma preferido.
        $out['opciones_lengua'] = $this->localRepository->getArrayLocales();

        // Opciones: nivel_stgr.
        $out['opciones_stgr'] = NivelStgrId::getArrayNivelStgr();

        // Opciones: inc.
        $out['opciones_inc'] = IncCode::getArrayIncCode();

        return $out;
    }

    /**
     * @param array<string,mixed> $input
     */
    private static function resolveIdNom(array $input): int
    {
        $a_sel = self::normalizeSel($input['sel'] ?? null);
        if (!empty($a_sel)) {
            return (int)strtok((string)$a_sel[0], '#');
        }
        return input_int($input, 'id_nom');
    }

    /**
     * @param mixed $sel
     * @return array<int,string>
     */
    private static function normalizeSel(mixed $sel): array
    {
        if (is_array($sel)) {
            return array_values(array_filter(array_map(static fn(mixed $v): string => is_scalar($v) ? (string)$v : '', $sel), static fn(string $v): bool => $v !== ''));
        }
        if (is_string($sel) && $sel !== '') {
            return [$sel];
        }
        return [];
    }
}
