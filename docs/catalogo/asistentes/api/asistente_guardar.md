---
id: "asistentes.asistente_guardar"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/asistente_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/asistente_guardar.php"
entrada: ["post.cfi:string", "post.cfi_con:integer", "post.encargo:string", "post.est_ok:string", "post.falta:string", "post.id_activ:integer", "post.id_activ_old:integer", "post.id_nom:integer", "post.id_pau:integer", "post.mod:string", "post.observ:string", "post.observ_est:string", "post.pau:string", "post.plaza:integer", "post.propietario:string", "post.propio:string", "post.sel:array"]
entrada_obligatoria: ["mod"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["mod no soportado: %s", "faltan parametros id_activ / id_nom", "falta id_activ_old", "los datos de asistencia los modifica la dl del asistente", "hay un error, no se ha guardado"]
frontend_referencias: []
casos_uso: ["src\\asistentes\\application\\AsistenteGuardar"]
tags: ["asistentes", "asistente", "guardar"]
estado_revision: "revisado"
---

# Asistente Guardar

Crea, edita o mueve un `Asistente`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sustituye los cases `nuevo`, `editar` y `mover` del legacy `update_3101.php`:

- `mod=nuevo`: abre el dossier `1301` y guarda el asistente.
- `mod=editar`: actualiza campos del asistente existente (valida `perm_modificar()`).
- `mod=mover`: guarda en `id_activ` destino y, si tiene éxito, elimina el origen (`id_activ_old`).

## Endpoint

- URL: `/src/asistentes/asistente_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `mod` | `string` | application | Si | `nuevo`, `editar` o `mover` |
| `pau` | `string` | application | No | `p` (persona) o `a` (actividad); con `sel` resuelve ids cruzados |
| `sel` | `array` | application | No | Token `id_nom#...` o `id_activ#...`; primer elemento |
| `id_activ` | `integer` | application | Si* | Destino; alternativa vía `sel`+`pau=a`+`id_pau` |
| `id_nom` | `integer` | application | Si* | Alternativa vía `sel`+`pau=p`+`id_pau` |
| `id_activ_old` | `integer` | application | Si (mover) | Actividad origen en `mod=mover` |
| `encargo` | `string` | application | No | |
| `observ` | `string` | application | No | |
| `observ_est` | `string` | application | No | |
| `propio` | `string` | application | No | Checkbox; en `mover` se fuerza `true` |
| `est_ok` | `string` | application | No | |
| `cfi` | `string` | application | No | |
| `falta` | `string` | application | No | |
| `cfi_con` | `integer` | application | No | |
| `propietario` | `string` | application | No | `xxx` se normaliza a vacío; en `mover` puede autocalcularse |
| `plaza` | `integer` | application | No | Validada con `setPlazaVoComprobando` si `actividadplazas` |

\* Al menos uno de los pares `id_activ`/`id_nom` debe resolverse distinto de 0.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"` (string vacío del caso de uso).

## Efectos colaterales

- `nuevo`: abre dossier `1301` para la persona.
- `mover`: elimina asistente y matrículas en `id_activ_old` vía `AsistenteEliminar`.
- Asigna `dl_responsable` a la delegación actual.

## Errores conocidos

- `mod no soportado: %s`
- `faltan parametros id_activ / id_nom`
- `falta id_activ_old` (solo `mover`)
- `los datos de asistencia los modifica la dl del asistente` (`editar`/`mover` sin `perm_modificar`)
- Errores de plaza devueltos por `setPlazaVoComprobando`
- `hay un error, no se ha guardado`

## Permisos

- `editar` y `mover` comprueban `Asistente::perm_modificar()` en el caso de uso.
- Alta/baja desde formularios: autorización de oficina en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\AsistenteGuardar`

## Frontend Relacionado

- Invocado desde submit de `form_asistentes_a_una_actividad`, `form_actividades_de_una_persona`,
  modal `asistente_mover` y enlaces de `tabla_peticiones` (URL en payload como `paths.asistente_guardar` /
  `paths.guardar`). No hay referencia literal a la URL en los controllers.
