---
id: "actividadestudios.matricula_eliminar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matricula_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matricula_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_nom:integer", "post.id_pau:integer", "post.pau:string", "post.sel:array"]
entrada_obligatoria: ["pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no encuentro la matricula", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_pendientes.php", "frontend/actividadestudios/view/matriculas.phtml", "frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml", "frontend/actividadestudios/view/select_matriculas_de_una_actividad.phtml"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaEliminar"]
tags: ["actividadestudios", "matricula", "eliminar"]
estado_revision: "revisado"
---

# Matricula Eliminar

Elimina una o varias matrículas y reajusta dossiers 1303 / 3103 y asignaturas impartidas
(`ActividadAsignatura`). Sustituye al case `eliminar` del antiguo `update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borrado según el origen del dossier (`pau`). Alias persona: si `id_nom` ≤ 0, usa `id_pau`.

**Casos particulares**

- **`pau === 'p'` (persona / pendientes):** itera `sel[]`:
  - Token de **2 partes** (`id_activ#id_nom`): borra **todas** las matrículas pendientes de esa
    persona (`getMatriculasPendientes`) y cierra dossier 1303.
  - Token de **3 partes** (`id_activ#id_asignatura#id_nom`): borra esa matrícula; si llega
    `id_activ` en POST, sobrescribe el del token; `id_nom` vacío en token cae a `id_pau`/`id_nom`.
  - Tras cada borrado individual: cierra dossier 1303 de la persona; si queda una sola
    `ActividadAsignatura` y ya no hay matrículas de esa asignatura en la actividad, la elimina.
- **`pau === 'a'` (actividad):** una sola matrícula. Con `sel`, parsea el primer token con
  `strtok('#')` en orden `id_nom`, `id_asignatura`, `id_activ` (en dossier actividad el sel típico
  es `id_nom#id_asignatura` y `id_activ` viene del POST). Sin `sel`, usa `id_activ` / `id_nom` /
  `id_asignatura`. Cierra dossier 3103 de la actividad. Errores: `no encuentro la matricula` /
  `hay un error, no se ha borrado`.
- **`pau` vacío u otro:** no hace nada (devuelve `''`).

## Endpoint

- URL: `/src/actividadestudios/matricula_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `pau` | `string` | application | Sí | `'p'` persona / `'a'` actividad |
| `sel` | `array` | application | Condicional | Tokens; ver objetivo |
| `id_activ` | `integer` | application | Condicional | Fallback / override |
| `id_asignatura` | `integer` | application | Condicional | Rama `'a'` sin sel |
| `id_nom` / `id_pau` | `integer` | application | Condicional | Alias: `id_nom` vacío → `id_pau` |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina matrícula(s).
- Cierra dossier 1303 (rama `'p'`) o 3103 (rama `'a'`).
- Puede eliminar `ActividadAsignatura` huérfana (última matrícula de esa asignatura en la actividad).

## Errores conocidos

- `no encuentro la matricula` (solo rama `'a'`; en `'p'` matrícula ausente se ignora)
- `hay un error, no se ha borrado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\MatriculaEliminar`

## Frontend Relacionado

- Pendientes: `frontend/actividadestudios/controller/matriculas_pendientes.php` (`pau=p`).
- Dossier persona: `select_matriculas_de_una_persona.phtml` (sel `id_activ#id_asignatura#id_nom`).
- Dossier actividad: `select_matriculas_de_una_actividad.phtml` (sel `id_nom#id_asignatura`).
- Listado: `frontend/actividadestudios/view/matriculas.phtml`.
