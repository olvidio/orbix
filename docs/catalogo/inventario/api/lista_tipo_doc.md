---
id: "inventario.lista_tipo_doc"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_tipo_doc"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_tipo_doc.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/docs_asignar_que.php", "frontend/inventario/controller/equipajes_form_add.php"]
casos_uso: ["src\inventario\application\TipoDocOpcionesData"]
tags: ["inventario", "lista", "tipo", "doc"]
estado_revision: "revisado"
---

# Opciones tipos de documento

Desplegable de tipos de documento para asignación y filtros.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Desplegable de tipos de documento para asignación y filtros.

## Endpoint

- URL: `/src/inventario/lista_tipo_doc`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_tipo_doc.php`

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

src\inventario\application\TipoDocOpcionesData

## Frontend Relacionado

- `frontend/inventario/controller/docs_asignar_que.php`
- `frontend/inventario/controller/equipajes_form_add.php`
