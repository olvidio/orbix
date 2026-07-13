---
id: "devel_db_admin.absorber_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/absorber_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/absorber_esquema.php"
entrada: ["post.esquema_del:string", "post.esquema_matriz:string"]
entrada_obligatoria: ["esquema_matriz", "esquema_del"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Delegación origen no encontrada.", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/devel_db_admin/controller/db_absorber_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\AbsorberEsquema"]
tags: ["devel_db_admin", "absorber", "esquema"]
estado_revision: "revisado"
---

# Absorber Esquema

Absorbe un esquema DL (origen) en otro esquema matriz: copia tablas en comun/sv/sv-e, actualiza
referencias de delegación, desactiva la delegación origen y renombra el esquema disuelto a `zz…`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Operación destructiva de unión de delegaciones. Recibe `esquema_matriz` (destino que permanece) y
`esquema_del` (origen que se disuelve). Ejecuta inserciones masivas vía `DBAlterSchema` en
`public`, `publicv` y `publicv-e`, actualiza `da_plazas_dl`, traslados, asistentes, cargos, etc.,
marca la delegación origen como inactiva y renombra los esquemas del origen a prefijo `zz`. Devuelve
líneas informativas y una lista de errores no bloqueantes acumulados durante el proceso.

## Endpoint

- URL: `/src/devel_db_admin/absorber_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/absorber_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema_matriz` | `string` | controller | Si | Esquema base destino (`region-dl`) |
| `esquema_del` | `string` | controller | Si | Esquema origen a absorber |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en frontend).
- Payload en `data`:
  - `lines` (`list<string>`): mensajes de resumen (p. ej. delegación pasada, aviso de borrar esquema viejo).
  - `errores` (`list<string>`): avisos/errores parciales de `DBAlterSchema` y persistencia.

## Errores conocidos

- `Delegación origen no encontrada.`
- `hay un error, no se ha guardado` (con detalle del repositorio)
- Mensajes dinámicos de `DBAlterSchema` y renombre (`renombrarSchema …`)

## Permisos

- Sin control propio en el caso de uso; acceso vía menú de administración DB (`sistema > DB`).

## Casos De Uso

- `src\devel_db_admin\application\AbsorberEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_absorber_esquema.php` (submit desde `db_absorber_esquema_que`).
