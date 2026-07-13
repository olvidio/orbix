---
id: "ubiscamas.cama_update"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/cama_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/cama_update.php"
entrada: ["post.sel:array", "post.id_cama:string", "post.id_habitacion:string", "post.descripcion:string", "post.larga:string", "post.vip:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/view/cama_form.phtml", "frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php"]
casos_uso: []
tags: ["ubiscamas", "cama", "update"]
estado_revision: "revisado"
errores: ["Habitación no válida", "Cama no válida", "Error al guardar la cama"]
---

# Cama Update

Alta o edición de una cama (descripción, larga, VIP) en una habitación. `sel` puede traer `id_cama` como primer token.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta o edición de una cama (descripción, larga, VIP) en una habitación. `sel` puede traer `id_cama` como primer token.

## Endpoint

- URL: `/src/ubiscamas/cama_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_habitacion#...` o `id_cama#...` según endpoint |
| `id_cama` | `string` | application | No |  |
| `id_habitacion` | `string` | application | No |  |
| `descripcion` | `string` | application | No |  |
| `larga` | `string` | application | No |  |
| `vip` | `string` | application | No |  |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado en mutaciones).

## Errores conocidos
- `Habitación no válida`
- `Cama no válida`
- `Error al guardar la cama`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION['oPerm']` y permisos del dossier/actividad padre.

## Casos De Uso

- Lógica inline en el controller (sin caso de uso en `application/`).

## Frontend Relacionado

- `frontend/ubiscamas/view/cama_form.phtml`
- `frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php`
