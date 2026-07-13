---
id: "notas.comprobar_notas_page_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/comprobar_notas_page_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/comprobar_notas_page_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Excepciones SQL/runtime en `mensaje`"]
frontend_referencias: ["frontend/notas/controller/comprobar_notas.php"]
casos_uso: []
tags: ["notas", "comprobar", "page", "data"]
estado_revision: "revisado"
---

# Comprobar Notas Page Data

Ejecuta comprobaciones SQL y devuelve HTML de resultados.

HTML de {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/comprobar_notas_page_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/comprobar_notas_page_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- `{html: string}` en `data` (doble `JSON.parse`).

## Objetivo funcional

Incluye `comprobar_notas_page_body.inc.php`; parámetros vía POST (`id_tabla` n/a).

## Permisos

- Menú ESTUDIOS > Comprobar datos n/agd.

## Errores conocidos

- `Excepciones SQL/runtime en `mensaje``

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/notas/controller/comprobar_notas.php`.