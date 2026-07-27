---
id: "notas.acta_ver_add_persona_form_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_ver_add_persona_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/acta_ver_add_persona_form_data.php"
entrada: ["post.acta:string"]
entrada_obligatoria: ["acta"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta el acta", "No se encuentra el acta", "El acta no tiene asignatura", "No se han podido cargar las personas publicadas"]
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaVerAddPersonaFormData"]
tags: ["notas", "acta", "ver", "add", "persona", "form"]
estado_revision: "revisado"
---

# Acta Ver Add Persona Form Data

Datos del formulario «añadir alumno + nota» en `acta_ver` (caso B: acta abierta desde listado, no embebida en actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_ver_add_persona_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_ver_add_persona_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | application | Sí | Número/id de acta |

## Salida

- Helper: `ContestarJson::enviar`
- Si hay `error` y `puede_anadir` falso → envelope de error (`mensaje` = error, sin payload útil).
- Éxito: `data` con doble `JSON.parse`:

| Clave | Tipo | Descripción |
|-------|------|-------------|
| `puede_anadir` | `bool` | `true` si se puede mostrar el form |
| `acta` | `string` | Eco del acta |
| `f_acta` | `string` | Fecha local del acta |
| `id_asignatura` | `int` | Del acta |
| `id_nivel` | `int` | De la asignatura (0 si no hay) |
| `id_activ` | `int` | Del acta |
| `nota_max_default` | `int` | `oConfig->getNotaMax()` |
| `opciones_personas` | `map id→etiqueta` | Candidatos al desplegable |

## Objetivo funcional

Prepara el desplegable de alumnos elegibles para añadir nota al acta.

## Casos particulares

- Candidatos = personas DL locales + publicadas para mi DL (`publicado_para` vigente con clave = `ConfigGlobal::mi_dele()`; **no** incluye `*` / de paso).
- Filtro base: `situacion=A` y `nivel_stgr != R` (excluye Repaso).
- Excluye quien ya tiene nota en la **asignatura** del acta (cualquier acta de esa asignatura).
- Publicadas: etiqueta con sufijo ` (dl)` si tienen delegación; se omiten ids ya listados desde DL.
- Fallo al leer publicadas → `RuntimeException` con mensaje gettext `No se han podido cargar las personas publicadas: …` (puede abortar la request).

## Permisos

- Sin `perm_*` en el caso de uso. Frontend solo pide el form si `$permiso === 3` y el PDF no pone `readonly` (acta no firmada).

## Errores conocidos

- `Falta el acta`
- `No se encuentra el acta`
- `El acta no tiene asignatura`
- `No se han podido cargar las personas publicadas` (vía excepción)

## Casos De Uso

- `src\notas\application\ActaVerAddPersonaFormData`

## Frontend Relacionado

- `frontend/notas/controller/acta_ver.php` (bloque `$puede_anadir_persona`).
