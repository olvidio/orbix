---
id: "profesores.profesor_asignatura_que"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/profesor_asignatura_que"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_que.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_ProfesorAsignaturaQueDataData"
respuesta_data: ["aOpciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/profesor_asignatura_que.php"]
casos_uso: ["src\\profesores\\application\\ProfesorAsignaturaQueData"]
tags: ["profesores", "profesor", "asignatura", "que"]
estado_revision: "revisado"
---

# Profesor Asignatura Que

Opciones del desplegable de asignatura para consultar profesores habilitados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/profesor_asignatura_que`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_que.php`

## Entrada

Sin parámetros POST.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `success: true`, `data.aOpciones` — mapa id→nombre de asignaturas con separadores de
  departamento (`getArrayAsignaturasConSeparador`).

## Objetivo funcional

Carga inicial del formulario de consulta «profesor para asignatura». El cambio de asignatura
dispara AJAX a `profesor_asignatura_ajax`.

## Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\profesores\application\ProfesorAsignaturaQueData`

## Frontend Relacionado

- `frontend/profesores/controller/profesor_asignatura_que.php` — desplegable + hash hacia
  `profesor_asignatura_ajax.php`.
