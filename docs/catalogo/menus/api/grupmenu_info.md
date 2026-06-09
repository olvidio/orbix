---
id: "menus.grupmenu_info"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_info"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_info.php"
entrada: ["post.id_grupmenu:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/menus/controller/grupmenu_form.php"]
casos_uso: []
tags: ["menus", "grupmenu", "info"]
estado_revision: "generado"
---

# Grupmenu Info

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/grupmenu_info`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_grupmenu` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/menus/controller/grupmenu_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.