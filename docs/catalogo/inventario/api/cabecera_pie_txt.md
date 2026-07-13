---
id: "inventario.cabecera_pie_txt"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/cabecera_pie_txt"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/inventario/infrastructure/ui/http/controllers/cabecera_pie_txt.php"
entrada: ["post.id_equipaje:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/cabecera_pie_txt.php", "frontend/inventario/controller/equipajes_imprimir.php"]
casos_uso: []
tags: ["inventario", "cabecera", "pie", "txt"]
estado_revision: "revisado"
---

# Cabecera y pie de impresión de equipajes

Devuelve los textos de cabecera (A/B), firma y pie para imprimir equipajes. Prioriza textos propios del equipaje (`id_equipaje`); si faltan, lee defaults de `data/inventario/cabecera_pie_textos.ini`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve los textos de cabecera (A/B), firma y pie para imprimir equipajes. Prioriza textos propios del equipaje (`id_equipaje`); si faltan, lee defaults de `data/inventario/cabecera_pie_textos.ini`.

## Endpoint

- URL: `/src/inventario/cabecera_pie_txt`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/cabecera_pie_txt.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{cabecera, cabeceraB, firma, pie}`. Doble `JSON.parse` en cliente.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/cabecera_pie_txt.php`
- `frontend/inventario/controller/equipajes_imprimir.php`
