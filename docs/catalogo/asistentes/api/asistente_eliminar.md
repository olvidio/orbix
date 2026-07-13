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
errores: ["faltan parametros id_activ / id_nom", "no se encuentra el asistente (id_nom: %s, id_activ: %s)", "los datos de asistencia los modifica la dl del asistente", "hay un error, no se ha eliminado"]
frontend_referencias: []
casos_uso: ["src\\asistentes\\application\\AsistenteEliminar"]
tags: ["asistentes", "asistente", "eliminar"]
estado_revision: "revisado"
---

# Asistente Eliminar

Elimina un `Asistente` y sus matrículas asociadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Case `eliminar` del legacy `update_3101.php`: borra el asistente, elimina matrículas de la actividad,
cierra el dossier `1301` de la persona y valida `perm_modificar()`.

## Endpoint

- URL: `/src/asistentes/asistente_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `pau` | `string` | application | No | `p` o `a`; con `sel` resuelve el par `id_activ`/`id_nom` |
| `sel` | `array` | application | No | Primer token: `id_activ#...` si `pau=p`, o `id_nom#...` si `pau=a` |
| `id_activ` | `integer` | application | Si* | Alternativa directa sin `sel` |
| `id_nom` | `integer` | application | Si* | Alternativa directa sin `sel` |
| `id_pau` | `integer` | application | No | Complemento de `sel` (`id_nom` si `pau=p`, `id_activ` si `pau=a`) |

\* Ambos deben resolverse distintos de 0.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina matrículas (`MatriculaRepository`) de la pareja `id_activ`/`id_nom`.
- Cierra dossier `1301` de la persona.

## Errores conocidos

- `faltan parametros id_activ / id_nom`
- `no se encuentra el asistente (id_nom: %s, id_activ: %s)`
- `los datos de asistencia los modifica la dl del asistente`
- `hay un error, no se ha eliminado` (puede acumularse al borrar matrículas)

## Permisos

- Comprueba `Asistente::perm_modificar()` antes de eliminar.
- Invocación desde listados/forms: autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\AsistenteEliminar`

## Frontend Relacionado

- Invocado desde dossiers `1301`/`3101` y listados de asistentes (acción eliminar). También lo llama
  internamente `AsistenteGuardar` en `mod=mover`. URL emitida en payloads de formulario.
