---
id: "actividadtarifas.relacion_tarifa_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borrar", "no se encuentra la relación", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaEliminar"]
tags: ["actividadtarifas", "relacion", "tarifa", "eliminar"]
estado_revision: "revisado"
---

# Relacion Tarifa Eliminar

Elimina una `RelacionTarifaTipoActividad`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la relación tarifa–tipo de actividad indicada por `id_item`.

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | Si | Debe ser `> 0` |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina la `RelacionTarifaTipoActividad`.

## Errores conocidos

- `no sé cuál he de borrar`
- `no se encuentra la relación`
- `hay un error, no se ha borrado` (puede incluir detalle del repositorio)

## Permisos

- Sin control propio; acción desde formulario de edición con permiso `adl` en listado.

## Casos De Uso

- `src\actividadtarifas\application\RelacionTarifaEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/view/tarifa_tipo_actividad.phtml`: `fnjs_guardar(..., 'eliminar')`.
