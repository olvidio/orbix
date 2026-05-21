---
id: "asistentes.asistente_eliminar"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/asistente_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/asistente_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.pau:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom", "los datos de asistencia los modifica la dl del asistente", "hay un error, no se ha eliminado"]
frontend_referencias: []
casos_uso: ["src\\asistentes\\application\\AsistenteEliminar"]
tags: ["asistentes", "asistente", "eliminar"]
estado_revision: "generado"
---

# Asistente Eliminar

Elimina un `Asistente` y sus matriculas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/asistente_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `pau` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un `Asistente` y las `Matricula`s asociadas, cerrando tambien el dossier 1301.
- Sustituye al case `eliminar` del antiguo `apps/asistentes/controller/update_3101.php`.

## Errores conocidos

- `faltan parametros id_activ / id_nom`
- `los datos de asistencia los modifica la dl del asistente`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\asistentes\application\AsistenteEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.