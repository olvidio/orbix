---
id: "actividadestudios.matricula_editar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matricula_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matricula_editar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_nivel:integer", "post.id_nom:integer", "post.id_pau:integer", "post.id_preceptor:integer", "post.id_situacion:integer", "post.preceptor:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan claves de la matricula", "no encuentro la matricula", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaEditar"]
tags: ["actividadestudios", "matricula", "editar"]
estado_revision: "generado"
---

# Matricula Editar

Edita una matricula.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/matricula_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |
| `id_nivel` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `id_preceptor` | `integer` | application | No | application |
| `id_situacion` | `integer` | application | No | application |
| `preceptor` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan claves de la matricula`
- `no encuentro la matricula`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadestudios\application\MatriculaEditar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.