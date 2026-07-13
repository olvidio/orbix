---
id: "profesores.congresos"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/congresos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/profesores/infrastructure/ui/http/controllers/congresos.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_CongresosListaData"
respuesta_data: ["id_tabla:string", "a_cabeceras:array", "a_valores:array"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/congresos.php"]
casos_uso: ["src\\profesores\\application\\CongresosLista"]
tags: ["profesores", "congresos"]
estado_revision: "revisado"
---

# Congresos

Listado global de asistencia a congresos del claustro: por cada profesor activo muestra delegación
(en RSTGR), nombre, tipo, lugar, fechas y organizador.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/congresos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/profesores/infrastructure/ui/http/controllers/congresos.php`

## Entrada

Sin parámetros POST.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `success: true`, `data` con tabla lista.
- `id_tabla`: `tabla_congreso`.
- `a_cabeceras`: columnas 1–7 (`dl` solo en RSTGR; apellidos/nombre, tipo, lugar, inicio, fin,
  organiza).
- `a_valores`: filas indexadas con datos de cada congreso por profesor.

## Objetivo funcional

Consulta de solo lectura del registro de congresos del profesorado STGR.

## Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']` (menú `stgr2`).

## Casos De Uso

- `src\profesores\application\CongresosLista`

## Frontend Relacionado

- `frontend/profesores/controller/congresos.php` — renderiza `Lista` con `congresos.phtml`.
- Linaje: `apps/profesores/controller/congresos.php`.
