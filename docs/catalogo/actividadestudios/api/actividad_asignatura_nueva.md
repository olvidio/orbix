---
id: "actividadestudios.actividad_asignatura_nueva"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/actividad_asignatura_nueva"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_nueva.php"
entrada: ["post.avis_profesor:string", "post.f_fin:string", "post.f_ini:string", "post.id_activ:integer", "post.id_asignatura:integer", "post.id_profesor:integer", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan claves de la asignatura de actividad", "hay un error, no se ha creado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaNueva"]
tags: ["actividadestudios", "actividad", "asignatura", "nueva"]
estado_revision: "generado"
---

# Actividad Asignatura Nueva

Crea una `ActividadAsignatura` (asignatura impartida en un ca) y abre el dossier 3005 de la actividad. Sustituye al case `nuevo` del antiguo `update_3005.php` dispatcher.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/actividad_asignatura_nueva`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_nueva.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `avis_profesor` | `string` | application | No | application |
| `f_fin` | `string` | application | No | application |
| `f_ini` | `string` | application | No | application |
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |
| `id_profesor` | `integer` | application | No | application |
| `tipo` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Crea una `ActividadAsignatura` (asignatura impartida en un ca) y abre el dossier 3005 de la actividad.

## Errores conocidos

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha creado`

## Casos De Uso

- `src\actividadestudios\application\ActividadAsignaturaNueva`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.