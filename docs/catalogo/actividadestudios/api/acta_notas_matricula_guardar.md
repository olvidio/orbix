---
id: "actividadestudios.acta_notas_matricula_guardar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/acta_notas_matricula_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_matricula_guardar.php"
entrada: ["post.acta_nota:array", "post.form_preceptor:array", "post.id_activ:integer", "post.id_asignatura:integer", "post.id_nom:array", "post.nota_max:array", "post.nota_num:array"]
entrada_obligatoria: ["id_activ", "id_asignatura", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Hay una nota mayor que el máximo", "no se puede definir cursada con preceptor", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasMatriculaGuardar"]
tags: ["actividadestudios", "acta", "notas", "matricula", "guardar"]
estado_revision: "revisado"
---

# Acta Notas Matricula Guardar

Guarda las notas de cada matrícula (borrador del acta de notas). Se invoca desde `acta_notas` al
pulsar "Grabar". Sustituye a la rama `que=1` del legacy
`apps/actividadestudios/controller/acta_notas_update.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recorre en paralelo los arrays `id_nom`, `nota_num`, `nota_max`, `acta_nota` y `form_preceptor`
(índice = alumno), actualiza cada `Matricula` encontrada y calcula `id_situacion` según nota, acta
y preceptor. Usa el corte de nota de `$_SESSION['oConfig']`.

**Casos particulares**

- **`id_nom` inválido o matrícula inexistente:** salta ese índice (`continue`).
- **Nota > máximo:** aborta con error (no guarda el resto).
- **No preceptor:** si `acta_nota == 2` (cursada) pone situación 2, o 12 si hay nota > 1; si hay nota
  numérica, pone 12 (por debajo del corte) o 10 (aprobada).
- **Preceptor:** rechaza `CURSADA`; sin nota → situación 0; con nota → 10.

## Endpoint

- URL: `/src/actividadestudios/acta_notas_matricula_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_matricula_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Actividad |
| `id_asignatura` | `integer` | application | Sí | Asignatura |
| `id_nom` | `array` | application | Sí | Lista de alumnos (índice = fila) |
| `nota_num` | `array` | application | No | Nota numérica por fila |
| `nota_max` | `array` | application | No | Nota máxima por fila |
| `acta_nota` | `array` | application | No | Acta / situación por fila |
| `form_preceptor` | `array` | application | No | `'p'` = preceptor en esa fila |

El controller pasa `$_POST` completo al caso de uso. Los arrays van alineados por índice.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Errores conocidos

- `Hay una nota mayor que el máximo`
- `no se puede definir cursada con preceptor`
- `hay un error, no se ha guardado` (+ texto de error del repositorio)

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`acta_notas.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\ActaNotasMatriculaGuardar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/acta_notas.php`
