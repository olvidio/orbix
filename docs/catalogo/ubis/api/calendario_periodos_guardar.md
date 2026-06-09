---
id: "ubis.calendario_periodos_guardar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_guardar.php"
entrada: ["post.f_fin:string", "post.f_ini:string", "post.id_item:integer", "post.id_ubi:integer", "post.sfsv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/ubis/controller/calendario_periodos.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodoGuardar"]
tags: ["ubis", "calendario", "periodos", "guardar"]
estado_revision: "generado"
---

# Calendario Periodos Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/calendario_periodos_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_fin` | `string` | controller | No | controller |
| `f_ini` | `string` | controller | No | controller |
| `id_item` | `integer` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `sfsv` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `hay un error, no se ha guardado`

## Casos De Uso

- `src\ubis\application\CalendarioPeriodoGuardar`

## Frontend Relacionado

- `frontend/ubis/controller/calendario_periodos.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.