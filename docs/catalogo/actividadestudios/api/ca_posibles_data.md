---
id: "actividadestudios.ca_posibles_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/ca_posibles_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_data.php"
entrada: ["post.ca_estudios:string", "post.ca_repaso:string", "post.ca_todos:string", "post.empiezamax:string", "post.empiezamin:string", "post.grupo_estudios:string", "post.id_ctr_agd:integer", "post.id_ctr_n:integer", "post.idca:string", "post.na:string", "post.obj_pau:string", "post.periodo:string", "post.ref:string", "post.sel:mixed", "post.texto:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/ca_posibles.php"]
casos_uso: ["src\\actividadestudios\\application\\CaPosiblesData"]
tags: ["actividadestudios", "ca", "posibles", "data"]
estado_revision: "generado"
---

# Ca Posibles Data

Misma lógica que `frontend/.../ca_posibles.php`; respuesta serializable. En modo `lista`, `pagina_link_spec` lo firma el front ({

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/ca_posibles_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ca_estudios` | `string` | application | No | application |
| `ca_repaso` | `string` | application | No | application |
| `ca_todos` | `string` | application | No | application |
| `empiezamax` | `string` | application | No | application |
| `empiezamin` | `string` | application | No | application |
| `grupo_estudios` | `string` | application | No | application |
| `id_ctr_agd` | `integer` | application | No | application |
| `id_ctr_n` | `integer` | application | No | application |
| `idca` | `string` | application | No | application |
| `na` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `periodo` | `string` | application | No | application |
| `ref` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |
| `texto` | `string` | application | No | application |
| `year` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadestudios\application\CaPosiblesData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/ca_posibles.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.