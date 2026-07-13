---
id: "actividadcargos.cargo_nuevo"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/cargo_nuevo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadcargos/infrastructure/ui/http/controllers/cargo_nuevo.php"
entrada: ["post.asis:string", "post.id_activ:integer", "post.id_cargo:integer", "post.id_nom:integer", "post.observ:string", "post.puede_agd:string"]
entrada_obligatoria: ["id_activ", "id_nom", "id_cargo"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom / id_cargo", "ya existe este cargo para esta actividad", "hay un error, no se ha guardado el asistente"]
frontend_referencias: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoNuevo"]
tags: ["actividadcargos", "cargo", "nuevo"]
estado_revision: "revisado"
---

# Cargo Nuevo

Crea un `ActividadCargo` (asigna un cargo a una persona en una actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Da de alta el vínculo persona–cargo–actividad. Si el formulario marca `asis` (checkbox de
asistencia, controlado por `asis_presente`), además crea el `Asistente` correspondiente y abre los
dossiers `1301`/`3101`. Réplica del case `nuevo` del legacy `update_3102.php`.

## Endpoint

- URL: `/src/actividadcargos/cargo_nuevo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_nuevo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `asis` | `string` | application | No | application |
| `id_activ` | `integer` | application | Si | application |
| `id_cargo` | `integer` | application | Si | application |
| `id_nom` | `integer` | application | Si | application |
| `observ` | `string` | application | No | application |
| `puede_agd` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Si llega `asis=true` ademas da de alta al `Asistente` asociado (flujo tipico `nuevo` desde el form 3102/1302).
- Replica la logica del case `nuevo` del antiguo `update_3102.php`: abrir dossiers `1302`/`3102` (cargos) y, cuando proceda, `1301`/`3101` (asistentes).

## Errores conocidos

- `faltan parametros id_activ / id_nom / id_cargo`
- `ya existe este cargo para esta actividad`
- `hay un error, no se ha guardado el asistente`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (form de cargos) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadcargos\application\ActividadCargoNuevo`

## Frontend Relacionado

- Invocado desde el submit del form `form_cargos_de_actividad` / `form_cargos_personas_en_actividad`
  (URL emitida en su payload como `url_cargo_nuevo`). No hay referencia literal a la URL en `frontend/`.