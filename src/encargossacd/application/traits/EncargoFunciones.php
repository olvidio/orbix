<?php

namespace src\encargossacd\application\traits;

use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\services\EncargoDominioService;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Facade de delegación hacia EncargoDominioService y EncargoAplicacionService.
 *
 * @method int|string calcular_dia(string $mas_menos, int|string $dia_ref, int|string $dia_inc)
 * @method int|false dedicacion_horas(int $id_nom, int $id_enc)
 * @method string texto_horario(string $mas_menos, int|string $dia_ref, int|string $dia_inc, int|string $dia_num, string $h_ini, string $h_fin, int|string $n_sacd = '')
 * @method string texto_horario_ex(int|string $mes, string $f_ini, string $f_fin, string $horario, string $mas_menos, int|string $dia_ref, int|string $dia_inc, int|string $dia_num, string $h_ini, string $h_fin, int|string $n_sacd)
 * @method string db_txt_h_sacd(int $id_enc, int $id_nom)
 * @method array<string, string>|string getArrayTraducciones(string $idioma)
 * @method string getTraduccion(string $clave, string $idioma)
 * @method string getLugar_dl()
 * @method DateTimeLocal getF_ini()
 * @method DateTimeLocal getF_fin()
 * @method array<string, string> getArraySeccion()
 * @method string getTxtDedicacion(iterable<\src\encargossacd\domain\entity\EncargoHorario|\src\encargossacd\domain\entity\EncargoSacdHorario> $cEncargoHorarios, string $idioma = '')
 * @method string|false dedicacion_ctr(int $id_ubi, int $id_enc, string $idioma = '')
 * @method string|false dedicacion(int $id_nom, int $id_enc, string $idioma = '')
 * @method void insert_horario_ctr(int $id_enc, string $modulo, mixed $dedicacion, int $n_sacd)
 * @method void modificar_horario_ctr(int $id_enc, string $modulo, mixed $dedicacion, int $n_sacd)
 * @method void insert_horario_sacd(int $id_item_t_sacd, int $id_enc, int $id_nom, string $modulo, mixed $dedicacion)
 * @method void finalizar_horario_sacd(int $id_enc, int $id_nom, DateTimeLocal $f_fin)
 * @method void modificar_horario_sacd(int $id_item_t_sacd, int $id_enc, int $id_nom, string $modulo, mixed $dedicacion)
 * @method EncargoSacd|null insert_sacd(int $id_enc, int $id_sacd, int $modo)
 * @method void finalizar_sacd(int $id_enc, int $id_sacd, int $modo, DateTimeLocal $f_fin)
 * @method void delete_sacd(int $id_enc, int $id_sacd, int $modo)
 * @method int crear_encargo(int $id_tipo_enc, int $sf_sv, int $id_ubi, int $id_zona, string $desc_enc, string $idioma_enc, string $desc_lugar, string $observ)
 * @method void grabar_alumnos(int $id_ubi, int $num_alum)
 */
class EncargoFunciones
{
    public function __construct(
        private EncargoDominioService $dominioService,
        private EncargoAplicacionService $aplicacionService,
    ) {
    }

    /**
     * @param array<int, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this->dominioService, $name)) {
            return $this->dominioService->$name(...$arguments);
        }
        if (method_exists($this->aplicacionService, $name)) {
            return $this->aplicacionService->$name(...$arguments);
        }
        throw new \BadMethodCallException("Método $name no encontrado en los servicios de Encargo.");
    }
}
