---
id: "actividadestudios.actividad_asignatura_nueva"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/actividad_asignatura_nueva"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_nueva.php"
entrada: ["post.avis_profesor:string", "post.f_fin:string", "post.f_ini:string", "post.id_activ:integer", "post.id_asignatura:integer", "post.id_profesor:integer", "post.tipo:string"]
entrada_obligatoria: ["id_activ", "id_asignatura"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan claves de la asignatura de actividad", "hay un error, no se ha creado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaNueva"]
tags: ["actividadestudios", "actividad", "asignatura", "nueva"]
estado_revision: "revisado"
---

# Actividad Asignatura Nueva

Crea una `ActividadAsignatura` (asignatura impartida en un ca) y abre el dossier 3005 de la
actividad. Sustituye al case `nuevo` del antiguo `update_3005.php` dispatcher.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea el vínculo actividad–asignatura con profesor, aviso, tipo y fechas. Tras guardar con éxito,
localiza o crea el dossier `tabla=a`, `id_tipo_dossier=3005` y lo abre.

## Endpoint

- URL: `/src/actividadestudios/actividad_asignatura_nueva`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_nueva.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Actividad destino |
| `id_asignatura` | `integer` | application | Sí | Asignatura a impartir |
| `id_profesor` | `integer` | application | No | Profesor |
| `avis_profesor` | `string` | application | No | Aviso al profesor |
| `tipo` | `string` | application | No | Tipo de impartición |
| `f_ini` | `string` | application | No | Fecha local inicio; vacío → null |
| `f_fin` | `string` | application | No | Fecha local fin; vacío → null |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Inserta `ActividadAsignatura` y abre (crea si falta) el dossier 3005 de la actividad.

## Errores conocidos

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha creado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`form_asignaturas_de_una_actividad.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\ActividadAsignaturaNueva`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`
