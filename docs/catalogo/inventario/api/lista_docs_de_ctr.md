---
id: "inventario.lista_docs_de_ctr"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_de_ctr"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_de_ctr.php"
entrada: ["post.id_lugar:integer", "post.id_ubi:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/inventario/controller/traslado_doc_lista.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "de", "ctr"]
estado_revision: "generado"
---

# Lista Docs De Ctr

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/lista_docs_de_ctr`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_ctr.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_lugar` | `integer` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/inventario/controller/traslado_doc_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.