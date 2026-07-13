---
id: "zonassacd.zona_ctr_update"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_update.php"
entrada: ["post.id_zona_new:string", "post.sel:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_ctr_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrUpdate"]
tags: ["zonassacd", "zona", "ctr", "update"]
estado_revision: "revisado"
errores: ["hay un error, no se ha guardado."]
---

# Zona Ctr Update

Reasigna centros marcados (sel=id_ubi) a id_zona_new. Primer dígito del id_ubi elige repo dl (1) o sf. id_zona_new=no deja sin zona.

Linaje: Case update del legacy zona_ctr_ajax.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Reasigna centros marcados (sel=id_ubi) a id_zona_new. Primer dígito del id_ubi elige repo dl (1) o sf. id_zona_new=no deja sin zona.

## Endpoint

- URL: `/src/zonassacd/zona_ctr_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona_new` | `string` | application | No | |
| `sel` | `array` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`. Errores parciales en `mensaje`.

## Errores conocidos
- `hay un error, no se ha guardado.`

## Permisos

Sin validación en caso de uso; UI restringe a perm_des.

## Casos De Uso

- `src\zonassacd\application\ZonaCtrUpdate`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_ctr_update_ajax.php"]`).
