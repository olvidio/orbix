---
id: "casas.casa_ingreso_eliminar"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingreso_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingreso_eliminar.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borar", "Ingreso no encontrado", "Hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/casas/controller/casa.php", "frontend/casas/controller/casa_ingreso_form.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoEliminar"]
tags: ["casas", "casa", "ingreso", "eliminar"]
estado_revision: "revisado"
---

# Casa Ingreso Eliminar

Elimina el `Ingreso` asociado a una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `que=eliminar` de `apps/casas/controller/casa_ajax.php`. Borra el registro de
ingreso de la actividad indicada (no modifica la actividad en sí).

## Endpoint

- URL: `/src/casas/casa_ingreso_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Sí | |

## Salida

- Helper: `ContestarJson::enviar($mensaje, $data)`.
- Éxito: `success: true`, `data: ""`.
- Error: mensaje en `data`.

## Errores conocidos

- `no sé cuál he de borar` (texto legacy conservado)
- `Ingreso no encontrado`
- `Hay un error, no se ha eliminado`

## Permisos

- Sin control propio; autorización en frontend + permisos de actividad (`economic`).

## Casos De Uso

- `src\casas\application\CasaIngresoEliminar`

## Frontend Relacionado

- `frontend/casas/controller/casa_ingreso_form.php`: acción de eliminar desde el modal.
