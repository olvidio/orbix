---
id: "ubiscamas.habitacion_delete"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/habitacion_delete"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/habitacion_delete.php"
entrada: ["post.sel:array", "post.id_habitacion:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/view/habitacion_form.phtml", "frontend/ubiscamas/view/select_habitaciones_cdc.phtml"]
casos_uso: []
tags: ["ubiscamas", "habitacion", "delete"]
estado_revision: "revisado"
errores: ["No se encontró la habitación a eliminar", "hay un error, no se ha eliminado la habitación", "Error al eliminar la habitación"]
---

# Habitacion Delete

Elimina una habitación CDC por `id_habitacion` o token `sel` (`id_habitacion#...`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina una habitación CDC por `id_habitacion` o token `sel` (`id_habitacion#...`).

## Endpoint

- URL: `/src/ubiscamas/habitacion_delete`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_delete.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_habitacion#...` o `id_cama#...` según endpoint |
| `id_habitacion` | `string` | application | No |  |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado en mutaciones).

## Errores conocidos
- `No se encontró la habitación a eliminar`
- `hay un error, no se ha eliminado la habitación`
- `Error al eliminar la habitación`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION['oPerm']` y permisos del dossier/actividad padre.

## Casos De Uso

- Lógica inline en el controller (sin caso de uso en `application/`).

## Frontend Relacionado

- `frontend/ubiscamas/view/habitacion_form.phtml`
- `frontend/ubiscamas/view/select_habitaciones_cdc.phtml`
