---
id: "actividadestudios.matricula_automatica"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matricula_automatica"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matricula_automatica.php"
entrada: ["post.id_activ:integer", "post.id_pau:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha encontrado a la persona con id: %s", "está de repaso", "no se ha hecho nada con %s no tiene asignado ca", "no se ha hecho nada com %s. ya tiene el plan de estudios confirmado", "hay un error, no se ha eliminado", "error al guardar la matrícula", "no se ha hecho nada con %s, tiene asignado más de un ca", "no se ha hecho nada"]
frontend_referencias: ["frontend/actividadestudios/controller/matricular.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaAutomatica"]
tags: ["actividadestudios", "matricula", "automatica"]
estado_revision: "revisado"
---

# Matricula Automatica

Matricula automáticamente a una o varias personas en las asignaturas de su plan de estudios del
curso actual. Sustituye a `apps/actividadestudios/controller/matricular.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recorre alumnos (uno o masivo DL), determina el CA de estudios del curso, y si el plan no está
confirmado borra matrículas previas y recalcula las matriculables (aprobadas + topes de opcionales).

**Casos particulares**

- **Ramas de entrada:**
  - Con `sel[]`: toma el primer token; `id_nom = strtok(sel, '#')` (no usa `id_activ` del POST).
  - Sin `sel`, con `id_pau` (+ opcional `id_activ`): persona concreta; si hay `id_activ`, fuerza esa
    asistencia (dl, out o ex).
  - Sin `sel` ni `id_pau`: masivo DL, `situacion = 'A'`.
- **Excluye nivel R:** persona concreta con `nivel_stgr = R` (repaso) → mensaje `está de repaso`.
  Un nivel vacío/NULL **no** es repaso (no se filtra con SQL `!= R`: en PostgreSQL
  `NULL != R` no devuelve la fila). El masivo DL sigue filtrando `nivel_stgr != R` en SQL.
- **Asistencia con `id_activ`:** busca en dl, out y **ex** (`d_asistentes_ex` para personas
  de paso con `id_nom` negativo).
- **Switch `count(asistencias)`:**
  - `0`: no hace nada (`no tiene asignado ca`).
  - `1`: si `est_ok` (plan confirmado) → no toca; si no, borra matrículas del CA, matricula
    asignaturas del CA (`tipo` null o no `'x'`), salta aprobadas.
  - `default` (>1): no hace nada (`tiene asignado más de un ca`).
- **Opcionales `id_asignatura > 3000`:** bloque = 2º dígito del id; topes por notas en niveles del
  bloque (1→max 3, 2→max 5, 3→max 8); si ya está al tope, skip; bloque desconocido → skip.
- **Curso:** fechas de curso STGR según mes y `mesFinStgr` de config; actividades status actual,
  tipos `sfsv(122)|(222)|(332)`.

## Endpoint

- URL: `/src/actividadestudios/matricula_automatica`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_automatica.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Primer token → `id_nom` (antes de `#`) |
| `id_pau` | `integer` | application | No | Persona si no hay `sel` |
| `id_activ` | `integer` | application | No | Solo sin `sel`; fuerza esa actividad |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- El UC devuelve `{success, msg}`. El controller:
  - **Éxito** (`success` true): `success: true`, `data` = JSON string de `{ "msg": "…" }`
    (no `"ok"`).
  - **Error / sin éxito:** `success: false`, `mensaje` = texto acumulado, `data: "ok"`.
- Si el UC no acumula mensajes: `msg = no se ha hecho nada`.

## Efectos colaterales

- Puede eliminar matrículas previas del CA y crear nuevas (obligatorias y opcionales bajo tope).
- No modifica dossiers.

## Errores conocidos

- `No se ha encontrado a la persona con id: %s`
- `está de repaso`
- `no se ha hecho nada con %s no tiene asignado ca`
- `no se ha hecho nada com %s. ya tiene el plan de estudios confirmado` (texto literal del UC)
- `hay un error, no se ha eliminado`
- `error al guardar la matrícula`
- `no se ha hecho nada con %s, tiene asignado más de un ca`
- `no se ha hecho nada`

Mensaje de éxito parcial por persona: `%s se ha matriculado de %s asignaturas`.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`matricular.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\MatriculaAutomatica`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matricular.php` (PostRequest a esta URL).
