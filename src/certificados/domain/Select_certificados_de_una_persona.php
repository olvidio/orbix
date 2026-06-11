<?php

namespace src\certificados\domain;

use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\is_true;

/**
 * Listado de certificados recibidos de una persona (dossier 1010 / código certificados_de_una_persona).
 *
 * Render: {@see \frontend\certificados\helpers\SelectCertificadosDeUnaPersonaRender}.
 *
 * @package    orbix
 * @subpackage certificados
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select_certificados_de_una_persona
{
    public function __construct(
        private readonly CertificadoRecibidoRepositoryInterface $certificadoRecibidoRepository,
    ) {
    }

    /** @var array<int|string, mixed> */
    private array $a_valores = [];
    private string $bloque = '';
    private string $queSel = '';
    private int $id_dossier = 0;
    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 0;
    private int|string|null $Qid_sel = null;
    private int|string|null $Qscroll_id = null;
    private mixed $status = null;
    private int $stackActual = 0;

    /**
     * @return list<array{txt: string, click: string}>
     */
    private function getBotones(): array
    {
        $a_botones[] = ['txt' => _("descargar pdf"), 'click' => "fnjs_descargar_pdf(\"#seleccionados\")"];
        $a_botones[] = array('txt' => _("modificar certificado"), 'click' => "fnjs_upload_certificado(\"#seleccionados\")");
        $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar_certificado(\"#seleccionados\")");
        return $a_botones;
    }

    /**
     * @return list<string>
     */
    private function getCabeceras(): array
    {
        return [
            _("certificado"),
            _("fecha certificado"),
            _("firmado digitalmente"),
            _("adjunto"),
            _("recibido"),
        ];
    }

    /**
     * @return array<int|string, mixed>
     */
    private function getValores(): array
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla(): void
    {
        $oPersona = Persona::findPersonaEnGlobal($this->id_pau);
        if (!is_object($oPersona)) {
            throw new \RuntimeException(
                "<br>No encuentro a ninguna persona con id_nom: $this->id_pau en  " . __FILE__ . ': line ' . __LINE__
            );
        }
        $aWhere = [
            'id_nom' => $this->id_pau,
            //'firmado' => true,           mejor que se vean todos
            '_ordre' => 'f_certificado'
        ];

        $cCertificados = $this->certificadoRecibidoRepository->getCertificados($aWhere);

        $i = 0;
        $a_valores = [];
        foreach ($cCertificados as $oCertificado) {
            $i++;

            $id_item = $oCertificado->getId_item();
            $certificado = $oCertificado->getCertificado();
            $firmado = $oCertificado->isFirmado();
            $f_certificado = $oCertificado->getF_certificado()?->getFromLocal() ?? '';
            $f_recibido = $oCertificado->getF_recibido()?->getFromLocal() ?? '';
            $pdf = $oCertificado->getDocumento();

            $a_valores[$i]['sel'] = $id_item;
            $a_valores[$i][1] = $certificado;
            $a_valores[$i][2] = $f_certificado;
            $a_valores[$i][3] = is_true($firmado) ? _("Sí") : _("No");
            $a_valores[$i][4] = empty($pdf) ? '' : _("Sí");
            $a_valores[$i][5] = $f_recibido;

        }
        if (!empty($a_valores)) {
            // Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
            // las pongo al final, porque al contar los valores del array se despista.
            if (!empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (!empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }

        $this->a_valores = $a_valores;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSegmentData(): array
    {
        $stack = $this->stackActual;

        return [
            'segment_tipo' => 'select_certificados_de_una_persona',
            'hash_main' => [
                'campos_no' => 'sel!mod!scroll_id!refresh!id_sel',
                'campos_hidden' => [
                    'pau' => $this->pau,
                    'id_pau' => $this->id_pau,
                    'obj_pau' => $this->obj_pau,
                    'queSel' => $this->queSel,
                    'id_dossier' => $this->id_dossier,
                    'permiso' => 1,
                    'stack' => $stack,
                ],
            ],
            'tabla' => [
                'id_tabla' => 'select_certificados_de_una_persona',
                'cabeceras' => $this->getCabeceras(),
                'botones' => $this->getBotones(),
                'valores' => $this->getValores(),
            ],
            'paths' => [
                'certificado_recibido_delete' => 'src/certificados/certificado_recibido_delete',
                'bloque' => $this->bloque,
            ],
            'url_nuevo_spec' => [
                'path' => 'frontend/certificados/controller/certificado_recibido_adjuntar.php',
                'query' => ['nuevo' => 1, 'id_nom' => $this->id_pau],
            ],
        ];
    }

    public function getId_dossier(): int
    {
        return $this->id_dossier;
    }

    public function getPau(): string
    {
        return $this->pau;
    }

    public function getObj_pau(): string
    {
        return $this->obj_pau;
    }

    public function getId_pau(): int
    {
        return $this->id_pau;
    }

    public function getPermiso(): int
    {
        return $this->permiso;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function setId_dossier(int|string $Qid_dossier): void
    {
        $this->id_dossier = (int) $Qid_dossier;
    }

    public function setPau(string $Qpau): void
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau(string $Qobj_pau): void
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau(int|string $Qid_pau): void
    {
        $this->id_pau = (int) $Qid_pau;
    }

    public function setPermiso(int|string $Qpermiso): void
    {
        $this->permiso = (int) $Qpermiso;
    }

    public function setStatus(mixed $Qstatus): void
    {
        $this->status = $Qstatus;
    }

    public function setQid_sel(int|string|null $Qid_sel): void
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id(int|string|null $Qscroll_id): void
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque(string $bloque): void
    {
        $this->bloque = $bloque;
    }

    public function setQueSel(string $queSel): void
    {
        $this->queSel = $queSel;
    }

    public function setStackActual(int $stack): void
    {
        $this->stackActual = $stack;
    }

}
