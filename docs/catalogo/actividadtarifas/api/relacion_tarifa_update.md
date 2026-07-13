---
id: "actividadtarifas.relacion_tarifa_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_update.php"
entrada: ["post.id_item:string", "post.id_tarifa:integer", "post.id_tipo_activ:integer"]
entrada_obligatoria: ["id_tarifa", "id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe indicar la tarifa", "debe indicar el tipo de actividad", "no se encuentra la relación", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaUpdate"]
tags: ["actividadtarifas", "relacion", "tarifa", "update"]
estado_revision: "revisado"
---

# Relacion Tarifa Update

Crea o actualiza una `RelacionTarifaTipoActividad` (qué tarifa aplica a cada tipo de actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta con `id_item` `nuevo`/vacío; edición por id numérico. Fija siempre `id_serie = SerieId::GENERAL`.
Exige `id_tarifa` e `id_tipo_activ` > 0.

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | application | No | `nuevo`/vacío = alta |
| `id_tarifa` | `integer` | application | Si | Tipo de tarifa del catálogo |
| `id_tipo_activ` | `integer` | application | Si | Tipo de actividad |

En alta el formulario nuevo también envía campos del bloque `actividad_que_datos`
(`iactividad_val`, `iasistentes_val`, etc.) firmados con `HashFront`; el caso de uso solo lee los tres anteriores.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `debe indicar la tarifa`
- `debe indicar el tipo de actividad`
- `no se encuentra la relación`
- `hay un error, no se ha guardado` (puede incluir detalle del repositorio)

## Permisos

- Sin control propio; enlace modificar en listado con `have_perm_oficina('adl')` y sección del tipo
  de actividad coincidente con `mi_sfsv`.

## Casos De Uso

- `src\actividadtarifas\application\RelacionTarifaUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`: form firmado con `HashFront`
  (twig `tarifa_tipo_actividad_form*.html.twig`).
