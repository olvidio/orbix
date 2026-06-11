---
id: "zonassacd.zona_ctr_update"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_update.php"
entrada: ["post.id_zona_new:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_ctr_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrUpdate"]
tags: ["zonassacd", "zona", "ctr", "update"]
estado_revision: "revisado"
---

# Zona Ctr Update

Reasigna los centros marcados (`sel[]`, ids de ubi) a la zona `id_zona_new`.

- El primer digito del `id_ubi` decide el repositorio: `1` → centros dl
  (`CentroDlRepository`), otro → centros sf (`CentroEllasRepository`).
- `id_zona_new = 'no'`: deja el centro sin zona (`id_zona = NULL`).
- Ids vacios o centros inexistentes se ignoran silenciosamente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_ctr_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona_new` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Errores parciales de persistencia van acumulados en `mensaje`.

## Permisos

- El caso de uso no valida permisos; el control de acceso está en la UI
  (checkboxes y boton asignar solo con permiso oficina `des` o `vcsd`).

## Casos De Uso

- `src\zonassacd\application\ZonaCtrUpdate`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_ctr_update_ajax.php`

## Revision Manual

- Revisado jun 2026 (lectura de `ZonaCtrUpdate::execute`).
- Pendiente: ejemplos reales de request/response.