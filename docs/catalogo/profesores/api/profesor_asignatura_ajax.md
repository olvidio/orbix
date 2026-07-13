---
id: "profesores.profesor_asignatura_ajax"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/profesor_asignatura_ajax"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_ajax.php"
entrada: ["post.id_asignatura:integer"]
entrada_obligatoria: ["post.id_asignatura"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_ProfesoresAsignaturaListaData"
respuesta_data: ["id_tabla:string", "a_cabeceras:array", "a_valores:array", "a_botones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/profesor_asignatura_ajax.php"]
casos_uso: ["src\\profesores\\application\\ProfesoresAsignaturaLista"]
tags: ["profesores", "profesor", "asignatura", "ajax"]
estado_revision: "revisado"
---

# Profesor Asignatura Ajax

Tabla de profesores habilitados para una asignatura: del departamento y por ampliación, con centro,
historial de docencia y contacto.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/profesor_asignatura_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_asignatura` | `integer` | controller | Sí | Disparado al cambiar el desplegable en `profesor_asignatura_que` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `success: true`, `data` con tabla lista.
- `id_tabla`: `list_profe_asig`.
- `a_cabeceras`: apellidos/nombre (clickFormatter), centro, docencia, teléfono, mail.
- `a_valores`: filas con `sel`=`id_nom`; columna 1 como `{ira, valor}`; docencia como cursos
  `YYYY-YYYY` concatenados; teléfonos y mails agregados.
- `a_botones`: vacío.
- Dos bloques: profesores de departamento y de ampliación (`ProfesorAsignaturaService`).

## Objetivo funcional

Listado AJAX al elegir asignatura. Ayuda a seleccionar profesor antes de asignar en el dossier del
curso (`actividadestudios`).

## Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\profesores\application\ProfesoresAsignaturaLista`

## Frontend Relacionado

- `frontend/profesores/controller/profesor_asignatura_ajax.php` — fragmento HTML vía
  `AjaxJsonSupport::html`.
- Linaje: `apps/profesores/controller/profesor_asignatura_ajax.php`.
