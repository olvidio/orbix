---
id: "inventario.lista_colecciones"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_colecciones"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_colecciones.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/domain/ListaAgrupar.php"]
casos_uso: ["src\inventario\application\ColeccionesOpcionesData"]
tags: ["inventario", "lista", "colecciones"]
estado_revision: "revisado"
---

# Opciones de colecciones

Desplegable de colecciones de documentos para filtros/agrupación en listados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Desplegable de colecciones de documentos para filtros/agrupación en listados.

## Endpoint

- URL: `/src/inventario/lista_colecciones`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_colecciones.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| *(ninguno)* | — | — | — | Sin parámetros en controller |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_opciones}` (id → nombre).

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

src\inventario\application\ColeccionesOpcionesData

## Frontend Relacionado

- `frontend/inventario/domain/ListaAgrupar.php`
