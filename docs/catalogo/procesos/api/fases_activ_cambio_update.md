---
id: "procesos.fases_activ_cambio_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_update.php"
entrada: ["post.accion:string", "post.id_fase_nueva:string", "post.sel:array"]
entrada_obligatoria: ["id_fase_nueva", "sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra esta fase %s para esta actividad %s(%s)", "puede que tenga que regenerar el proceso", "error: La fase del proceso tipo: %s, fase: %s, tarea: %s", "hay un error, no se ha guardado", "No tiene permiso para completar la fase, no se ha guardado"]
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioUpdate"]
tags: ["procesos", "fases", "activ", "cambio", "update"]
estado_revision: "revisado"
---

# Fases Activ Cambio Update

Aplica `setCompletado` a la fase nueva para las actividades seleccionadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recorre `sel` (lista de `id_activ`, admite tokens `id#...`) y marca o desmarca la fase indicada
en `id_fase_nueva` según `accion` (`marcar` / `desmarcar`). Valida permiso de oficina responsable
y existencia de la tarea en el proceso de cada actividad. Acumula avisos por actividad sin abortar
el lote completo.

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_fase_nueva` | `string` | application | Si | Fase a marcar/desmarcar |
| `sel` | `array` | application | Si | Lista de `id_activ` (tokens `id#...` truncados al `#`) |
| `accion` | `string` | application | No | `desmarcar` pone `completado=false`; otro valor marca |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar($txtOut)` — el texto de salida va en `mensaje` si no está vacío
- Forma: `standard_envelope_string_data`
- Exito sin avisos: `success: true`, `data: "ok"`.
- Con avisos parciales: `success: false`, `mensaje` con HTML/texto acumulado por actividad.

## Errores conocidos

- `No se encuentra esta fase %s para esta actividad %s(%s)` + `puede que tenga que regenerar el proceso`
- `error: La fase del proceso tipo: %s, fase: %s, tarea: %s` (aborta el lote)
- `hay un error, no se ha guardado` (puede concatenar error de repositorio)
- `No tiene permiso para completar la fase, no se ha guardado`
- Errores de `ProcesoActividadService::getErrorTxt()` si los hay

## Permisos

- Por actividad: exige permiso de oficina responsable (`have_perm_oficina`) salvo oficina vacía.

## Casos De Uso

- `src\procesos\application\FasesActivCambioUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio.php` (URL emitida como `url_update`)
