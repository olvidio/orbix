---
id: "asistentes.asistente_guardar"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/asistente_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/asistente_guardar.php"
entrada: ["post.cfi:string", "post.cfi_con:integer", "post.encargo:string", "post.est_ok:string", "post.falta:string", "post.id_activ:integer", "post.id_activ_old:integer", "post.id_nom:integer", "post.id_pau:integer", "post.mod:string", "post.observ:string", "post.observ_est:string", "post.pau:string", "post.plaza:integer", "post.propietario:string", "post.propio:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom", "falta id_activ_old", "los datos de asistencia los modifica la dl del asistente", "hay un error, no se ha guardado"]
frontend_referencias: []
casos_uso: ["src\\asistentes\\application\\AsistenteGuardar"]
tags: ["asistentes", "asistente", "guardar"]
estado_revision: "generado"
---

# Asistente Guardar

Crea, edita o mueve un `Asistente`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/asistente_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `cfi` | `string` | application | No | application |
| `cfi_con` | `integer` | application | No | application |
| `encargo` | `string` | application | No | application |
| `est_ok` | `string` | application | No | application |
| `falta` | `string` | application | No | application |
| `id_activ` | `integer` | application | No | application |
| `id_activ_old` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `mod` | `string` | application | No | application |
| `observ` | `string` | application | No | application |
| `observ_est` | `string` | application | No | application |
| `pau` | `string` | application | No | application |
| `plaza` | `integer` | application | No | application |
| `propietario` | `string` | application | No | application |
| `propio` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Crea, edita o mueve un `Asistente`.
- Sustituye a los cases `nuevo`, `editar` y `mover` del antiguo `apps/asistentes/controller/update_3101.php`: - `mod === 'nuevo'`: abre el dossier 1301 y guarda el asistente.
- - `mod === 'editar'`: guarda el asistente existente (valida `perm_modificar`).
- - `mod === 'mover'`: elimina el asistente origen (`id_activ_old`) y guarda el nuevo en `id_activ`.

## Errores conocidos

- `faltan parametros id_activ / id_nom`
- `falta id_activ_old`
- `los datos de asistencia los modifica la dl del asistente`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\asistentes\application\AsistenteGuardar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.