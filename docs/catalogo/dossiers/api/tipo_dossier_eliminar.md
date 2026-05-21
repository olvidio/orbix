---
id: "dossiers.tipo_dossier_eliminar"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/tipo_dossier_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_eliminar.php"
entrada: ["post.id_tipo_dossier:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_tipo_dossier", "Hay un error, no se ha eliminado."]
frontend_referencias: []
casos_uso: ["src\\dossiers\\application\\TipoDossierEliminar"]
tags: ["dossiers", "tipo", "dossier", "eliminar"]
estado_revision: "generado"
---

# Tipo Dossier Eliminar

Elimina un `TipoDossier`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dossiers/tipo_dossier_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_dossier` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un `TipoDossier`.
- Sustituye al case `eliminar` del antiguo `apps/dossiers/controller/perm_dossier_update.php`.

## Errores conocidos

- `falta id_tipo_dossier`
- `Hay un error, no se ha eliminado.`

## Casos De Uso

- `src\dossiers\application\TipoDossierEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.