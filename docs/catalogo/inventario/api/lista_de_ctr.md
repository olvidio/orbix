---
id: "inventario.lista_de_ctr"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_de_ctr"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_de_ctr.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/traslado_doc_que.php"]
casos_uso: []
tags: ["inventario", "lista", "de", "ctr"]
estado_revision: "revisado"
---

# Centros con inventario

Lista centros (ubi inventario) para selección en traslado o consulta.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista centros (ubi inventario) para selección en traslado o consulta.

## Endpoint

- URL: `/src/inventario/lista_de_ctr`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_de_ctr.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| *(ninguno)* | — | — | — | Sin parámetros en controller |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_opciones}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/traslado_doc_que.php`
