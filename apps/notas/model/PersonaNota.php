<?php

namespace notas\model;

use NumberFormatter;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\NotaSituacion;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class PersonaNota
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private array $primaryKey;

    private int $id_schema;
    private int $id_nom;
    private int $id_nivel;
    private ?int $id_asignatura;
    private ?int $id_situacion;
    private ?string $acta;
    private DateTimeLocal|NullDateTimeLocal $f_acta;
    private ?string $detalle;
    private bool $preceptor;
    private ?int $id_preceptor;
    private ?int $epoca;
    private ?int $id_activ;
    private ?float $nota_num;
    private ?int $nota_max;
    private ?int $tipo_acta;

    private bool $aprobada;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    public function __construct(?array $a_id = NULL)
    {
        if (!empty($a_id)) {
            $this->setPrimary_key($a_id);
        }
    }

    public function getPrimary_key(): array
    {
        if (!isset($this->primaryKey)) {
            $this->primaryKey = array('id_nom' => $this->id_nom, 'id_nivel' => $this->id_nivel);
        }
        return $this->primaryKey;
    }

    public function setPrimary_key(?array $a_id = null): void
    {
        if (!empty($a_id)) {
            $this->primaryKey = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_nom') && $val_id !== '') {
                    $this->id_nom = (int)$val_id;
                }
                if (($nom_id === 'id_nivel') && $val_id !== '') {
                    $this->id_nivel = (int)$val_id;
                }
            }
        }
    }

    /**
     * Recupera la nota en forma de texto
     */
    public function getNota_txt(): string
    {
        $id_situacion = $this->getIdSituacion();
        switch ($id_situacion) {
            case '3': // Magna
                $nota_txt = 'Magna cum laude (8,6-9,5/10)';
                break;
            case '4': // Summa
                $nota_txt = 'Summa cum laude (9,6-10/10)';
                break;
            case '10': // Nota numérica
                $num = $this->getNotaNum();
                $max = $this->getNotaMax();
                // deben ser números.
                if (is_numeric($num) && is_numeric($max)) {
                    //$a = new \NumberFormatter("es_ES.UTF-8", \NumberFormatter::DECIMAL);
                    // SI dejo el locale en blanco coge el que se ha definido por defecto en el usuario.
                    $a = new NumberFormatter("", \NumberFormatter::DECIMAL);
                    $num_local = $a->format($num);
                    $nota_txt = $num_local . '/' . $max;
                    if ($max >= 1) {
                        $nota_x_uno = $num / $max;
                        if ($nota_x_uno > 0.95) {
                            $nota_txt = _("Summa cum laude") . ' (' . $nota_txt . ')';
                        } elseif ($nota_x_uno > 0.85) {
                            $nota_txt = _("Magna cum laude") . ' (' . $nota_txt . ')';
                        } elseif ($nota_x_uno > 0.75) {
                            $nota_txt = _("Cum laude") . ' (' . $nota_txt . ')';
                        } elseif ($nota_x_uno > 0.65) {
                            $nota_txt = _("Bene probatus") . ' (' . $nota_txt . ')';
                        } elseif ($nota_x_uno >= 0.6) {
                            $nota_txt = _("Probatus") . ' (' . $nota_txt . ')';
                        } else {
                            $nota_txt = _("Non probatus") . ' (' . $nota_txt . ')';
                        }
                    }
                } else {
                    $nota_txt = sprintf(_("Error: algún número está mal en la base de datos. nota: %s , max: %s"), $num, $max);
                }
                break;
            default:
                $oNota = new Nota($id_situacion);
                $nota_txt = $oNota->getDescripcion();
                break;
        }
        return $nota_txt;
    }

    /**
     * @return boolean
     */
    public function isAprobada()
    {
        $nota_corte = $_SESSION['oConfig']->getNotaCorte();
        $this->aprobada = false;
        if ($this->id_situacion === NotaSituacion::NUMERICA) {
            $nota_num = $this->getNotaNum();
            $nota_max = $this->getNotaMax();
            // deben ser números.
            if (is_numeric($nota_num) && is_numeric($nota_max)) {
                if ($nota_num / $nota_max >= $nota_corte) {
                    $this->aprobada = true;
                }
            }
        } else {
            $NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
            $aNotas = $NotaRepository->getArrayNotasSuperadas();
            $this->aprobada = isset($aNotas[$this->id_situacion]);
        }
        return $this->aprobada;
    }

    public function getIdSchema(): int
    {
        return $this->id_schema;
    }

    public function setIdSchema(int $id_schema): void
    {
        $this->id_schema = $id_schema;
    }

    public function getIdNom(): int
    {
        return $this->id_nom;
    }

    public function setIdNom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    public function getIdNivel(): int
    {
        return $this->id_nivel;
    }

    public function setIdNivel(int $id_nivel): void
    {
        $this->id_nivel = $id_nivel;
    }

    public function getIdAsignatura(): ?int
    {
        return $this->id_asignatura;
    }

    public function setIdAsignatura(?int $id_asignatura): void
    {
        $this->id_asignatura = $id_asignatura;
    }

    public function getIdSituacion(): ?int
    {
        return $this->id_situacion;
    }

    public function setIdSituacion(?int $id_situacion): void
    {
        $this->id_situacion = $id_situacion;
    }

    public function getActa(): ?string
    {
        return $this->acta;
    }

    public function setActa(?string $acta): void
    {
        $this->acta = $acta;
    }

    public function getFActa(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->f_acta;
    }

    public function setFActa(DateTimeLocal|NullDateTimeLocal $f_acta): void
    {
        $this->f_acta = $f_acta;
    }

    public function getDetalle(): ?string
    {
        return $this->detalle;
    }

    public function setDetalle(?string $detalle): void
    {
        $this->detalle = $detalle;
    }

    public function isPreceptor(): bool
    {
        return $this->preceptor;
    }

    public function setPreceptor(bool $preceptor): void
    {
        $this->preceptor = $preceptor;
    }

    public function getIdPreceptor(): ?int
    {
        return $this->id_preceptor;
    }

    public function setIdPreceptor(?int $id_preceptor): void
    {
        $this->id_preceptor = $id_preceptor;
    }

    public function getEpoca(): ?int
    {
        return $this->epoca;
    }

    public function setEpoca(?int $epoca): void
    {
        $this->epoca = $epoca;
    }

    public function getIdActiv(): ?int
    {
        return $this->id_activ;
    }

    public function setIdActiv(?int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    public function getNotaNum(): ?float
    {
        return $this->nota_num;
    }

    public function setNotaNum(?float $nota_num): void
    {
        $this->nota_num = $nota_num;
    }

    public function getNotaMax(): ?int
    {
        return $this->nota_max;
    }

    public function setNotaMax(?int $nota_max): void
    {
        $this->nota_max = $nota_max;
    }

    public function getTipoActa(): ?int
    {
        return $this->tipo_acta;
    }

    public function setTipoActa(?int $tipo_acta): void
    {
        $this->tipo_acta = $tipo_acta;
    }

}