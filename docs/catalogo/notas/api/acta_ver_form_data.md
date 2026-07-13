---
id: "notas.acta_ver_form_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_ver_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/acta_ver_form_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro el profesor."]
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaVerFormData"]
tags: ["notas", "acta", "ver", "form", "data"]
estado_revision: "revisado"
---

# Acta Ver Form Data

Estado del formulario de cabecera de acta (`acta_ver`).

Estado del formulario `acta_ver` (sin HashFront ni vistas).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_ver_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_ver_form_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Objetivo funcional

Ramas: ver acta existente, alta (`notas=nuevo`/`mod=nueva`), edición con datos POST. Devuelve cabecera, tribunal, URLs de mutación, `permiso`, `has_pdf`, `warn_no_id_activ`.

## Permisos

- `scope_permiso` (default 3); forzado 0 en `rstgr`.

## Errores conocidos

- `No encuentro el profesor.`

## Casos De Uso

- `src\notas\application\ActaVerFormData`

## Frontend Relacionado

- `frontend/notas/controller/acta_ver.php`; también embebido desde `actividadestudios` (`acta_notas`).