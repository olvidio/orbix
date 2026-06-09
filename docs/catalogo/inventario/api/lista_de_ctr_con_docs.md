---
id: "inventario.lista_de_ctr_con_docs"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_de_ctr_con_docs"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_de_ctr_con_docs.php"
entrada: ["post.id_tipo_doc:integer", "post.inventario:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/inventario/controller/doc_de_ctr.php"]
casos_uso: []
tags: ["inventario", "lista", "de", "ctr", "con", "docs"]
estado_revision: "generado"
---

# Lista De Ctr Con Docs

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/lista_de_ctr_con_docs`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_de_ctr_con_docs.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_doc` | `integer` | controller | No | controller |
| `inventario` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/inventario/controller/doc_de_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.