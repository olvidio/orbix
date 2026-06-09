---
id: "menus.menus_importar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_importar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_importar.php"
entrada: ["post.id_template_menu:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/menus/controller/menus_importar_form.php"]
casos_uso: []
tags: ["menus", "importar"]
estado_revision: "generado"
---

# Menus Importar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menus_importar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_importar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_template_menu` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/menus/controller/menus_importar_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.