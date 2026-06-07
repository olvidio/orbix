<?php

namespace src\encargossacd\application\services;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class EncargoAplicacionService
{
    /** @var array<string, array<string, string>> */
    protected array $a_txt = [];

    public function __construct(
        private EncargoTextoRepositoryInterface $encargoTextoRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private EncargoRepositoryInterface $encargoRepository,
    ) {
    }

    private function havePermOficina(string $perm): bool
    {
        $oPerm = $_SESSION['oPerm'] ?? null;
        if (!is_object($oPerm) || !method_exists($oPerm, 'have_perm_oficina')) {
            return false;
        }

        return $oPerm->have_perm_oficina($perm);
    }

    /**
     * @return array<string, string>|string
     */
    public function getArrayTraducciones(string $idioma): array|string
    {
        $idioma = $idioma === '' ? 'es_ES.UTF-8' : $idioma;
        if (empty($this->a_txt[$idioma])) {
            $cEncargoTextos = $this->encargoTextoRepository->getEncargoTextos();
            foreach ($cEncargoTextos as $oEncargoTexto) {
                $clave = $oEncargoTexto->getClaveVo()->value();
                $idioma_x = $oEncargoTexto->getIdiomaVo()->value();
                $textoVo = $oEncargoTexto->getTextoVo();
                $texto = $textoVo !== null ? $textoVo->value() : '';
                $this->a_txt[$idioma_x][$clave] = $texto;
            }
        }
        if (empty($this->a_txt[$idioma])) {
            return sprintf(_("No existe text para el idioma: %s"), $idioma);
        }

        return $this->a_txt[$idioma];
    }

    public function getTraduccion(string $clave, string $idioma): string
    {
        $txt_traduccion = '';
        $a_traduccion = $this->getArrayTraducciones($idioma);
        if (is_array($a_traduccion) && !empty($a_traduccion[$clave])) {
            $txt_traduccion = $a_traduccion[$clave];
        } else {
            $a_traduccion = $this->getArrayTraducciones('es_ES.UTF-8');
            if (is_array($a_traduccion) && !empty($a_traduccion[$clave])) {
                $txt_traduccion = $a_traduccion[$clave];
            } else {
                echo sprintf(_("falta definir el texto %s en este idioma: %s"), $clave, $idioma);
            }
        }

        return $txt_traduccion;
    }

    public function getLugar_dl(): string
    {
        $cCentros = $this->centroDlRepository->getCentros(['tipo_ctr' => 'dl']);
        $num_dl = count($cCentros);
        switch ($num_dl) {
            case 0:
                $cCentros = $this->centroDlRepository->getCentros(['tipo_ctr' => 'cr']);
                if ($cCentros === []) {
                    return '?';
                }
                $oCentro = $cCentros[0];
                break;
            case 1:
                $oCentro = $cCentros[0];
                break;
            default:
                exit(_("Más de un centro definido como dl"));
        }

        $cDirecciones = $oCentro->getDirecciones();
        $poblacion = '';
        if ($cDirecciones === []) {
            exit(_("falta poner la dirección a la dl"));
        }
        $d = 0;
        foreach ($cDirecciones as $oDireccion) {
            $d++;
            if ($d > 1) {
                $poblacion .= '<br>';
            }
            $poblacion .= $oDireccion->getPoblacion();
        }

        return $poblacion;
    }

    public function getF_ini(): DateTimeLocal
    {
        return new DateTimeLocal(date('Y-m-d'));
    }

    public function getF_fin(): DateTimeLocal
    {
        return new DateTimeLocal(date('Y-m-d'));
    }

    /**
     * @return array<string, string>
     */
    public function getArraySeccion(): array
    {
        $seccion = [
            '1' => 'sv',
            '2' => 'sf',
            '3' => 'sss+',
            '4' => 'igl',
            '5' => 'cgi/oc',
            '8' => 'zonas',
        ];
        if ($this->havePermOficina('des') || $this->havePermOficina('vcsd')) {
            return $this->stringKeyedSeccion($seccion);
        }

        unset($seccion['2']);

        return $this->stringKeyedSeccion($seccion);
    }

    /**
     * @param array<int|string, string> $seccion
     * @return array<string, string>
     */
    private function stringKeyedSeccion(array $seccion): array
    {
        $out = [];
        foreach ($seccion as $k => $v) {
            $out[(string) $k] = $v;
        }

        return $out;
    }

    /**
     * @param iterable<EncargoHorario|EncargoSacdHorario> $cEncargoHorarios
     */
    public function getTxtDedicacion(iterable $cEncargoHorarios, string $idioma = ''): string
    {
        $dedicacion_m_txt = '';
        $dedicacion_t_txt = '';
        $dedicacion_v_txt = '';
        foreach ($cEncargoHorarios as $oEncargoHorario) {
            $dia_ref = $oEncargoHorario->getDiaRefVo()?->value();
            $dia_inc = $oEncargoHorario->getDia_inc();
            switch ($dia_ref) {
                case 'm':
                    if ($dia_inc > 1) {
                        $txt = $this->getTraduccion('t_mañana', $idioma);
                        $dedicacion_m_txt = $dia_inc . ' ' . $txt;
                    } else {
                        $txt = $this->getTraduccion('t_mañanas', $idioma);
                        $dedicacion_m_txt = $dia_inc . ' ' . $txt;
                    }
                    break;
                case 't':
                    if ($dia_inc > 1) {
                        $txt = $this->getTraduccion('t_tarde1', $idioma);
                        $dedicacion_t_txt = $dia_inc . ' ' . $txt;
                    } else {
                        $txt = $this->getTraduccion('t_tardes1', $idioma);
                        $dedicacion_t_txt = $dia_inc . ' ' . $txt;
                    }
                    break;
                case 'v':
                    if ($dia_inc > 1) {
                        $txt = $this->getTraduccion('t_tarde2', $idioma);
                        $dedicacion_v_txt = $dia_inc . ' ' . $txt;
                    } else {
                        $txt = $this->getTraduccion('t_tardes2', $idioma);
                        $dedicacion_v_txt = $dia_inc . ' ' . $txt;
                    }
                    break;
            }
        }
        $dedicacion_txt = "($dedicacion_m_txt, $dedicacion_t_txt, $dedicacion_v_txt)";
        $dedicacion_txt = str_replace(', , ', ', ', $dedicacion_txt);
        $dedicacion_txt = str_replace('(, ', '(', $dedicacion_txt);
        $dedicacion_txt = str_replace(', )', ')', $dedicacion_txt);
        if ($dedicacion_txt === '()') {
            $dedicacion_txt = '';
        }

        return $dedicacion_txt;
    }

    public function dedicacion_ctr(int $id_ubi, int $id_enc, string $idioma = ''): string|false
    {
        $aWhere = ['id_enc' => $id_enc, 'f_fin' => 'x'];
        $aOperador = ['f_fin' => 'IS NULL'];
        $cEncargoHorarios = $this->encargoHorarioRepository->getEncargoHorarios($aWhere, $aOperador);

        if ($cEncargoHorarios === []) {
            return false;
        }

        return $this->getTxtDedicacion($cEncargoHorarios, $idioma);
    }

    public function dedicacion(int $id_nom, int $id_enc, string $idioma = ''): string|false
    {
        $aWhere = ['id_enc' => $id_enc, 'id_nom' => $id_nom, 'f_fin' => 'x'];
        $aOperador = ['f_fin' => 'IS NULL'];
        $cEncargoSacdHorario = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        if ($cEncargoSacdHorario === []) {
            return false;
        }

        return $this->getTxtDedicacion($cEncargoSacdHorario, $idioma);
    }

    public function insert_horario_ctr(int $id_enc, string $modulo, mixed $dedicacion, int $n_sacd): void
    {
        if ($n_sacd === 0) {
            $n_sacd = 1;
        }
        $newId_item = $this->encargoHorarioRepository->getNewId();
        $oEncargoHorario = new EncargoHorario();
        $oEncargoHorario->setId_item_h($newId_item);
        $oEncargoHorario->setId_enc($id_enc);
        $oEncargoHorario->setF_ini($this->getF_ini());
        $oEncargoHorario->setF_fin(null);
        $oEncargoHorario->setDiaRefVo($modulo);
        $oEncargoHorario->setDia_inc(is_numeric($dedicacion) ? (int) $dedicacion : null);
        $oEncargoHorario->setN_sacd($n_sacd);
        if ($this->encargoHorarioRepository->Guardar($oEncargoHorario) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $this->encargoHorarioRepository->getErrorTxt();
        }
    }

    public function modificar_horario_ctr(int $id_enc, string $modulo, mixed $dedicacion, int $n_sacd): void
    {
        if ($n_sacd === 0) {
            $n_sacd = 1;
        }

        $aWhere = ['id_enc' => $id_enc, 'dia_ref' => $modulo, 'f_fin' => 'x'];
        $aOperador = ['f_fin' => 'IS NULL'];
        $cEncargoHorarios = $this->encargoHorarioRepository->getEncargoHorarios($aWhere, $aOperador);
        if ($cEncargoHorarios === []) {
            if ($dedicacion !== '' && $dedicacion !== null) {
                $this->insert_horario_ctr($id_enc, $modulo, $dedicacion, $n_sacd);
            }
        } else {
            $oEncargoHoraio = $cEncargoHorarios[0];
            $dia_inc = $oEncargoHoraio->getDia_inc();
            $oEncargoHoraio->setDia_inc(is_numeric($dedicacion) ? (int) $dedicacion : null);
            $oEncargoHoraio->setN_sacd($n_sacd);
            if ($this->encargoHorarioRepository->Guardar($oEncargoHoraio) === false) {
                echo _("hay un error, no se ha guardado");
            }

            if ($dedicacion !== '' && $dedicacion !== null && $dia_inc != $dedicacion) {
                $this->insert_horario_ctr($id_enc, $modulo, $dedicacion, $n_sacd);
            }
        }
    }

    public function insert_horario_sacd(int $id_item_t_sacd, int $id_enc, int $id_nom, string $modulo, mixed $dedicacion): void
    {
        $newId_item = $this->encargoSacdHorarioRepository->getNewId();
        $oEncargoSacdHorario = new EncargoSacdHorario();
        $oEncargoSacdHorario->setId_item($newId_item);
        $oEncargoSacdHorario->setId_enc($id_enc);
        $oEncargoSacdHorario->setId_nom($id_nom);
        $oEncargoSacdHorario->setF_ini($this->getF_fin());
        $oEncargoSacdHorario->setF_fin(null);
        $oEncargoSacdHorario->setDiaRefVo($modulo);
        $oEncargoSacdHorario->setDia_inc(is_numeric($dedicacion) ? (int) $dedicacion : null);
        $oEncargoSacdHorario->setId_item_tarea_sacd($id_item_t_sacd);
        if ($this->encargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $this->encargoSacdHorarioRepository->getErrorTxt();
        }
    }

    public function finalizar_horario_sacd(int $id_enc, int $id_nom, DateTimeLocal $f_fin): void
    {
        $aWhere = ['id_enc' => $id_enc, 'id_nom' => $id_nom, 'f_fin' => 'x'];
        $aOperador = ['f_fin' => 'IS NULL'];
        $cEncargoSacdHorario = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        foreach ($cEncargoSacdHorario as $oEncargoSacdHorario) {
            $oEncargoSacdHorario->setF_fin($f_fin);
            if ($this->encargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $this->encargoSacdHorarioRepository->getErrorTxt();
            }
        }
    }

    public function modificar_horario_sacd(int $id_item_t_sacd, int $id_enc, int $id_nom, string $modulo, mixed $dedicacion): void
    {
        $aWhere = ['id_enc' => $id_enc, 'id_nom' => $id_nom, 'dia_ref' => $modulo, 'f_fin' => 'x'];
        $aOperador = ['f_fin' => 'IS NULL'];
        $cEncargoSacdHorario = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        if ($cEncargoSacdHorario === []) {
            if ($dedicacion !== '' && $dedicacion !== null) {
                $this->insert_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, $modulo, $dedicacion);
            }
        } else {
            $oEncargoSacdHorario = $cEncargoSacdHorario[0];
            $dia_inc = $oEncargoSacdHorario->getDia_inc();
            if ($dedicacion !== '' && $dedicacion !== null) {
                if ($dia_inc != $dedicacion) {
                    if ($oEncargoSacdHorario->getF_ini() == $this->getF_ini()) {
                        $oEncargoSacdHorario->setDia_inc(is_numeric($dedicacion) ? (int) $dedicacion : null);
                        if ($this->encargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                            echo _("hay un error, no se ha guardado");
                        }
                    } else {
                        $oEncargoSacdHorario->setF_fin($this->getF_fin());
                        if ($this->encargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                            echo _("hay un error, no se ha guardado");
                        }
                        $this->insert_horario_sacd($id_item_t_sacd, $id_enc, $id_nom, $modulo, $dedicacion);
                    }
                } else {
                    $oFactual_f_fin = $oEncargoSacdHorario->getF_fin();
                    if ($oFactual_f_fin == $this->getF_fin()) {
                        $oEncargoSacdHorario->setF_fin(null);
                        if ($this->encargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                            echo _("hay un error, no se ha guardado");
                        }
                    }
                }
            } else {
                $oEncargoSacdHorario->setDia_inc(null);
                $oEncargoSacdHorario->setF_fin($this->getF_fin());
                if ($this->encargoSacdHorarioRepository->Guardar($oEncargoSacdHorario) === false) {
                    echo _("hay un error, no se ha guardado");
                }
            }
        }
    }

    public function insert_sacd(int $id_enc, int $id_sacd, int $modo): ?EncargoSacd
    {
        $cEncargosSacd = $this->encargoSacdRepository->getEncargosSacd(['id_enc' => $id_enc, 'id_nom' => $id_sacd, 'modo' => $modo]);
        $flag = 0;
        $oEncargoSacd = null;
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $oFactual_f_fin = $oEncargoSacd->getF_fin();
            $oFactual_f_ini = $oEncargoSacd->getF_ini();
            if ($oFactual_f_fin == $this->getF_fin() || $oFactual_f_ini == $this->getF_ini()) {
                $oEncargoSacd->setF_fin(null);
                if ($this->encargoSacdRepository->Guardar($oEncargoSacd) === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $this->encargoSacdRepository->getErrorTxt();
                }
                $flag = 1;
            }
            if (empty($oFactual_f_fin)) {
                $flag = 1;
            }
        }
        if ($flag === 0) {
            $newId_item = $this->encargoSacdRepository->getNewId();
            $oEncargoSacd = new EncargoSacd();
            $oEncargoSacd->setId_item($newId_item);
            $oEncargoSacd->setId_enc($id_enc);
            $oEncargoSacd->setId_nom($id_sacd);
            $oEncargoSacd->setModo($modo);
            $oEncargoSacd->setF_ini($this->getF_ini());
            $oEncargoSacd->setF_fin(null);
            if ($this->encargoSacdRepository->Guardar($oEncargoSacd) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $this->encargoSacdRepository->getErrorTxt();
            }
        }

        return $oEncargoSacd;
    }

    public function finalizar_sacd(int $id_enc, int $id_sacd, int $modo, DateTimeLocal $f_fin): void
    {
        $cEncargosSacd = $this->encargoSacdRepository->getEncargosSacd(['id_enc' => $id_enc, 'id_nom' => $id_sacd, 'modo' => $modo]);
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $oEncargoSacd->setF_fin($f_fin);
            if ($this->encargoSacdRepository->Guardar($oEncargoSacd) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $this->encargoSacdRepository->getErrorTxt();
            }
        }
    }

    public function delete_sacd(int $id_enc, int $id_sacd, int $modo): void
    {
        $cEncargosSacd = $this->encargoSacdRepository->getEncargosSacd(['id_enc' => $id_enc, 'id_nom' => $id_sacd, 'modo' => $modo]);
        foreach ($cEncargosSacd as $oEncargoSacd) {
            if ($this->encargoSacdRepository->Eliminar($oEncargoSacd) === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $this->encargoSacdRepository->getErrorTxt();
            }
        }
    }

    public function crear_encargo(
        int $id_tipo_enc,
        int $sf_sv,
        int $id_ubi,
        int $id_zona,
        string $desc_enc,
        string $idioma_enc,
        string $desc_lugar,
        string $observ,
    ): int {
        $newId_enc = $this->encargoRepository->getNewId();
        $oEncargo = new Encargo();
        $oEncargo->setId_enc($newId_enc);
        $oEncargo->setId_tipo_enc($id_tipo_enc);
        $grupo = EncargoGrupo::fromNullableInt($sf_sv);
        if ($grupo === null) {
            throw new \InvalidArgumentException('grupo_encargo invalido');
        }
        $oEncargo->setGrupoEncargoVo($grupo);
        $oEncargo->setId_ubi($id_ubi);
        $oEncargo->setId_zona($id_zona);
        $oEncargo->setDesc_enc($desc_enc);
        $oEncargo->setIdioma_enc($idioma_enc);
        $oEncargo->setDesc_lugar($desc_lugar);
        $oEncargo->setObservVo($observ);
        if ($this->encargoRepository->Guardar($oEncargo) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $this->encargoRepository->getErrorTxt();
        }

        return $oEncargo->getId_enc();
    }

    public function grabar_alumnos(int $id_ubi, int $num_alum): void
    {
        // Legacy no-op: el metodo no existia en el servicio original.
    }
}
