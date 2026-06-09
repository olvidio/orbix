---
id: "actividadtarifas.relacion_tarifa_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borrar", "no se encuentra la relación", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaEliminar"]
tags: ["actividadtarifas", "relacion", "tarifa", "eliminar"]
estado_revision: "generado"
---

# Relacion Tarifa Eliminar

Endpoint backend: elimina una `RelacionTarifaTipoActividad`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Mutacion: elimina una `RelacionTarifaTipoActividad`.

## Errores conocidos

- `no sé cuál he de borrar`
- `no se encuentra la relación`
- `hay un error, no se ha borrado`

## Casos De Uso

- `src\actividadtarifas\application\RelacionTarifaEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.