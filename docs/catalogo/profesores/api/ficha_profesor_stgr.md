---
id: "profesores.ficha_profesor_stgr"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/ficha_profesor_stgr"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/profesores/infrastructure/ui/http/controllers/ficha_profesor_stgr.php"
entrada: ["post.depende:string", "post.id_nom:integer", "post.id_tabla:string", "post.obj_pau:string", "post.permiso:string", "post.print:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/ficha_profesor_stgr.php"]
casos_uso: ["src\\profesores\\application\\FichaProfesorStgr"]
tags: ["profesores", "ficha", "profesor", "stgr"]
estado_revision: "generado"
---

# Ficha Profesor Stgr

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/ficha_profesor_stgr`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/profesores/infrastructure/ui/http/controllers/ficha_profesor_stgr.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `depende` | `string` | controller | No | controller |
| `id_nom` | `integer` | controller | No | controller |
| `id_tabla` | `string` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |
| `permiso` | `string` | controller | No | controller |
| `print` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\profesores\application\FichaProfesorStgr`

## Frontend Relacionado

- `frontend/profesores/controller/ficha_profesor_stgr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.