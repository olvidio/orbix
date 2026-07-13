---
id: "actividadtarifas.tipo_tarifa_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php"
entrada: ["post.id_tarifa:integer"]
entrada_obligatoria: ["id_tarifa"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borrar", "no se encuentra la tarifa", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaEliminar"]
tags: ["actividadtarifas", "tipo", "tarifa", "eliminar"]
estado_revision: "revisado"
---

# Tipo Tarifa Eliminar

Elimina un `TipoTarifa` del catálogo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra el tipo de tarifa indicado por `id_tarifa` (entero > 0). Solo disponible desde el formulario
de edición.

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tarifa` | `integer` | application | Si | Debe ser `> 0` |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina el `TipoTarifa` y sus dependencias según reglas del repositorio.

## Errores conocidos

- `no sé cuál he de borrar`
- `no se encuentra la tarifa`
- `hay un error, no se ha borrado`

## Permisos

- Sin control propio; botón eliminar solo en formulario de edición con permiso `adl` en listado.

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/view/tarifa_form.phtml`: `fnjs_guardar(..., 'eliminar')` en `tarifa.phtml`.
