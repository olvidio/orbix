---
id: "personas.traslado_update"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/traslado_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/traslado_update.php"
entrada: ["post.ctr_o:string", "post.dl:string", "post.f_ctr:string", "post.f_dl:string", "post.id_ctr_o:string", "post.id_pau:integer", "post.new_ctr:string", "post.new_dl:string", "post.obj_pau:string", "post.situacion:string"]
entrada_obligatoria: ["post.id_pau", "post.obj_pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan id_pau u obj_pau", "No existe la clase de la persona", "No se encuentra la persona", "Falta una situación válida", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/personas/view/traslado_form.phtml"]
casos_uso: ["src\\personas\\application\\TrasladoUpdate"]
tags: ["personas", "traslado", "update"]
estado_revision: "revisado"
---

# Traslado Update

Aplica traslado de centro y/o delegación y abre el dossier de traslados (tipo 1004).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dos bloques independientes (pueden ejecutarse en la misma petición):

- **Centro** (`new_ctr` + `f_ctr`): actualiza `id_ctr` en `PersonaDl`, registra `Traslado` tipo
  `sede` con origen/destino.
- **Delegación** (`new_dl` + `f_dl` + `situacion`): usa dominio `Trasladar` con región/sfsv;
  requiere situación válida (`SituacionCode`).
- Siempre crea/abre dossier `p` / `id_pau` / tipo `1004`.

`obj_pau` admite N, Agd, Nax, S, SSSC, Ex (no Sacd en match del update).

## Endpoint

- URL: `/src/personas/traslado_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/traslado_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_pau` | `integer` | application | Sí | `id_nom` de la persona |
| `obj_pau` | `string` | application | Sí | |
| `new_ctr` | `string` | application | No | `id_ctr#nombre` (usa strtok `#`) |
| `f_ctr` | `string` | application | No | Fecha traslado centro |
| `id_ctr_o` | `string` | application | No | Centro origen (hidden) |
| `ctr_o` | `string` | application | No | Nombre ctr origen |
| `new_dl` | `string` | application | No | Delegación destino |
| `f_dl` | `string` | application | No | Fecha traslado dl |
| `situacion` | `string` | application | Cond. | Obligatorio si hay traslado dl |
| `dl` | `string` | application | No | Dl origen (hidden) para `reg_dl_org` |

## Salida

- Helper: `ContestarJson::enviar($error_txt, 'ok')`.
- Éxito parcial: puede devolver cadena vacía aunque hubo errores concatenados en bloques;
  errores en `mensaje` (ltrim de `\n`).

## Permisos

- Sin control en caso de uso; acceso desde ficha con permiso de edición.

## Efectos colaterales

- Persiste filas en `traslado`, puede invocar `Trasladar::trasladar()`, abre dossier 1004.

## Errores conocidos

- `Faltan id_pau u obj_pau`
- `No existe la clase de la persona`
- `No se encuentra la persona`
- `Falta una situación válida` (traslado dl)
- `hay un error, no se ha guardado`
- Errores de dominio `Trasladar` (concatenados en `mensaje`)

## Casos De Uso

- `src\personas\application\TrasladoUpdate`

## Frontend Relacionado

- `frontend/personas/view/traslado_form.phtml` (`fnjs_guardar`)
