---
id: "actividades.actividad_eliminar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_eliminar.php"
entrada: ["post.id_activ:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["actividad no encontrada", "sesión de permisos no disponible", "No tiene permiso para borrar esta actividad", "hay un error, no se ha eliminado", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividades/view/actividad_select.phtml", "frontend/actividades/view/lista_actividades_sg.phtml"]
casos_uso: ["src\\actividades\\application\\ActividadEliminar", "src\\actividades\\application\\BorrarActividad"]
tags: ["actividades", "actividad", "eliminar"]
estado_revision: "revisado"
---

# Actividad Eliminar

Elimina (o marca como borrable) las actividades indicadas. Acepta dos formas de
entrada, acumulables:

- `sel[]`: ids marcados con checkbox en un listado (formato `id` o `id#extra`,
  se trunca en `#`).
- `id_activ`: id unico (flujo desde planning, borrar una ficha concreta).

La logica real esta en `BorrarActividad::ejecutar`, que **no siempre borra
fisicamente**:

| Caso | Accion |
|------|--------|
| Actividad de la propia dl con `status = PROYECTO` | Borrado fisico (tabla dl). |
| Actividad de la propia dl con otro status | Se marca `status = BORRABLE`. |
| Actividad de otra dl, `id_tabla='dl'` (importada) | Se elimina solo el registro de `Importada`. |
| Actividad de otra dl, `id_tabla='ex'` | Se marca `status = BORRABLE`. |

## Endpoint

- URL: `/src/actividades/actividad_eliminar`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_eliminar.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `sel` | `array` | No* | Ids seleccionados (`id` o `id#extra`). |
| `id_activ` | `integer` | No* | Id unico (planning). *Al menos uno de los dos para que haga algo. |

## Salida

- Helper: `ContestarJson::enviar`
- Exito: `success: true` (sin payload).
- Error: `success: false`, `mensaje` concatena los errores de cada actividad fallida.

## Permisos

- Si la app `procesos` esta instalada: por **cada** actividad exige
  `$_SESSION['oPermActividades']` y valida `have_perm_activ('borrar')` para esa
  actividad concreta; si no lo tiene, esa actividad no se toca y se acumula el error.
- Sin `procesos`: no hay validacion de permisos en servidor.

## Errores conocidos

- `actividad no encontrada`
- `sesión de permisos no disponible`
- `No tiene permiso para borrar esta actividad`
- `hay un error, no se ha eliminado` / `hay un error, no se ha guardado` + detalle

## Casos De Uso

- `src\actividades\application\ActividadEliminar`
- `src\actividades\application\BorrarActividad` (logica de borrado/marcado)

## Frontend Relacionado

- `frontend/actividades/view/actividad_select.phtml` — boton eliminar del listado de busqueda
- `frontend/actividades/view/lista_actividades_sg.phtml` — boton eliminar del listado sg

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadEliminar` + `BorrarActividad`):
  tabla de casos de borrado, permiso `borrar` por actividad y consumidores verificados.
- Pendiente: ejemplos reales de request/response.
