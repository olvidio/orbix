---
id: "notas.asignaturas_pendientes_resumen_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/asignaturas_pendientes_resumen_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/asignaturas_pendientes_resumen_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_AsignaturasPendientesResumenDataData"
respuesta_data: ["pendientes:array"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/asignaturas_pendientes_resumen.php"]
casos_uso: ["src\\notas\\application\\AsignaturasPendientesResumenData"]
tags: ["notas", "asignaturas", "pendientes", "resumen", "data"]
estado_revision: "revisado"
---

# Asignaturas Pendientes Resumen Data

Resumen: número de alumnos por asignatura pendiente.

Resumen: número de alumnos con cada asignatura pendiente, desglosado por tramo (nb, nc1, nc2, n total, ab, ac1, ac2, a total). Sucesor de la lógica embebida en `frontend/notas/controller/asignaturas_pendientes_resumen.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/asignaturas_pendientes_resumen_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/asignaturas_pendientes_resumen_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Tabla resumen en `data`.
- Payload en `data` (schema `notas_AsignaturasPendientesResumenDataData`):
  - `pendientes` (`array`)

## Objetivo funcional

Agrega conteos por asignatura para la pantalla resumen.

## Permisos

- Menú resumen pendientes.

## Casos De Uso

- `src\notas\application\AsignaturasPendientesResumenData`

## Frontend Relacionado

- `frontend/notas/controller/asignaturas_pendientes_resumen.php`.