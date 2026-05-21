---
id: "ubis.ubis_buscar_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_buscar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_buscar_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisBuscarOpcionesDataData"
respuesta_data: ["opciones_region:array", "opciones_tipo_ctr:array", "opciones_tipo_casa:array", "opciones_pais:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_buscar.php"]
casos_uso: ["src\\ubis\\application\\UbisBuscarOpcionesData"]
tags: ["ubis", "buscar", "data"]
estado_revision: "generado"
---

# Ubis Buscar Data

Opciones de formulario para frontend/ubis/controller/ubis_buscar.php

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_buscar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_buscar_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_UbisBuscarOpcionesDataData`):
  - `opciones_region` (`array`)
  - `opciones_tipo_ctr` (`array`)
  - `opciones_tipo_casa` (`array`)
  - `opciones_pais` (`array`)

## Casos De Uso

- `src\ubis\application\UbisBuscarOpcionesData`

## Frontend Relacionado

- `frontend/ubis/controller/ubis_buscar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.