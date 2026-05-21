---
id: "dossiers.tipo_dossier_guardar"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/tipo_dossier_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_guardar.php"
entrada: ["post.Permiso_escritura:array", "post.Permiso_lectura:array", "post.app:string", "post.campo_to:string", "post.class:string", "post.codigo:string", "post.depende_modificar:string", "post.descripcion:string", "post.id_tipo_dossier:integer", "post.id_tipo_dossier_rel:integer", "post.tabla_from:string", "post.tabla_to:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_tipo_dossier", "Hay un error, no se ha guardado."]
frontend_referencias: []
casos_uso: ["src\\dossiers\\application\\TipoDossierGuardar"]
tags: ["dossiers", "tipo", "dossier", "guardar"]
estado_revision: "generado"
---

# Tipo Dossier Guardar

Guarda los cambios a un `TipoDossier`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dossiers/tipo_dossier_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `Permiso_escritura` | `array` | application | No | application |
| `Permiso_lectura` | `array` | application | No | application |
| `app` | `string` | application | No | application |
| `campo_to` | `string` | application | No | application |
| `class` | `string` | application | No | application |
| `codigo` | `string` | application | No | application |
| `depende_modificar` | `string` | application | No | application |
| `descripcion` | `string` | application | No | application |
| `id_tipo_dossier` | `integer` | application | No | application |
| `id_tipo_dossier_rel` | `integer` | application | No | application |
| `tabla_from` | `string` | application | No | application |
| `tabla_to` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Guarda los cambios a un `TipoDossier`.
- Sustituye al case `guardar` del antiguo `apps/dossiers/controller/perm_dossier_update.php`.

## Errores conocidos

- `falta id_tipo_dossier`
- `Hay un error, no se ha guardado.`

## Casos De Uso

- `src\dossiers\application\TipoDossierGuardar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.