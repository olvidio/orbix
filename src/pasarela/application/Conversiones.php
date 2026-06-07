<?php

namespace src\pasarela\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\actividades\domain\entity\TiposActividades;
use src\pasarela\domain\Activacion;
use src\pasarela\domain\ContribucionNoDuerme;
use src\pasarela\domain\ContribucionReserva;
use src\pasarela\domain\Nombre;

/**
 * Compone, para cada `id_tipo_activ`, los valores derivados de los parámetros de
 * pasarela (activacion, contribuciones, nombre, perfil, tipo): mezcla los defaults
 * con las excepciones declaradas en `pasarela_dl`.
 */
class Conversiones
{
    /** @var list<TipoDeActividad> */
    private array $c_tipos_activ = [];

    /** @var array<int|string, string> */
    private array $a_tipos_nom = [];

    /** @var array<int|string, string> */
    private array $a_tipos_activ1 = [];

    /** @var array<int|string, string> */
    private array $a_tipos_asistentes = [];

    /** @var array<int|string, int|string> */
    private array $a_tipos_activacion = [];

    /** @var array<int|string, int> */
    private array $a_tipos_contribucion_no_duerme = [];

    /** @var array<int|string, int> */
    private array $a_tipos_contribucion_reserva = [];

    public function __construct(
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly Activacion $activacion,
        private readonly ContribucionNoDuerme $contribucionNoDuerme,
        private readonly ContribucionReserva $contribucionReserva,
        private readonly Nombre $nombre,
    ) {
    }

    /** @return array<int|string, int> */
    public function getArrayContribucionReserva(): array
    {
        $default = $this->contribucionReserva->getDefault();
        $a_excepciones = $this->contribucionReserva->getExcepciones();
        $a_tipos = $this->getArrayTipos_contribucion_reserva($default);

        return array_replace($a_tipos, $a_excepciones);
    }

    /** @return array<int|string, int> */
    public function getArrayContribucionNoDuerme(): array
    {
        $default = $this->contribucionNoDuerme->getDefault();
        $a_excepciones = $this->contribucionNoDuerme->getExcepciones();
        $a_tipos = $this->getArrayTipos_contribucion_no_duerme($default);

        return array_replace($a_tipos, $a_excepciones);
    }

    /** @return array<int|string, int|string> */
    public function getArrayActivacion(): array
    {
        $default = $this->activacion->getDefault();
        $a_excepciones = $this->activacion->getExcepciones();
        $a_tipos = $this->getArrayTipos_activacion($default);

        return array_replace($a_tipos, $a_excepciones);
    }

    /** @return array<int|string, string> */
    public function getArrayPerfil(): array
    {
        return $this->getArrayTipos_asistentes();
    }

    /** @return array<int|string, string> */
    public function getArrayNombre(): array
    {
        $a_tipos = $this->getArrayTipos_nombre();
        $a_excepciones = $this->nombre->getExcepciones();

        return array_replace($a_tipos, $a_excepciones);
    }

    /** @return array<int|string, string> */
    public function getArrayTipo(): array
    {
        return $this->getArrayTipos_actividad();
    }

    /**
     * @param int|null $default
     * @return array<int|string, int>
     */
    private function getArrayTipos_contribucion_reserva(?int $default): array
    {
        $this->getcTiposDeActividades();
        if ($this->a_tipos_contribucion_reserva === []) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $a_tipos[$id_tipo_activ] = $default ?? 0;
            }
            $this->a_tipos_contribucion_reserva = $a_tipos;
        }

        return $this->a_tipos_contribucion_reserva;
    }

    /**
     * @param int|null $default
     * @return array<int|string, int>
     */
    private function getArrayTipos_contribucion_no_duerme(?int $default): array
    {
        $this->getcTiposDeActividades();
        if ($this->a_tipos_contribucion_no_duerme === []) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $a_tipos[$id_tipo_activ] = $default ?? 0;
            }
            $this->a_tipos_contribucion_no_duerme = $a_tipos;
        }

        return $this->a_tipos_contribucion_no_duerme;
    }

    /**
     * @param int|string|null $default
     * @return array<int|string, int|string>
     */
    private function getArrayTipos_activacion(int|string|null $default): array
    {
        $this->getcTiposDeActividades();
        if ($this->a_tipos_activacion === []) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $a_tipos[$id_tipo_activ] = $default ?? '';
            }
            $this->a_tipos_activacion = $a_tipos;
        }

        return $this->a_tipos_activacion;
    }

    /** @return array<int|string, string> */
    private function getArrayTipos_asistentes(): array
    {
        $this->getcTiposDeActividades();
        if ($this->a_tipos_asistentes === []) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $oTiposActividades = new TiposActividades($id_tipo_activ, true);
                switch ($oTiposActividades->getAsistentesText()) {
                    case 'sg':
                        $a_tipos[$id_tipo_activ] = _('CP/AMIG');
                        break;
                    case 'sss+':
                        $a_tipos[$id_tipo_activ] = _('SACD');
                        break;
                    case 'sr':
                    case 'sr-nax':
                    case 'sr-agd':
                        $a_tipos[$id_tipo_activ] = _('SR');
                        $nom_asistentes = $oTiposActividades->getActividad2DigitosText();
                        if (strpos($nom_asistentes, 'univ') !== false) {
                            $a_tipos[$id_tipo_activ] = _('SR-UNIV');
                        }
                        if (strpos($nom_asistentes, 'bach') !== false) {
                            $a_tipos[$id_tipo_activ] = _('SR-BACH');
                        }
                        break;
                    default:
                        $a_tipos[$id_tipo_activ] = strtoupper($oTiposActividades->getAsistentesText());
                }
            }
            $this->a_tipos_asistentes = $a_tipos;
        }

        return $this->a_tipos_asistentes;
    }

    /** @return array<int|string, string> */
    private function getArrayTipos_actividad(): array
    {
        $this->getcTiposDeActividades();
        if ($this->a_tipos_activ1 === []) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $oTiposActividades = new TiposActividades($id_tipo_activ);
                if ($oTiposActividades->getActividadText() === 'crt') {
                    $a_tipos[$id_tipo_activ] = _('curso retiro');
                } else {
                    $a_tipos[$id_tipo_activ] = _('convivencia');
                }
            }
            $this->a_tipos_activ1 = $a_tipos;
        }

        return $this->a_tipos_activ1;
    }

    /** @return array<int|string, string> */
    private function getArrayTipos_nombre(): array
    {
        $this->getcTiposDeActividades();
        if ($this->a_tipos_nom === []) {
            $a_tipos = [];
            foreach ($this->c_tipos_activ as $oTipo) {
                $id_tipo_activ = $oTipo->getId_tipo_activ();
                $oTiposActividades = new TiposActividades($id_tipo_activ);
                $a_tipos[$id_tipo_activ] = $oTiposActividades->getNomPasarela();
            }
            $this->a_tipos_nom = $a_tipos;
        }

        return $this->a_tipos_nom;
    }

    /** @return list<TipoDeActividad> */
    private function getcTiposDeActividades(): array
    {
        if ($this->c_tipos_activ === []) {
            $aWhere = ['_ordre' => 'id_tipo_activ'];
            $this->c_tipos_activ = $this->tipoDeActividadRepository->getTiposDeActividades($aWhere);
        }

        return $this->c_tipos_activ;
    }
}
