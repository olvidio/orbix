---
id: "actividadtarifas.relacion_tarifa_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_update.php"
entrada: ["post.id_item:string", "post.id_tarifa:integer", "post.id_tipo_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe indicar la tarifa", "debe indicar el tipo de actividad", "no se encuentra la relación", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php", "frontend/pasarela/controller/nombre_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaUpdate"]
tags: ["actividadtarifas", "relacion", "tarifa", "update"]
estado_revision: "generado"
---

# Relacion Tarifa Update

Endpoint backend: crea o actualiza una `RelacionTarifaTipoActividad`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller+application | No | controller+application |
| `id_tarifa` | `integer` | controller+application | No | controller+application |
| `id_tipo_activ` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `debe indicar la tarifa`
- `debe indicar el tipo de actividad`
- `no se encuentra la relación`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadtarifas\application\RelacionTarifaUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`
- `frontend/pasarela/controller/nombre_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.