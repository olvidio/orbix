---
id: "ubis.teleco_desc_lista"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_desc_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_desc_lista.php"
entrada: ["post.id_tipo_teleco:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/teleco_desc_lista_ajax.php"]
casos_uso: ["src\\ubis\\application\\TelecoDescLista"]
tags: ["ubis", "teleco", "desc", "lista"]
estado_revision: "generado"
---

# Teleco Desc Lista

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/teleco_desc_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_desc_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_teleco` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\TelecoDescLista`

## Frontend Relacionado

- `frontend/ubis/controller/teleco_desc_lista_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.