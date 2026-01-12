<?php

namespace notas\model;

use NumberFormatter;
use src\actividadestudios\domain\value_objects\NotaMax;
use src\actividadestudios\domain\value_objects\NotaNum;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class PersonaNota
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private array $primaryKey;

    private int $id_schema;
    private int $id_nom;
    private int $id_nivel;
    private ?AsignaturaId $id_asignatura;
    private ?NotaSituacion $id_situacion;
    private ?ActaNumero $acta;
    private DateTimeLocal|NullDateTimeLocal $f_acta;
    private ?string $detalle;
    private bool $preceptor;
    private ?int $id_preceptor;
    private ?NotaEpoca $epoca;
    private ?int $id_activ;
    private ?NotaNum $nota_num;
    private ?NotaMax $nota_max;
    private ?TipoActa $tipo_acta;
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

    /**
     * @deprecated Usar `getIdAsignaturaVo(): ?AsignaturaId` en su lugar.
     */
    public function getIdAsignatura(): ?int
    {
        return $this->id_asignatura?->value();
    }
    public function getIdAsignaturaVo(): ?AsignaturaId
    {
        return $this->id_asignatura;
    }

    /**
     * @deprecated Usar `setIdAsignaturaVo(?AsignaturaId $vo = null): void` en su lugar.
     */
    public function setIdAsignatura(?int $id_asignatura): void
    {
        $this->id_asignatura = AsignaturaId::fromNullableInt($id_asignatura);
    }
    public function setIdAsignaturaVo(AsignaturaId|int|null $valor = null): void
    {
        $this->id_asignatura = $valor instanceof AsignaturaId
            ? $valor
            : AsignaturaId::fromNullableInt($valor);
    }

    /**
     * @deprecated Usar `getIdSituacionVo(): ?NotaSituacion` en su lugar.
     */
    public function getIdSituacion(): ?int
    {
        return $this->id_situacion?->value();
    }
    public function getIdSituacionVo(): ?NotaSituacion
    {
        return $this->id_situacion;
    }

    /**
     * @deprecated Usar `setIdSituacionVo(?NotaSituacion $vo = null): void` en su lugar.
     */
    public function setIdSituacion(?int $id_situacion): void
    {
        $this->id_situacion = NotaSituacion::fromNullableInt($id_situacion);
    }
    public function setIdSituacionVo(NotaSituacion|int|null $valor = null): void
    {
        $this->id_situacion = $valor instanceof NotaSituacion
            ? $valor
            : NotaSituacion::fromNullableInt($valor);
    }

    /**
     * @deprecated Usar `getActaVo(): ?ActaNumero` en su lugar.
     */
    public function getActa(): ?string
    {
        return $this->acta?->value();
    }
    public function getActaVo(): ?ActaNumero
    {
        return $this->acta;
    }

    /**
     * @deprecated Usar `setActaVo(?ActaNumero $vo = null): void` en su lugar.
     */
    public function setActa(?string $acta): void
    {
        $this->acta = ActaNumero::fromNullableString($acta);
    }
    public function setActaVo(ActaNumero|string|null $valor = null): void
    {
        $this->acta = $valor instanceof ActaNumero
            ? $valor
            : ActaNumero::fromNullableString($valor);
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

    /**
     * @deprecated Usar `getEpocaVo(): ?NotaEpoca` en su lugar.
     */
    public function getEpoca(): ?int
    {
        return $this->epoca?->value();
    }
    public function getEpocaVo(): ?NotaEpoca
    {
        return $this->epoca;
    }

    /**
     * @deprecated Usar `setEpocaVo(?NotaEpoca $vo = null): void` en su lugar.
     */
    public function setEpoca(?int $epoca): void
    {
        $this->epoca = NotaEpoca::fromNullableInt($epoca);
    }
    public function setEpocaVo(NotaEpoca|int|null $valor = null): void
    {
        $this->epoca = $valor instanceof NotaEpoca
            ? $valor
            : NotaEpoca::fromNullableInt($valor);
    }

    public function getIdActiv(): ?int
    {
        return $this->id_activ;
    }

    public function setIdActiv(?int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     * @deprecated Usar `getNotaNumVo(): ?NotaNum` en su lugar.
     */
    public function getNotaNum(): ?float
    {
        return $this->nota_num?->value();
    }
    public function getNotaNumVo(): ?NotaNum
    {
        return $this->nota_num;
    }

    /**
     * @deprecated Usar `setNotaNumVo(?NotaNum $vo = null): void` en su lugar.
     */
    public function setNotaNum(?float $nota_num): void
    {
        $this->nota_num = NotaNum::fromNullableFloat($nota_num);
    }
    public function setNotaNumVo(NotaNum|float|null $valor = null): void
    {
        $this->nota_num = $valor instanceof NotaNum
            ? $valor
            : NotaNum::fromNullableFloat($valor);
    }

    /**
     * @deprecated Usar `getNotaMaxVo(): ?NotaMax` en su lugar.
     */
    public function getNotaMax(): ?int
    {
        return $this->nota_max?->value();
    }
    public function getNotaMaxVo(): ?NotaMax
    {
        return $this->nota_max;
    }

    /**
     * @deprecated Usar `setNotaMaxVo(?NotaMax $vo = null): void` en su lugar.
     */
    public function setNotaMax(?int $nota_max): void
    {
        $this->nota_max = NotaMax::fromNullableInt($nota_max);
    }
    public function setNotaMaxVo(NotaMax|int|null $valor = null): void
    {
        $this->nota_max = $valor instanceof NotaMax
            ? $valor
            : NotaMax::fromNullableInt($valor);
    }

    /**
     * @deprecated Usar `getTipoActaVo(): ?TipoActa` en su lugar.
     */
    public function getTipoActa(): ?int
    {
        return $this->tipo_acta?->value();
    }
    public function getTipoActaVo(): ?TipoActa
    {
        return $this->tipo_acta;
    }

    /**
     * @deprecated Usar `setTipoActaVo(?TipoActa $vo = null): void` en su lugar.
     */
    public function setTipoActa(?int $tipo_acta): void
    {
        $this->tipo_acta = TipoActa::fromNullableInt($tipo_acta);
    }
    public function setTipoActaVo(TipoActa|int|null $valor = null): void
    {
        $this->tipo_acta = $valor instanceof TipoActa
            ? $valor
            : TipoActa::fromNullableInt($valor);
    }

}