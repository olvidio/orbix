<?php

namespace misas\model;

use DateInterval;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use web\DateTimeLocal;

class EncargosZona
{

    protected int $id_zona;
    protected array $a_tipo_enc;
    protected string $orden;
    private DateTimeLocal $inicio;
    private DateTimeLocal $fin;

    public function __construct($id_zona, $inicio, $fin, $orden = '')
    {
        $this->id_zona = $id_zona;
        $this->inicio = $inicio ?? DateTimeLocal::createFromLocal('1/1/2000');
        $this->fin = $fin ?? DateTimeLocal::createFromLocal('8/1/2000');
        $this->orden = $orden;
    }

    public function cuadriculaSemana(): array
    {
        $a_centros = [];
        $a_ctr_enc = [];

        $cEncargos = $this->getEncargos();
        $id_ubi_anterior = '';

        $EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $id_ubi = $oEncargo->getId_ubi();
            $desc_enc = $oEncargo->getDesc_enc();

            if ($id_ubi !== $id_ubi_anterior) {
                $id_ubi_anterior = $id_ubi;
                $a_centros[] = $id_ubi;
            }

            //$a_ctr_enc[$id_ubi][$id_tipo_enc] = $id_enc;

            // por cada día del periodo
            $oInicio = $this->getInicio();
            $oFin = $this->getFin();
            $oFecha = clone $oInicio;

            while ($oFecha <= $oFin) {

                $dia_num_semana = $oFecha->format('N'); // N 	ISO 8601 numeric representation of the day of the week 	1 (for Monday) through 7 (for Sunday)
                $a_dia_semana = $this->getDiasSemana($dia_num_semana);

                $a_ctr_enc[$id_ubi][$id_tipo_enc][$id_enc][$dia_num_semana] = '+';

                $aWhere['id_enc'] = $id_enc;
                $inicio_iso = $oInicio->getIso();
                $fin_iso = $oFin->getIso();
                $aWhere['f_ini'] = "'$inicio_iso'";
                $aOperador['f_ini'] = '>=';
                $aWhere['f_fin'] = "'$fin_iso'";
                $aOperador['f_fin'] = '>=';

                $cEncargoHorario = $EncargoHorarioRepository->getEncargoHorarios($aWhere, $aOperador);
                foreach ($cEncargoHorario as $oEncargoHorario) {
                    $dia_ref = strtolower($oEncargoHorario->getDia_ref() ?? '');
                    if ($dia_ref === $a_dia_semana) {
                        $a_ctr_enc[$id_ubi][$id_tipo_enc][$id_enc][$dia_num_semana] = _("sin sacd");
                    }
                }
                $oFecha->add(new DateInterval('P1D'));
            }
        }

        return $a_ctr_enc;
    }


    // Un grupo de filas por cada centro
    // una fila por cada tipo de encargo
    //una columna por cada día de la semana

    public function getEncargos()
    {
        $aWhere = [];
        $aOperador = [];

        // encargos de misa (8010) para la zona
        $cond_tipo_enc = "{" . implode(', ', $this->a_tipo_enc) . "}";
        $aWhere['id_tipo_enc'] = $cond_tipo_enc;
        $aOperador['id_tipo_enc'] = 'ANY';
        $aWhere['id_zona'] = $this->id_zona;
//        $aWhere['_ordre'] = 'desc_enc';
        if (!empty($this->orden)) {
            $aWhere['_ordre'] = $this->orden;
        }

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $cEncargos = $EncargoRepository->getEncargos($aWhere, $aOperador);

        return $cEncargos;
    }

    private function getDiasSemana(): array
    {
        return [
            1 => 'l',
            2 => 'm',
            3 => 'x',
            4 => 'j',
            5 => 'v',
            6 => 's',
            7 => 'd',
        ];
    }

    public function getATipoEnc(): array
    {
        return $this->a_tipo_enc;
    }

    public
    function setATipoEnc(array $a_tipo_enc): void
    {
        $this->a_tipo_enc = $a_tipo_enc;
    }

    public
    function getFin(): DateTimeLocal
    {
        return $this->fin;
    }

    public
    function setFin(DateTimeLocal $fin): void
    {
        $this->fin = $fin;
    }

    public
    function getInicio(): DateTimeLocal
    {
        return $this->inicio;
    }

    public
    function setInicio(DateTimeLocal $inicio): void
    {
        $this->inicio = $inicio;
    }

}