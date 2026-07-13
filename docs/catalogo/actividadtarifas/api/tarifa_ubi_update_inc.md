---
id: "actividadtarifas.tarifa_ubi_update_inc"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_update_inc"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php"
entrada: ["post.inc_cantidad:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiUpdateInc"]
tags: ["actividadtarifas", "tarifa", "ubi", "update", "inc"]
estado_revision: "revisado"
---

# Tarifa Ubi Update Inc

Actualiza en lote las cantidades de varias `TarifaUbi` desde el estudio económico de casa.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recorre el array `inc_cantidad` cuyas claves tienen formato `{letra}#{id_item}` (solo usa la parte
tras `#` como `id_item`). Actualiza `cantidad` (entero redondeado) de cada registro existente.
Omite entradas con `id_item=0` o cantidad cero; si el registro no existe, continúa sin error.
Array vacío o ausente → éxito silencioso.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_update_inc`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `inc_cantidad` | `array` | controller+application | No | Mapa clave `{texto}#{id_item}` → cantidad numérica |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"` (también si no hay filas que procesar).
- Error parcial: `success: false`, `mensaje` con errores concatenados por `\n`.

## Errores conocidos

- `hay un error, no se ha guardado` (puede repetirse y añadir detalle del repositorio)

## Permisos

- Sin control propio en el caso de uso; invocado desde el estudio económico de casa
  (`calendario_ubi_resumen`); autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiUpdateInc`

## Frontend Relacionado

- `frontend/casas/controller/calendario_ubi_resumen.php`: envía el array `inc_cantidad` al guardar
  importes del estudio económico.
