---
id: "zonassacd.zona_ctr_lista"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_lista.php"
entrada: ["post.id_zona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_ctr_lista_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrLista"]
tags: ["zonassacd", "zona", "ctr", "lista"]
estado_revision: "generado"
---

# Zona Ctr Lista

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_ctr_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

## Casos De Uso

- `src\zonassacd\application\ZonaCtrLista`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_ctr_lista_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.