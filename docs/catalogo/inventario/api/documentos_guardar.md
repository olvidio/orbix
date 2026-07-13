---
id: "inventario.documentos_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/documentos_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/documentos_guardar.php"
entrada: ["post.documentos:string", "post.chk_f_recibido:string", "post.f_recibido:string", "post.chk_f_asignado:string", "post.f_asignado:string", "post.chk_eliminado:string", "post.eliminado:integer", "post.chk_f_eliminado:string", "post.f_eliminado:string", "post.chk_num_ini:string", "post.num_ini:string", "post.chk_num_fin:string", "post.num_fin:string"]
entrada_obligatoria: ["documentos"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado", "No ha seleccionado ningún documento"]
frontend_referencias: ["frontend/inventario/controller/documentos_form.php"]
casos_uso: []
tags: ["inventario", "documentos", "guardar"]
estado_revision: "revisado"
---

# Actualización masiva de documentos

Actualiza en lote documentos identificados en `documentos` (claves base64 con JSON `{id_doc}` separadas por `#`). Solo modifica campos cuyo checkbox `chk_*` viene activo: fechas recibido/asignado/eliminado, flag eliminado, rango num_ini/num_fin.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza en lote documentos identificados en `documentos` (claves base64 con JSON `{id_doc}` separadas por `#`). Solo modifica campos cuyo checkbox `chk_*` viene activo: fechas recibido/asignado/eliminado, flag eliminado, rango num_ini/num_fin.

## Endpoint

- URL: `/src/inventario/documentos_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/documentos_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `documentos` | `string` | POST | Si | |
| `chk_f_recibido` | `string` | POST | No | |
| `f_recibido` | `string` | POST | No | |
| `chk_f_asignado` | `string` | POST | No | |
| `f_asignado` | `string` | POST | No | |
| `chk_eliminado` | `string` | POST | No | |
| `eliminado` | `integer` | POST | No | |
| `chk_f_eliminado` | `string` | POST | No | |
| `f_eliminado` | `string` | POST | No | |
| `chk_num_ini` | `string` | POST | No | |
| `num_ini` | `string` | POST | No | |
| `chk_num_fin` | `string` | POST | No | |
| `num_fin` | `string` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `data: "ok"`.

## Errores conocidos

  - `hay un error, no se ha guardado`
  - `No ha seleccionado ningún documento`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/documentos_form.php`
