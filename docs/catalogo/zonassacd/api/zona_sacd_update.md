---
id: "zonassacd.zona_sacd_update"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php"
entrada: ["post.acumular:integer", "post.id_zona:string", "post.id_zona_new:string", "post.sel:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdUpdate"]
tags: ["zonassacd", "zona", "sacd", "update"]
estado_revision: "revisado"
errores: ["hay un error, no se ha guardado", "hay un error, no se ha eliminado"]
---

# Zona Sacd Update

Mueve, crea o elimina asignaciones sacd↔zona para sel[]. acumular=1 cambia zona propia; acumular=2 añade/quita asignación iglesia/cgi no propia. id_zona_new=no elimina; vacío no hace nada.

Linaje: Case update del legacy zona_sacd_ajax.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Mueve, crea o elimina asignaciones sacd↔zona para sel[]. acumular=1 cambia zona propia; acumular=2 añade/quita asignación iglesia/cgi no propia. id_zona_new=no elimina; vacío no hace nada.

## Endpoint

- URL: `/src/zonassacd/zona_sacd_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acumular` | `integer` | application | No | |
| `id_zona` | `string` | application | No | |
| `id_zona_new` | `string` | application | No | |
| `sel` | `array` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`. Errores parciales en `mensaje`.

## Errores conocidos
- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

## Permisos

Sin validación en caso de uso; UI restringe a perm_des.

## Casos De Uso

- `src\zonassacd\application\ZonaSacdUpdate`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_sacd_update_ajax.php"]`).
