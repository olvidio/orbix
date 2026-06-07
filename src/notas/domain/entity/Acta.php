<?php

namespace src\notas\domain\entity;

use src\shared\infrastructure\DependencyResolver;


use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Libro;
use src\notas\domain\value_objects\Linea;
use src\notas\domain\value_objects\Lugar;
use src\notas\domain\value_objects\Observ;
use src\notas\domain\value_objects\Pagina;
use src\notas\domain\value_objects\Pdf;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;


class Acta
{

    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private ActaNumero $acta;

    private ?AsignaturaId $id_asignatura = null;

    private ?int $id_activ = null;

    private ?DateTimeLocal $f_acta = null;

    private ?Libro $libro = null;

    private ?Pagina $pagina = null;

    private ?Linea $linea = null;

    private ?Lugar $lugar = null;

    private ?Observ $observ = null;

    private ?Pdf $pdf = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getActaVo(): ActaNumero
    {
        return $this->acta;
    }


    public function setActaVo(ActaNumero|string|null $oActaNumero): void
    {
        if ($oActaNumero instanceof ActaNumero) {
            $this->acta = $oActaNumero;
        } elseif ($oActaNumero !== null && $oActaNumero !== '') {
            $this->acta = new ActaNumero($oActaNumero);
        }
    }

    /**
     * @deprecated use getActaVo()
     */
    public function getActa(): string
    {
        return $this->acta->value();
    }

    /**
     * @deprecated use setActaVo()
     */
    public function setActa(string $acta): void
    {
        $this->acta = new ActaNumero($acta);
    }


    /**
     * @deprecated use getIdAsignaturaVo()
     */
    public function getId_asignatura(): ?int
    {
        return $this->id_asignatura?->value();
    }

    public function getIdAsignaturaVo(): ?AsignaturaId
    {
        return $this->id_asignatura;
    }

    public function setId_asignatura(?int $id_asignatura = null): void
    {
        $this->id_asignatura = new AsignaturaId((int) $id_asignatura);
    }

    public function setIdAsignaturaVo(AsignaturaId|int|null $id_asignatura): void
    {
        if ($id_asignatura instanceof AsignaturaId) {
            $this->id_asignatura = $id_asignatura;
        } elseif ($id_asignatura !== null) {
            $this->id_asignatura = new AsignaturaId($id_asignatura);
        } else {
            $this->id_asignatura = null;
        }
    }


    public function getId_activ(): ?int
    {
        return $this->id_activ;
    }


    public function setId_activ(?int $id_activ = null): void
    {
        $this->id_activ = $id_activ;
    }


    public function getF_acta(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_acta ?? new NullDateTimeLocal;
    }


    public function setF_acta(DateTimeLocal|null $f_acta = null): void
    {
        $this->f_acta = $f_acta;
    }


    public function getLibroVo(): ?Libro
    {
        return $this->libro;
    }


    public function setLibroVo(Libro|int|null $valor = null): void
    {
        $this->libro = $valor instanceof Libro
            ? $valor
            : Libro::fromNullableInt($valor);
    }

    /**
     * @deprecated use getLibroVo()
     */
    public function getLibro(): ?string
    {
        $v = $this->libro?->value();
        return $v !== null ? (string) $v : null;
    }

    /**
     * @deprecated use setLibroVo()
     */
    public function setLibro(?int $libro = null): void
    {
        $this->libro = Libro::fromNullableInt($libro);
    }


    public function getPaginaVo(): ?Pagina
    {
        return $this->pagina;
    }

    public function setPaginaVo(Pagina|int|null $valor = null): void
    {
        $this->pagina = $valor instanceof Pagina
            ? $valor
            : Pagina::fromNullableInt($valor);
    }

    /**
     * @deprecated use getPaginaVo()
     */
    public function getPagina(): ?string
    {
        $v = $this->pagina?->value();
        return $v !== null ? (string) $v : null;
    }

    /**
     * @deprecated use setPaginaVo()
     */
    public function setPagina(?int $pagina = null): void
    {
        $this->pagina = Pagina::fromNullableInt($pagina);
    }


    public function getLineaVo(): ?Linea
    {
        return $this->linea;
    }


    public function setLineaVo(Linea|int|null $valor = null): void
    {
        $this->linea = $valor instanceof Linea
            ? $valor
            : Linea::fromNullableInt($valor);
    }

    /**
     * @deprecated use getLineaVo()
     */
    public function getLinea(): ?string
    {
        $v = $this->linea?->value();
        return $v !== null ? (string) $v : null;
    }

    /**
     * @deprecated use setLineaVo()
     */
    public function setLinea(?int $linea = null): void
    {
        $this->linea = Linea::fromNullableInt($linea);
    }


    public function getLugarVo(): ?Lugar
    {
        return $this->lugar;
    }


    public function setLugarVo(Lugar|string|null $texto = null): void
    {
        $this->lugar = $texto instanceof Lugar
            ? $texto
            : Lugar::fromNullableString($texto);
    }

    /**
     * @deprecated use getLugarVo()
     */
    public function getLugar(): ?string
    {
        return $this->lugar?->value();
    }

    /**
     * @deprecated use setLugarVo()
     */
    public function setLugar(?string $lugar = null): void
    {
        $this->lugar = Lugar::fromNullableString($lugar);
    }


    public function getObservVo(): ?Observ
    {
        return $this->observ;
    }


    public function setObservVo(Observ|string|null $texto = null): void
    {
        $this->observ = $texto instanceof Observ
            ? $texto
            : Observ::fromNullableString($texto);
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = Observ::fromNullableString($observ);
    }


    public function getPdfVo(): ?Pdf
    {
        return $this->pdf;
    }


    public function setPdfVo(Pdf|string|null $texto = null): void
    {
        $this->pdf = $texto instanceof Pdf
            ? $texto
            : Pdf::fromNullableString($texto);
    }

    /**
     * @deprecated use getPdfVo()
     */
    public function getPdf(): ?string
    {
        return $this->pdf?->value();
    }

    /**
     * @deprecated use setPdfVo()
     */
    public function setPdf(?string $pdf = null): void
    {
        $this->pdf = Pdf::fromNullableString($pdf);
    }

    /**
     * inventa el valor del acta, si no es correcto
     *
     */
    public static function inventarActa(string $valor, DateTimeLocal|NullDateTimeLocal|string|null $fecha): string
    {
        $valor = trim($valor);
        // comprobar si hace falta, o ya está bien el acta como está
        $reg_exp = "/^(\?|\w+\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
        if (!preg_match($reg_exp, $valor)) {
            // inventar acta.
            // Se puede usar la función desde personaNota, por eso se puede pasar la fecha.
            if ($fecha === null || $fecha === '' || $fecha instanceof NullDateTimeLocal) {
                $any = '?';
                $num_acta = 'x';
            } else {
                if ($fecha instanceof DateTimeLocal) {
                    $oData = $fecha;
                } else {
                    $oData = DateTimeLocal::createFromLocal($fecha);
                }
                $any = $oData->format('y');
                // inventar acta.
                $ActaRepository = DependencyResolver::get(ActaRepositoryInterface::class);
                $num_acta = 1 + $ActaRepository->getUltimaActa($any, $valor);
            }
            // no sé nada
            if ($valor === '?') {
                // 'dl? xx/15?';
                $valor = "dl? $num_acta/$any?";
            } else {  // solo la región o dl
                // 'region xx/15?';
                $valor = "$valor $num_acta/$any?";
            }
        }
        return $valor;
    }
}