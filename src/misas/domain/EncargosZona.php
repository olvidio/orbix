<?php

declare(strict_types=1);

namespace src\misas\domain;

use DateInterval;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\shared\domain\value_objects\DateTimeLocal;

class EncargosZona
{
    private EncargoHorarioRepositoryInterface $encargoHorarioRepository;
    private EncargoRepositoryInterface $encargoRepository;

    protected int $id_zona;

    /** @var list<int> */
    protected array $a_tipo_enc;

    protected string $orden;
    private DateTimeLocal $inicio;
    private DateTimeLocal $fin;

    public function __construct(
        int $id_zona,
        DateTimeLocal $inicio,
        DateTimeLocal $fin,
        EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        EncargoRepositoryInterface $encargoRepository,
        string $orden = '',
    ) {
        $this->encargoHorarioRepository = $encargoHorarioRepository;
        $this->encargoRepository = $encargoRepository;

        $this->id_zona = $id_zona;
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->orden = $orden;
        $this->a_tipo_enc = [];
    }

    /**
     * @return array<int|string, array<int|string, array<int|string, array<int|string, string>>>>
     */
    public function cuadriculaSemana(): array
    {
        $a_ctr_enc = [];

        $cEncargos = $this->getEncargos();
        $id_ubi_anterior = '';

        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $id_ubi = $oEncargo->getId_ubi();

            if ($id_ubi !== $id_ubi_anterior) {
                $id_ubi_anterior = $id_ubi;
            }

            $oInicio = $this->getInicio();
            $oFin = $this->getFin();
            $oFecha = clone $oInicio;

            while ($oFecha <= $oFin) {
                $dia_num_semana = (int) $oFecha->format('N');
                $a_dia_semana = $this->getDiaSemana($dia_num_semana);

                $a_ctr_enc[$id_ubi][$id_tipo_enc][$id_enc][$dia_num_semana] = '+';

                $aWhere = [];
                $aOperador = [];
                $aWhere['id_enc'] = $id_enc;
                $inicio_iso = $oInicio->getIso();
                $fin_iso = $oFin->getIso();
                $aWhere['f_ini'] = "'$inicio_iso'";
                $aOperador['f_ini'] = '>=';
                $aWhere['f_fin'] = "'$fin_iso'";
                $aOperador['f_fin'] = '>=';

                $cEncargoHorario = $this->encargoHorarioRepository->getEncargoHorarios($aWhere, $aOperador);
                foreach ($cEncargoHorario as $oEncargoHorario) {
                    $dia_ref = strtolower($oEncargoHorario->getDia_ref() ?? '');
                    if ($dia_ref === $a_dia_semana) {
                        $a_ctr_enc[$id_ubi][$id_tipo_enc][$id_enc][$dia_num_semana] = _('sin sacd');
                    }
                }
                $oFecha->add(new DateInterval('P1D'));
            }
        }

        return $a_ctr_enc;
    }

    /**
     * @return list<Encargo>
     */
    public function getEncargos(): array
    {
        $aWhere = [];
        $aOperador = [];

        $cond_tipo_enc = '{' . implode(', ', $this->a_tipo_enc) . '}';
        $aWhere['id_tipo_enc'] = $cond_tipo_enc;
        $aOperador['id_tipo_enc'] = 'ANY';
        $aWhere['id_zona'] = $this->id_zona;
        if ($this->orden !== '') {
            $aWhere['_ordre'] = $this->orden;
        }

        return $this->encargoRepository->getEncargos($aWhere, $aOperador);
    }

    private function getDiaSemana(int $diaNum): string
    {
        return $this->getDiasSemanaMap()[$diaNum] ?? '';
    }

    /**
     * @return array<int, string>
     */
    private function getDiasSemanaMap(): array
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

    /**
     * @return list<int>
     */
    public function getATipoEnc(): array
    {
        return $this->a_tipo_enc;
    }

    /**
     * @param list<int> $a_tipo_enc
     */
    public function setATipoEnc(array $a_tipo_enc): void
    {
        $this->a_tipo_enc = $a_tipo_enc;
    }

    public function getFin(): DateTimeLocal
    {
        return $this->fin;
    }

    public function setFin(DateTimeLocal $fin): void
    {
        $this->fin = $fin;
    }

    public function getInicio(): DateTimeLocal
    {
        return $this->inicio;
    }

    public function setInicio(DateTimeLocal $inicio): void
    {
        $this->inicio = $inicio;
    }
}
