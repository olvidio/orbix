<?php

namespace src\notas\domain\entity;

use NumberFormatter;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Detalle;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaMax;
use src\notas\domain\value_objects\NotaNum;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\PersonaNotaPk;
use src\notas\domain\value_objects\TipoActa;
use src\procesos\domain\value_objects\ActividadId;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;

class PersonaNota
{
    use Hydratable;

    /**
     * @param array<string, mixed>|null $a_id
     */
    public function __construct(?array $a_id = null)
    {
        if ($a_id !== null) {
            foreach ($a_id as $nom_id => $val_id) {
                if (!is_scalar($val_id) || (string) $val_id === '') {
                    continue;
                }
                if ($nom_id === 'id_nom') {
                    $this->id_nom = (int) $val_id;
                }
                if ($nom_id === 'id_asignatura') {
                    $this->id_asignatura = new AsignaturaId((int) $val_id);
                }
                if ($nom_id === 'id_nivel') {
                    $this->id_nivel = new NivelId((int) $val_id);
                }
                if ($nom_id === 'tipo_acta') {
                    $this->tipo_acta = TipoActa::fromNullableInt((int) $val_id);
                }
            }
        }
    }
    /* ATRIBUTOS ----------------------------------------------------------------- */

    protected int $id_schema;

    protected int $id_nom;

    protected NivelId $id_nivel;

    protected AsignaturaId $id_asignatura;

    protected NotaSituacion $id_situacion;

    protected ?ActaNumero $acta;

    protected ?DateTimeLocal $f_acta = null;

    protected ?Detalle $detalle;

    protected ?bool $preceptor;

    protected ?int $id_preceptor;

    protected ?NotaEpoca $epoca;

    protected ?ActividadId $id_activ;

    protected ?NotaNum $nota_num;

    protected ?NotaMax $nota_max;

    protected ?TipoActa $tipo_acta;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    public function getPersonaNotaPk(): PersonaNotaPk {
         return PersonaNotaPk::fromArray(['id_nom' => $this->id_nom ,
            'id_nivel' => $this->id_nivel->value(),
            'tipo_acta' => $this->tipo_acta?->value() ?? TipoActa::FORMATO_ACTA,
        ]);
    }

    public function isAprobada(): bool
    {
        $config = $_SESSION['oConfig'] ?? null;
        if (!is_object($config) || !method_exists($config, 'getNotaCorte')) {
            return false;
        }
        $nota_corte = (float) $config->getNotaCorte();
        if ($this->id_situacion->value() === NotaSituacion::NUMERICA) {
            $nota_num = $this->getNotaNumVo()?->value();
            $nota_max = $this->getNotaMaxVo()?->value();
            if (is_numeric($nota_num) && is_numeric($nota_max) && (float) $nota_max !== 0.0) {
                return (float) $nota_num / (float) $nota_max >= $nota_corte;
            }
            return false;
        }
        return in_array($this->id_situacion->value(), NotaSituacion::getArraySuperadas(), true);
    }

    /**
     * Recupera la nota en forma de text
     *
     * @return string snota_txt
     */
    function getNota_txt(): string
    {
        $nota_txt = 'Hollla';
        $id_situacion = $this->getId_situacion();
        switch ($id_situacion) {
            case '3': // Magna
                $nota_txt = 'Magna cum laude (8,6-9,5/10)';
                break;
            case '4': // Summa
                $nota_txt = 'Summa cum laude (9,6-10/10)';
                break;
            case '10': // Nota numérica
                $num = $this->getNota_num();
                $max = $this->getNota_max();
                // deben ser números.
                if (is_numeric($num) && is_numeric($max)) {
                    //$a = new \NumberFormatter("es_ES.UTF-8", \NumberFormatter::DECIMAL);
                    // SI dejo el locale en blanco coge el que se ha definido por defecto en el usuario.
                    $a = new NumberFormatter("", NumberFormatter::DECIMAL);
                    $num_local = $a->format((float) $num);
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
                $nota_txt = Nota::getStatusTxt($id_situacion);
                break;
        }
        return $nota_txt;
    }

    public function getId_schema(): int
    {
        return $this->id_schema;
    }

    public function setId_schema(int $id_schema): void
    {
        $this->id_schema = $id_schema;
    }

    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     * @deprecated use getIdNivelVo()
     */
    public function getId_nivel(): int
    {
        return $this->id_nivel->value();
    }

    public function getIdNivelVo(): NivelId
    {
        return $this->id_nivel;
    }

    /**
     * @deprecated use setIdNivelVo()
     */
    public function setId_nivel(int $id_nivel): void
    {
        $this->id_nivel = new NivelId((int) $id_nivel);
    }

    public function setIdNivelVo(NivelId|int|null $oIdNivel): void
    {
        if ($oIdNivel instanceof NivelId) {
            $this->id_nivel = $oIdNivel;
        } elseif ($oIdNivel !== null) {
            $this->id_nivel = new NivelId($oIdNivel);
        }
    }

    /**
     * @deprecated use getIdAsignaturaVo()
     */
    public function getId_asignatura(): int
    {
        return $this->id_asignatura->value();
    }

    public function getIdAsignaturaVo(): AsignaturaId
    {
        return $this->id_asignatura;
    }

    /**
     * @deprecated use getIdAsignaturaVo()
     */
    public function setId_asignatura(int $id_asignatura): void
    {
        $this->id_asignatura = new AsignaturaId((int) $id_asignatura);
    }

    public function setIdAsignaturaVo(AsignaturaId|int|null $oIdAsignatura): void
    {
        if ($oIdAsignatura instanceof AsignaturaId) {
            $this->id_asignatura = $oIdAsignatura;
        } elseif ($oIdAsignatura !== null) {
            $this->id_asignatura = new AsignaturaId($oIdAsignatura);
        }
    }

    /**
     * @deprecated use getIdSituacionVo()
     */
    public function getId_situacion(): int
    {
        return $this->id_situacion->value();
    }

    public function getIdSituacionVo(): NotaSituacion
    {
        return $this->id_situacion;
    }

    /**
     * @deprecated use setIdSituacionVo()
     */
    public function setId_situacion(int $id_situacion): void
    {
        $this->id_situacion = new NotaSituacion((int) $id_situacion);
    }

    public function setIdSituacionVo(NotaSituacion|int|null $oIdSituacion): void
    {
        if ($oIdSituacion instanceof NotaSituacion) {
            $this->id_situacion = $oIdSituacion;
        } elseif ($oIdSituacion !== null) {
            $this->id_situacion = new NotaSituacion($oIdSituacion);
        }
    }

    /**
     * @deprecated use getActaVo()
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
     * @deprecated use setActaVo()
     */
    public function setActa(?string $acta): void
    {
        $this->acta = ActaNumero::fromNullableString($acta);
    }

    public function setActaVo(ActaNumero|string|null $texto = null): void
    {
        $this->acta = $texto instanceof ActaNumero
            ? $texto
            : ActaNumero::fromNullableString($texto);
    }

    public function getF_acta(): DateTimeLocal|null
    {
        return $this->f_acta;
    }

    public function setF_acta(DateTimeLocal|null $f_acta = null): void
    {
        $this->f_acta = $f_acta;
    }


    public function getDetalleVo(): ?Detalle
    {
        return $this->detalle;
    }


    public function setDetalleVo(Detalle|string|null $oDetalle): void
    {
        $this->detalle = $oDetalle instanceof Detalle
            ? $oDetalle
            : Detalle::fromNullableString($oDetalle);
    }

    /**
     * @deprecated use getDetalleVo()
     */
    public function getDetalle(): ?string
    {
        return $this->detalle?->value();
    }

    /**
     * @deprecated use setDetalleVo()
     */
    public function setDetalle(?string $detalle = null): void
    {
        $this->detalle = Detalle::fromNullableString($detalle);
    }

    public function isPreceptor(): bool { return (bool) ($this->preceptor ?? false); }

    public function setPreceptor(bool $preceptor): void
    {
        $this->preceptor = $preceptor;
    }

    public function getId_preceptor(): ?int
    {
        return $this->id_preceptor;
    }

    public function setId_preceptor(?int $id_preceptor): void
    {
        $this->id_preceptor = $id_preceptor;
    }


    public function getEpocaVo(): ?NotaEpoca
    {
        return $this->epoca;
    }


    public function setEpocaVo(NotaEpoca|int|null $oEpoca): void
    {
        $this->epoca = $oEpoca instanceof NotaEpoca
            ? $oEpoca
            : NotaEpoca::fromNullableInt($oEpoca);
    }

    /**
     * @deprecated use getEpocaVo()
     */
    public function getEpoca(): ?string { $v = $this->epoca?->value(); return $v !== null ? (string) $v : null; }

    /**
     * @deprecated use setEpocaVo()
     */
    public function setEpoca(?int $epoca = null): void
    {
        $this->epoca = NotaEpoca::fromNullableInt($epoca);
    }

    /**
     * @deprecated use getIdActivVo()
     */
    public function getId_activ(): ?int
    {
        return $this->id_activ?->value();
    }

    public function getIdActivVo(): ?ActividadId
    {
        return $this->id_activ;
    }

    public function setId_activ(?int $id_activ): void
    {
        $this->id_activ = ActividadId::fromNullableInt($id_activ);
    }

    public function setIdActivVo(ActividadId|int|null $idActiv): void
    {
        if ($idActiv instanceof ActividadId) {
            $this->id_activ = $idActiv;
        } elseif ($idActiv !== null) {
            $this->id_activ = new ActividadId($idActiv);
        }
    }

    public function getNotaNumVo(): ?NotaNum
    {
        return $this->nota_num;
    }


    public function setNotaNumVo(NotaNum|float|null $oNota_num): void
    {
        $this->nota_num = $oNota_num instanceof NotaNum
            ? $oNota_num
            : NotaNum::fromNullableFloat($oNota_num);
    }

    /**
     * @deprecated use getNotaNumVo()
     */
    public function getNota_num(): ?string
    {
        $v = $this->nota_num?->value();
        return $v !== null ? (string) $v : null;
    }

    /**
     * @deprecated use setNotaNumVo()
     */
    public function setNota_num(?float $nota_num = null): void
    {
        $this->nota_num = NotaNum::fromNullableFloat($nota_num);
    }


    public function getNotaMaxVo(): ?NotaMax
    {
        return $this->nota_max;
    }


    public function setNotaMaxVo(NotaMax|int|null $oNota_max): void
    {
        $this->nota_max = $oNota_max instanceof NotaMax
            ? $oNota_max
            : NotaMax::fromNullableInt($oNota_max);
    }

    /**
     * @deprecated use getNotaMaxVo()
     */
    public function getNota_max(): ?string
    {
        $v = $this->nota_max?->value();
        return $v !== null ? (string) $v : null;
    }

    /**
     * @deprecated use setNotaMaxVo()
     */
    public function setNota_max(?int $nota_max = null): void
    {
        $this->nota_max = NotaMax::fromNullableInt($nota_max);
    }


    public function getTipoActaVo(): ?TipoActa
    {
        return $this->tipo_acta;
    }


    public function setTipoActaVo(TipoActa|int|null $oTipo_acta): void
    {
        $this->tipo_acta = $oTipo_acta instanceof TipoActa
            ? $oTipo_acta
            : TipoActa::fromNullableInt($oTipo_acta);
    }

    /**
     * @deprecated use getTipoActaVo()
     */
    public function getTipo_acta(): ?int
    {
        return $this->tipo_acta?->value();
    }

    /**
     * @deprecated use setTipoActaVo()
     */
    public function setTipo_acta(?int $tipo_acta = null): void
    {
        $this->tipo_acta = TipoActa::fromNullableInt($tipo_acta);
    }

}