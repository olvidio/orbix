---
id: "actividadcargos.cargo_editar"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/cargo_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadcargos/infrastructure/ui/http/controllers/cargo_editar.php"
entrada: ["post.asis:string", "post.asis_presente:mixed", "post.id_activ:integer", "post.id_cargo:integer", "post.id_item:integer", "post.id_nom:integer", "post.observ:string", "post.puede_agd:string"]
entrada_obligatoria: ["id_activ", "id_nom", "id_cargo"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom / id_cargo", "no encuentro el cargo", "ya existe este cargo para esta actividad", "hay un error, no se ha guardado", "hay un error, no se ha guardado el asistente", "hay un error, no se ha eliminado el asistente"]
frontend_referencias: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoEditar"]
tags: ["actividadcargos", "cargo", "editar"]
estado_revision: "generado"
---

# Cargo Editar

Edita un `ActividadCargo` existente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadcargos/cargo_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `asis` | `string` | controller+application | No | controller+application |
| `asis_presente` | `mixed` | controller+application | No | controller+application |
| `id_activ` | `integer` | application | Si | application |
| `id_cargo` | `integer` | application | Si | application |
| `id_item` | `integer` | application | No | application |
| `id_nom` | `integer` | application | Si | application |
| `observ` | `string` | application | No | application |
| `puede_agd` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Si `asis` cambia respecto al estado actual del `Asistente`, lo crea o lo elimina, y actualiza los dossiers 1301/3101 correspondientes.

## Errores conocidos

- `faltan parametros id_activ / id_nom / id_cargo`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`
- `hay un error, no se ha guardado`
- `hay un error, no se ha guardado el asistente`
- `hay un error, no se ha eliminado el asistente`

## Casos De Uso

- `src\actividadcargos\application\ActividadCargoEditar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.