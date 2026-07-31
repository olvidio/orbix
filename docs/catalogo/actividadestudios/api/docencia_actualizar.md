---
id: "actividadestudios.docencia_actualizar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/docencia_actualizar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/docencia_actualizar.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/actualizar_docencia.php"]
casos_uso: ["src\\actividadestudios\\application\\DocenciaActualizar"]
tags: ["actividadestudios", "docencia", "actualizar"]
estado_revision: "revisado"
---

# Docencia Actualizar

Actualiza el dossier `d_docencia_stgr` con la información docente derivada de las actividades
terminadas del periodo indicado. Sustituye a la rama "continuar" del legacy
`apps/actividadestudios/controller/actualizar_docencia.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve el intervalo con `Periodo` (`year`, `periodo`, `empiezamin`, `empiezamax`; por defecto
`periodo=curso_ca`). Busca actividades terminadas cuyo `f_ini` cae en el intervalo y cuyo
`id_tipo_activ` coincide con el patrón stgr de la sesión (`^{sfsv}[123][23]`). Por cada asignatura
con profesor:

1. Calcula curso académico según mes de inicio vs `MesIniStgr`.
2. Determina `tipo` (valor de la asignatura o, si vacío, CA / INV según tipo de actividad).
3. Concatena actas existentes y crea/actualiza `ProfesorDocenciaStgr`.

**Casos particulares**

- **Sin `periodo`:** fuerza `curso_ca`.
- **Actividad sin `f_ini`:** se salta.
- **Asignatura sin profesor:** se salta.
- **Tipo vacío:** CA por defecto; INV si `id_tipo_activ` encaja en `^{sfsv}325`.
- **Docencia ya existente:** actualiza curso/tipo/acta; si no, crea con `getNewId()`.

## Endpoint

- URL: `/src/actividadestudios/docencia_actualizar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/docencia_actualizar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `year` | `string` | application | No | Año del periodo |
| `periodo` | `string` | application | No | Default `curso_ca` |
| `empiezamin` | `string` | application | No | Inicio custom del periodo |
| `empiezamax` | `string` | application | No | Fin custom del periodo |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar('', $txt_rta)` — `data` es string de mensaje (no `"ok"`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data`: mensaje
  `Se ha actualizado la docencia para el periodo: <txt_curso>` (siempre, incluso si no hubo
  actividades; no hay rama de error `_()` en el caso de uso).

## Efectos colaterales

- Crea/actualiza registros `ProfesorDocenciaStgr` para las asignaturas con profesor de las
  actividades terminadas del periodo.

## Errores conocidos

- El caso de uso no devuelve errores `_()` bloqueantes; el mensaje de éxito va en `data`.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`actualizar_docencia.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\DocenciaActualizar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/actualizar_docencia.php`
