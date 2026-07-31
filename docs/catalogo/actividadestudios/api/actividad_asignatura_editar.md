---
id: "actividadestudios.actividad_asignatura_editar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/actividad_asignatura_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_editar.php"
entrada: ["post.avis_profesor:string", "post.f_fin:string", "post.f_ini:string", "post.id_activ:integer", "post.id_asignatura:integer", "post.id_profesor:integer", "post.tipo:string"]
entrada_obligatoria: ["id_activ", "id_asignatura"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan claves de la asignatura de actividad", "no encuentro la asignatura", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaEditar"]
tags: ["actividadestudios", "actividad", "asignatura", "editar"]
estado_revision: "revisado"
---

# Actividad Asignatura Editar

Edita una `ActividadAsignatura` existente (profesor, aviso, tipo y fechas). Sustituye al case
`editar` del antiguo `update_3005.php` dispatcher.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Localiza la `ActividadAsignatura` por `(id_activ, id_asignatura)` y actualiza profesor, aviso, tipo,
`f_ini` y `f_fin` (fechas locales vacías → `null`).

## Endpoint

- URL: `/src/actividadestudios/actividad_asignatura_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Clave actividad |
| `id_asignatura` | `integer` | application | Sí | Clave asignatura |
| `id_profesor` | `integer` | application | No | Profesor asignado |
| `avis_profesor` | `string` | application | No | Aviso al profesor |
| `tipo` | `string` | application | No | Tipo de impartición |
| `f_ini` | `string` | application | No | Fecha local inicio; vacío → null |
| `f_fin` | `string` | application | No | Fecha local fin; vacío → null |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan claves de la asignatura de actividad`
- `no encuentro la asignatura`
- `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`form_asignaturas_de_una_actividad.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\ActividadAsignaturaEditar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`
