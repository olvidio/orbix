---
id: "actividades.actividad_nuevo_curso_ejecutar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_nuevo_curso_ejecutar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_nuevo_curso_ejecutar.php"
entrada: ["post.year_ref:integer", "post.year:integer", "post.ver_lista:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadNuevoCursoEjecutarData"
respuesta_data: ["html:string", "copiadas:integer"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/actividad_nuevo_curso.php"]
casos_uso: ["src\\actividades\\application\\ActividadNuevoCursoEjecutar"]
tags: ["actividades", "actividad", "nuevo", "curso", "ejecutar"]
estado_revision: "revisado"
---

# Actividad Nuevo Curso Ejecutar

Genera las actividades del nuevo curso copiándolas desde un curso de referencia, solo para la
delegación actual. Es una operación con efectos (borra y crea actividades).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para la delegación propia (`dl_org` = mi dele y `mi dele + 'f'`):

1. Borra las actividades del año destino (`year`), acumulando incidencias.
2. Copia las actividades de status 2 y 3 del año de referencia (`year_ref`) al nuevo curso mediante
   `ActividadNuevoCurso::crear_actividad`, replicando repeticiones, centros encargados y fases.
3. Comprueba solapes y recoge avisos del proceso.

Devuelve un HTML con el resumen (copiadas, incidencias al borrar, errores al crear, solapes, avisos) y
el número de actividades copiadas. Con `ver_lista` activado, antepone la lista detallada de las creadas.
El registro de cambios queda deshabilitado (`setRegistrarCambios(false)`).

## Endpoint

- URL: `/src/actividades/actividad_nuevo_curso_ejecutar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo_curso_ejecutar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `year_ref` | `integer` | controller | No | Año de referencia (origen de la copia) |
| `year` | `integer` | controller | No | Año destino (se borra antes de copiar) |
| `ver_lista` | `string` | controller | No | Si es "true"/no vacío, incluye la lista detallada |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data`: `{ html: string, copiadas: integer }`. Las incidencias/errores no viajan como `success: false`,
  sino integradas en `data.html`.

## Permisos

- Sin control de permisos propio en el caso de uso; opera siempre sobre la delegación actual. La
  autorización se resuelve en el frontend (`actividad_nuevo_curso.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\ActividadNuevoCursoEjecutar`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_nuevo_curso.php`
