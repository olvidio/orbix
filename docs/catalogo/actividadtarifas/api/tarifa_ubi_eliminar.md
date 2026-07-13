---
id: "actividadtarifas.tarifa_ubi_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php"
entrada: ["post.ctx_eliminar:string"]
entrada_obligatoria: ["ctx_eliminar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
errores: ["Operación no autorizada", "no sé cuál he de borrar", "no se encuentra la tarifa", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiEliminar"]
tags: ["actividadtarifas", "tarifa", "ubi", "eliminar"]
estado_revision: "revisado"
---

# Tarifa Ubi Eliminar

Elimina una `TarifaUbi` de una casa y año.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra el registro identificado por `id_item` dentro de la cápsula `ctx_eliminar`. Solo disponible
en edición (el formulario incluye el hidden `ctx_eliminar` cuando `es_nuevo=false`).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_eliminar` | `string` | controller | Si | Cápsula `HashB` con `{id_item}` emitida como `token_eliminar` en `tarifa_ubi_form_data` |

El `id_item` del POST no lo usa el controller: lo extrae de `ctx_eliminar`.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina la `TarifaUbi` indicada.

## Errores conocidos

- `Operación no autorizada`
- `no sé cuál he de borrar`
- `no se encuentra la tarifa`
- `hay un error, no se ha borrado` (puede incluir detalle del repositorio)

## Permisos

- Autorización vía cápsula `HashB` (`ctx_eliminar`). La acción de eliminar solo se ofrece en el
  formulario de edición con permiso `adl` en el listado.

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/view/tarifa_ubi_form.phtml`: botón eliminar → `fnjs_guardar(..., 'eliminar')`.
- `frontend/actividadtarifas/view/tarifa_ubi.phtml`: confirma con `TXT_ELIMINAR` y POST a `url_eliminar`.
