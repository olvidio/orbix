---
id: "menus.menus_exportar_ref_a_ficheros"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_exportar_ref_a_ficheros"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_exportar_ref_a_ficheros.php"
entrada: ["post.accion:string"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["menus", "exportar", "ref", "a", "ficheros"]
estado_revision: "generado"
---

# Menus Exportar Ref A Ficheros

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menus_exportar_ref_a_ficheros`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_exportar_ref_a_ficheros.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `accion` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.