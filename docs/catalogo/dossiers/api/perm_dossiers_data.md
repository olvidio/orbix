---
id: "dossiers.perm_dossiers_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/perm_dossiers_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/perm_dossiers_data.php"
entrada: ["post.tipo:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dossiers_PermDossiersListaDataData"
respuesta_data: ["a_filas:list<array<string, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/perm_dossiers.php"]
casos_uso: ["src\\dossiers\\application\\PermDossiersListaData"]
tags: ["dossiers", "perm", "data"]
estado_revision: "generado"
---

# Perm Dossiers Data

Listado de tipos de dossier para pantalla de permisos. `pagina_link_spec` se firma en `perm_dossiers_data.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dossiers/perm_dossiers_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/perm_dossiers_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `dossiers_PermDossiersListaDataData`):
  - `a_filas` (`list<array<string, mixed>>`)

## Efectos colaterales

- Listado de tipos de dossier para pantalla de permisos.
- `pagina_link_spec` se firma en `perm_dossiers_data.php`.

## Casos De Uso

- `src\dossiers\application\PermDossiersListaData`

## Frontend Relacionado

- `frontend/dossiers/controller/perm_dossiers.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.