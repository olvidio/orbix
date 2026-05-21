---
id: "actividadestudios.actividad_asignatura_editar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/actividad_asignatura_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_editar.php"
entrada: ["post.avis_profesor:string", "post.f_fin:string", "post.f_ini:string", "post.id_activ:integer", "post.id_asignatura:integer", "post.id_profesor:integer", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan claves de la asignatura de actividad", "no encuentro la asignatura", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaEditar"]
tags: ["actividadestudios", "actividad", "asignatura", "editar"]
estado_revision: "generado"
---

# Actividad Asignatura Editar

Edita una `ActividadAsignatura` existente. Sustituye al case `editar` del antiguo `update_3005.php` dispatcher.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/actividad_asignatura_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_editar.php`

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

## Errores conocidos

- `faltan claves de la asignatura de actividad`
- `no encuentro la asignatura`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadestudios\application\ActividadAsignaturaEditar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.