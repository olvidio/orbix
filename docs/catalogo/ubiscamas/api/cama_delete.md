---
id: "ubiscamas.cama_delete"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/cama_delete"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/cama_delete.php"
entrada: ["post.id_cama:string"]
entrada_obligatoria: ["id_cama"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/view/habitacion_form.phtml"]
casos_uso: []
tags: ["ubiscamas", "cama", "delete"]
estado_revision: "revisado"
errores: ["ID de cama no proporcionado", "No se encontró la cama a eliminar", "hay un error, no se ha eliminado la cama", "Error al eliminar la cama"]
---

# Cama Delete

Elimina una cama por UUID desde el formulario de habitación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina una cama por UUID desde el formulario de habitación.

## Endpoint

- URL: `/src/ubiscamas/cama_delete`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_delete.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cama` | `string` | application | Si |  |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado en mutaciones).

## Errores conocidos
- `ID de cama no proporcionado`
- `No se encontró la cama a eliminar`
- `hay un error, no se ha eliminado la cama`
- `Error al eliminar la cama`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION['oPerm']` y permisos del dossier/actividad padre.

## Casos De Uso

- Lógica inline en el controller (sin caso de uso en `application/`).

## Frontend Relacionado

- `frontend/ubiscamas/view/habitacion_form.phtml`
