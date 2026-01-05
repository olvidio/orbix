<?php

namespace src\notas\domain\entity;

use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Libro;
use src\notas\domain\value_objects\Pagina;
use src\notas\domain\value_objects\Linea;
use src\notas\domain\value_objects\Lugar;
use src\notas\domain\value_objects\Observ;
use src\notas\domain\value_objects\Pdf;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;


class Acta
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private string $acta;

    private int|null $id_asignatura = null;

    private int|null $id_activ = null;

    private DateTimeLocal $f_acta;

    private int|null $libro = null;

    private int|null $pagina = null;

    private int|null $linea = null;

    private string|null $lugar = null;

    private string|null $observ = null;

    private string|null $pdf = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getActaVo(): ActaNumero
    {
        return new ActaNumero($this->acta);
    }


    public function setActaVo(ActaNumero $oActaNumero): void
    {
        $this->acta = $oActaNumero->value();
    }

    /**
     * @deprecated use getActaVo()
     */
    public function getActa(): string
    {
        return $this->acta;
    }

    /**
     * @deprecated use setActaVo()
     */
    public function setActa(string $acta): void
    {
        $this->acta = $acta;
    }


    public function getId_asignatura(): ?int
    {
        return $this->id_asignatura;
    }


    public function setId_asignatura(?int $id_asignatura = null): void
    {
        $this->id_asignatura = $id_asignatura;
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
        return Libro::fromNullable($this->libro);
    }


    public function setLibroVo(?Libro $oLibro): void
    {
        $this->libro = $oLibro?->value();
    }

    /**
     * @deprecated use getLibroVo()
     */
    public function getLibro(): ?int
    {
        return $this->libro;
    }

    /**
     * @deprecated use setLibroVo()
     */
    public function setLibro(?int $libro = null): void
    {
        $this->libro = $libro;
    }


    public function getPaginaVo(): ?Pagina
    {
        return Pagina::fromNullable($this->pagina);
    }


    public function setPaginaVo(?Pagina $oPagina): void
    {
        $this->pagina = $oPagina?->value();
    }

    /**
     * @deprecated use getPaginaVo()
     */
    public function getPagina(): ?int
    {
        return $this->pagina;
    }

    /**
     * @deprecated use setPaginaVo()
     */
    public function setPagina(?int $pagina = null): void
    {
        $this->pagina = $pagina;
    }


    public function getLineaVo(): ?Linea
    {
        return Linea::fromNullable($this->linea);
    }


    public function setLineaVo(?Linea $oLinea): void
    {
        $this->linea = $oLinea?->value();
    }

    /**
     * @deprecated use getLineaVo()
     */
    public function getLinea(): ?int
    {
        return $this->linea;
    }

    /**
     * @deprecated use setLineaVo()
     */
    public function setLinea(?int $linea = null): void
    {
        $this->linea = $linea;
    }


    public function getLugarVo(): ?Lugar
    {
        return Lugar::fromNullable($this->lugar);
    }


    public function setLugarVo(?Lugar $oLugar): void
    {
        $this->lugar = $oLugar?->value();
    }

    /**
     * @deprecated use getLugarVo()
     */
    public function getLugar(): ?string
    {
        return $this->lugar;
    }

    /**
     * @deprecated use setLugarVo()
     */
    public function setLugar(?string $lugar = null): void
    {
        $this->lugar = $lugar;
    }


    public function getObservVo(): ?Observ
    {
        return Observ::fromNullable($this->observ);
    }


    public function setObservVo(?Observ $oObserv): void
    {
        $this->observ = $oObserv?->value();
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }


    public function getPdfVo(): ?Pdf
    {
        return Pdf::fromNullable($this->pdf);
    }


    public function setPdfVo(?Pdf $oPdf): void
    {
        $this->pdf = $oPdf?->value();
    }

    /**
     * @deprecated use getPdfVo()
     */
    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    /**
     * @deprecated use setPdfVo()
     */
    public function setPdf(?string $pdf = null): void
    {
        $this->pdf = $pdf;
    }

    public function hasEmptyPdf(): bool
    {
        $this->DBCarregarConPdf();
        return empty($this->pdf);
    }


   /**
     * inventa el valor del acta, si no es correcto
     *
     */
    public static function inventarActa(string $valor, DateTimeLocal|string $fecha): string
    {
        $valor = trim($valor);
        // comprobar si hace falta, o ya está bien el acta como está
        $reg_exp = "/^(\?|\w+\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
        if (!preg_match($reg_exp, $valor)) {
            // inventar acta.
            // Se puede usar la función desde personaNota, por eso se puede pasar la fecha.
            if (empty($fecha)) {
                $any = '?';
                $num_acta = 'x';
            } else {
                if (is_object($fecha)) {
                    $oData = $fecha;
                } else {
                    $oData = DateTimeLocal::createFromLocal($fecha);
                }
                $any = $oData->format('y');
                // inventar acta.
                $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
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