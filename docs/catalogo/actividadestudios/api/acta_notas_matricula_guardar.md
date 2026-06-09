---
id: "actividadestudios.acta_notas_matricula_guardar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/acta_notas_matricula_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_matricula_guardar.php"
entrada: ["post.acta_nota:array", "post.form_preceptor:array", "post.id_activ:integer", "post.id_asignatura:integer", "post.id_nom:array", "post.nota_max:array", "post.nota_num:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Hay una nota mayor que el máximo", "no se puede definir cursada con preceptor", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasMatriculaGuardar"]
tags: ["actividadestudios", "acta", "notas", "matricula", "guardar"]
estado_revision: "generado"
---

# Acta Notas Matricula Guardar

Guarda las notas de cada matricula (borrador del acta de notas). Se invoca desde la pantalla `acta_notas` cuando el usuario pulsa "Grabar". Sustituye a la rama `que=1` del legacy `apps/actividadestudios/controller/acta_notas_update.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/acta_notas_matricula_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_matricula_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta_nota` | `array` | application | No | application |
| `form_preceptor` | `array` | application | No | application |
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |
| `id_nom` | `array` | application | No | application |
| `nota_max` | `array` | application | No | application |
| `nota_num` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `Hay una nota mayor que el máximo`
- `no se puede definir cursada con preceptor`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadestudios\application\ActaNotasMatriculaGuardar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/acta_notas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.