<?php

namespace src\cambios\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\value_objects\ObjetoNombre;
use src\cambios\domain\value_objects\PropiedadNombre;
use src\cambios\domain\value_objects\TipoCambioId;
use src\shared\config\ConfigGlobal;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\value_objects\DelegacionCode;
use stdClass;

/**
 * Clase que implementa la entidad av_cambios_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
class Cambio
{
    use Hydratable;

    //  tipo cambio constants.
    public const TIPO_CMB_INSERT = 1;
    public const TIPO_CMB_UPDATE = 2;
    public const TIPO_CMB_DELETE = 3;
    public const TIPO_CMB_FASE = 4;

    /**
     * Posa en marxa un procés per generar la taula d'avisos per cada usuari.
     */
    public function generarTabla(): void
    {
        $program = ConfigGlobal::$directorio . '/src/cambios/infrastructure/cli/avisos_generar_tabla.php';
        $username = ConfigGlobal::mi_usuario();
        $pwd = ConfigGlobal::mi_pass();
        $err = ConfigGlobal::$directorio . '/log/avisos.err';
        $out = ConfigGlobal::$directorio . '/log/avisos.out';
        /* Hay que pasarle los argumentos que no tienen si se le llama por command line:
         $username;
         $password;
         $dir_web = orbix | pruebas;
         document_root = /home/dani/orbix_local
         $ubicacion = 'sv';
         $esquema_web = 'H-dlbv';
         $private = 'sf'; para el caso del servidor exterior en dlb. puerto distinto.
         $DB_SERVER = 1 o 2; para indicar el servidor dede el que se ejecuta. (ver comentario en clase: CambioAnotado)
         */
        $dirweb = isset($_SERVER['DIRWEB']) && is_string($_SERVER['DIRWEB']) ? $_SERVER['DIRWEB'] : '';
        $doc_root = isset($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
        $ubicacion = getenv('UBICACION');
        $esquema_web = getenv('ESQUEMA');
        $private = getenv('PRIVATE');
        $private = empty($private) ? 'x' : $private;
        $db_server = getenv('DB_SERVER');

        // Si he entrado escogiendo el esquema de un desplegable, no tengo el valor
        if (empty($esquema_web)) {
            $esquema_web = ConfigGlobal::mi_region_dl();
        }

        $command = "nohup /usr/bin/php $program $username $pwd $dirweb $doc_root $ubicacion $esquema_web $private $db_server >> $out 2>> $err < /dev/null &";
        exec($command);
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_schema;

    private int $id_item_cambio;

    private TipoCambioId $id_tipo_cambio;

    private int $id_activ;

    private ActividadTipoId $id_tipo_activ;

    /** @var array<string, mixed>|stdClass|null */
    private array|stdClass|null $json_fases_sv = null;

    /** @var array<string, mixed>|stdClass|null */
    private array|stdClass|null $json_fases_sf = null;

    private ?StatusId $id_status = null;

    private DelegacionCode|null $dl_org = null;

    private ObjetoNombre|null $objeto = null;

    private PropiedadNombre|null $propiedad = null;

    private ?string $valor_old = null;

    private ?string $valor_new = null;

    private ?int $quien_cambia = null;

    private ?int $sfsv_quien_cambia = null;

   private ?DateTimeLocal $timestamp_cambio = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_schema(): int
    {
        return $this->id_schema;
    }


    public function setId_schema(int $id_schema): void
    {
        $this->id_schema = $id_schema;
    }


    public function getId_item_cambio(): int
    {
        return $this->id_item_cambio;
    }


    public function setId_item_cambio(int $id_item_cambio): void
    {
        $this->id_item_cambio = $id_item_cambio;
    }


    public function getTipoCambioVo(): TipoCambioId
    {
        return $this->id_tipo_cambio;
    }


    public function setTipoCambioVo(TipoCambioId|int $valor): void
    {
        if ($valor instanceof TipoCambioId) {
            $this->id_tipo_cambio = $valor;
            return;
        }
        $vo = TipoCambioId::fromNullableInt($valor);
        if ($vo !== null) {
            $this->id_tipo_cambio = $vo;
        }
    }

    /**
     * @deprecated usar getTipoCambioVo()
     */
    public function getId_tipo_cambio(): int
    {
        return $this->id_tipo_cambio->value();
    }

    /**
     * @deprecated usar setTipoCambioVo()
     */
    public function setId_tipo_cambio(int $id_tipo_cambio): void
    {
        $vo = TipoCambioId::fromNullableInt($id_tipo_cambio);
        if ($vo !== null) {
            $this->id_tipo_cambio = $vo;
        }
    }


    public function getId_activ(): int
    {
        return $this->id_activ;
    }


    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     * @deprecated Usar `getIdTipoActivVo(): ActividadTipoId` en su lugar.
     */
    public function getId_tipo_activ(): int
    {
        return $this->id_tipo_activ->value();
    }

    public function getIdTipoActivVo(): ActividadTipoId
    {
        return $this->id_tipo_activ;
    }

    /**
     * @deprecated Usar `getIdTipoActivVo(): ActividadTipoId` en su lugar.
     */
    public function setId_tipo_activ(int $id_tipo_activ): void
    {
        $vo = ActividadTipoId::fromNullableInt($id_tipo_activ);
        if ($vo !== null) {
            $this->id_tipo_activ = $vo;
        }
    }

    public function setIdTipoActivVo(ActividadTipoId|int|null $value): void
    {
        if ($value instanceof ActividadTipoId) {
            $this->id_tipo_activ = $value;
            return;
        }
        $vo = ActividadTipoId::fromNullableInt($value);
        if ($vo !== null) {
            $this->id_tipo_activ = $vo;
        }
    }

    /**
     * @return array<string, mixed>|stdClass|null
     */
    public function getJson_fases_sv(): array|stdClass|null
    {
        return $this->json_fases_sv;
    }


    /**
     * @param array<string, mixed>|stdClass|null $json_fases_sv
     */
    public function setJson_fases_sv(stdClass|array|null $json_fases_sv = null): void
    {
        $this->json_fases_sv = $json_fases_sv;
    }


    /**
     * @return array<string, mixed>|stdClass|null
     */
    public function getJson_fases_sf(): array|stdClass|null
    {
        return $this->json_fases_sf;
    }


    /**
     * @param array<string, mixed>|stdClass|null $json_fases_sf
     */
    public function setJson_fases_sf(stdClass|array|null $json_fases_sf = null): void
    {
        $this->json_fases_sf = $json_fases_sf;
    }


    /**
     * @deprecated Usar `getIdStatusVo(): ?StatusId` en su lugar.
     */
    public function getId_status(): ?int
    {
        return $this->id_status?->value();
    }

    public function getIdStatusVo(): ?StatusId
    {
        return $this->id_status;
    }

    /**
     * @deprecated Usar `setIdStatusVo(?StatusId $vo): void` en su lugar.
     */
    public function setId_status(?int $id_status = null): void
    {
        $this->id_status = StatusId::fromNullableInt($id_status);
    }

    public function setIdStatusVo(StatusId|int|null $texto): void
    {
        $this->id_status = $texto instanceof StatusId
            ? $texto
            : StatusId::fromNullableInt($texto);
    }


    /**
     * @deprecated Usar `getDlOrgVo(): ?DelegacionCode` en su lugar.
     */
    public function getDl_org(): ?string
    {
        return $this->dl_org?->value();
    }


    /**
     * @deprecated Usar `setDlOrgVo(?DelegacionCode $vo): void` en su lugar.
     */
    public function setDl_org(?string $dl_org = null): void
    {
        $this->dl_org = DelegacionCode::fromNullableString($dl_org);
    }

    public function getDlOrgVo(): ?DelegacionCode
    {
        return $this->dl_org;
    }

    public function setDlOrgVo(DelegacionCode|string|null $texto): void
    {
        $this->dl_org = $texto instanceof DelegacionCode
            ? $texto
            : DelegacionCode::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getObjetoVo(): ?ObjetoNombre` en su lugar.
     */
    public function getObjeto(): ?string
    {
        return $this->objeto?->value();
    }


    /**
     * @deprecated Usar `setObjetoVo(?ObjetoNombre $vo): void` en su lugar.
     */
    public function setObjeto(?string $objeto = null): void
    {
        $this->objeto = ObjetoNombre::fromNullableString($objeto);
    }

    public function getObjetoVo(): ?ObjetoNombre
    {
        return $this->objeto;
    }

    public function setObjetoVo(ObjetoNombre|string|null $texto): void
    {
        $this->objeto = $texto instanceof ObjetoNombre
            ? $texto
            : ObjetoNombre::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getPropiedadVo(): ?PropiedadNombre` en su lugar.
     */
    public function getPropiedad(): ?string
    {
        return $this->propiedad?->value();
    }


    /**
     * @deprecated Usar `setPropiedadVo(?PropiedadNombre $vo): void` en su lugar.
     */
    public function setPropiedad(?string $propiedad = null): void
    {
        $this->propiedad = PropiedadNombre::fromNullableString($propiedad);
    }

    public function getPropiedadVo(): ?PropiedadNombre
    {
        return $this->propiedad;
    }

    public function setPropiedadVo(PropiedadNombre|string|null $texto): void
    {
        $this->propiedad = $texto instanceof PropiedadNombre
            ? $texto
            : PropiedadNombre::fromNullableString($texto);
    }


    public function getValor_old(): ?string
    {
        return $this->valor_old;
    }


    public function setValor_old(?string $valor_old = null): void
    {
        $this->valor_old = $valor_old;
    }


    public function getValor_new(): ?string
    {
        return $this->valor_new;
    }


    public function setValor_new(?string $valor_new = null): void
    {
        $this->valor_new = $valor_new;
    }


    public function getQuien_cambia(): ?int
    {
        return $this->quien_cambia;
    }


    public function setQuien_cambia(?int $quien_cambia = null): void
    {
        $this->quien_cambia = $quien_cambia;
    }


    public function getSfsv_quien_cambia(): ?int
    {
        return $this->sfsv_quien_cambia;
    }


    public function setSfsv_quien_cambia(?int $sfsv_quien_cambia = null): void
    {
        $this->sfsv_quien_cambia = $sfsv_quien_cambia;
    }


    public function getTimestamp_cambio(): DateTimeLocal|null
    {
        return $this->timestamp_cambio;
    }


    public function setTimestamp_cambio(DateTimeLocal|null $timestamp_cambio = null): void
    {
        $this->timestamp_cambio = $timestamp_cambio;
    }
}
