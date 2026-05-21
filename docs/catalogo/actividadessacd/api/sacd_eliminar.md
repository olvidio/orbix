---
id: "actividadessacd.sacd_eliminar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_cargo:integer", "post.id_nom:integer"]
entrada_obligatoria: ["id_activ", "id_cargo"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se sabe cual borrar"]
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdEliminar"]
tags: ["actividadessacd", "sacd", "eliminar"]
estado_revision: "generado"
---

# Sacd Eliminar

Endpoint backend: elimina el sacd ({id_activ, id_cargo}) de una actividad y la asistencia asociada.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/sacd_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | application |
| `id_cargo` | `integer` | application | Si | application |
| `id_nom` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un sacd ({id_activ, id_cargo}) de una actividad, incluyendo la fila de `Asistencia` {id_activ, id_nom} asociada (si existe).
- En el legacy se usaban los metodos mal escritos `finsById` y `DBEliminar()` directamente en la entidad; aqui se arregla al contrato estandar del repositorio (`findById` + `Eliminar`).

## Errores conocidos

- `no se sabe cual borrar`

## Casos De Uso

- `src\actividadessacd\application\SacdEliminar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.