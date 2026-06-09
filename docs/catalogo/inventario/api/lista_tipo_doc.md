---
id: "inventario.lista_tipo_doc"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_tipo_doc"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_tipo_doc.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "inventario_TipoDocOpcionesDataData"
respuesta_data: ["a_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/inventario/controller/docs_asignar_que.php", "frontend/inventario/controller/equipajes_form_add.php"]
casos_uso: ["src\\inventario\\application\\TipoDocOpcionesData"]
tags: ["inventario", "lista", "tipo", "doc"]
estado_revision: "generado"
---

# Lista Tipo Doc

Opciones del desplegable de tipos de documento (`lista_tipo_doc.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/lista_tipo_doc`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_tipo_doc.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `inventario_TipoDocOpcionesDataData`):
  - `a_opciones` (`array`)

## Casos De Uso

- `src\inventario\application\TipoDocOpcionesData`

## Frontend Relacionado

- `frontend/inventario/controller/docs_asignar_que.php`
- `frontend/inventario/controller/equipajes_form_add.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.