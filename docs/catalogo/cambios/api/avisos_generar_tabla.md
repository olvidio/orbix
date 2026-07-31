---
id: "cambios.avisos_generar_tabla"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/avisos_generar_tabla"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/avisos_generar_tabla.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Algo falla", "Se han detectado incidencias al generar la tabla de avisos"]
frontend_referencias: []
casos_uso: ["src\\cambios\\application\\AvisosGenerarTabla"]
tags: ["cambios", "avisos", "generar", "tabla", "batch"]
estado_revision: "revisado"
---

# Avisos Generar Tabla

Proceso batch que recorre los `Cambio` pendientes, aplica preferencias de aviso del usuario
(`CambioUsuarioObjetoPref` / `CambioUsuarioPropiedadPref`) y permisos de actividad, y anota filas
en `CambioUsuario` (avisos pendientes de consultar/purgar en `avisos_generar`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

1. Si el módulo `cambios` no está instalado → no-op (`err_fila` vacío, sin bucle infinito).
2. Crea PID de proceso (`Avisos::crear_pid`), borra cambios antiguos (`borrarCambios('P1Y')`).
3. Por cada cambio nuevo: normaliza objeto (`Actividad*`→`Actividad`, `Asistente*`→`Asistente`),
   resuelve ámbito de permiso (`afecta`), filtra por DL propia vs importada, matching de fases/
   status y propiedades, y apunta aviso (`fn_apuntar`) cuando corresponde; marca el cambio
   `anotado()`.
4. Repite mientras queden cambios nuevos; si el contador no baja y no son solo omitidos →
   `bucle_infinito`.
5. Borra PID al terminar.

Invocaciones: menú web (esta URL), cron / `Cambio::generarTabla()` vía driver CLI
(`src/cambios/infrastructure/cli/avisos_generar_tabla.php`). El controller HTTP solo hace
`require` del driver; en web el driver responde `ContestarJson`.

## Endpoint

- URL: `/src/cambios/avisos_generar_tabla`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion` (proceso batch con efectos en BD; no es lista/form data)
- Controller: `src/cambios/infrastructure/ui/http/controllers/avisos_generar_tabla.php`
  (delega a `src/cambios/infrastructure/cli/avisos_generar_tabla.php`)

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno vía POST en web)_ | | | | Usuario/esquema: `ConfigGlobal::mi_usuario()` / `mi_region_dl()` |
| CLI `argv` | varios | CLI | Cond. | username, password, DIRWEB, DOCUMENT_ROOT, env UBICACION/ESQUEMA/… |

## Salida

- Helper web: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito sin incidencias: `data: { html: "<p>Tabla de avisos generada.</p>…" }`.
- Éxito con filas de error recuperables: `data: { html: alert + tabla schema/id_item/id_activ/motivo }`.
- Bucle infinito: `mensaje` = `Algo falla`, `data` vacío.
- Excepción `RuntimeException`: `mensaje` = `getMessage()`, `data` vacío.
- CLI: imprime HTML de errores a stdout; exit 1 si bucle infinito.

## Casos particulares

- Actividad inexistente → omite el cambio (log + fila HTML); no lo marca anotado; puede quedar
  pendiente en cola.
- Cambio de otra DL sin actividad importada → `anotado()` sin apuntar.
- Sin preferencias de objeto coincidentes → `anotado()` sin apuntar.
- Objeto `Asistente` + propiedad `id_nom` de un sacd → permiso `asistentesSacd` en lugar de
  `asistentes`.
- Matching de fase: con/sin módulo `procesos`, `aviso_on`/`aviso_off`, `aviso_outdate`, permiso
  `ocupado` sobre la actividad.
- Si tras un ciclo el número de pendientes no baja y todos son omitidos → termina OK (log); si no
  → `bucle_infinito`.
- Índice de menú en dumps legacy puede apuntar aún a la ruta CLI; la URL canónica tras migración
  es `/src/cambios/avisos_generar_tabla`.

## Permisos

- Sin `perm_*` en el caso de uso. Entrada de menú restringida a perfiles con acceso a
  «generar tabla avisos» (usuarios web / ADMIN LOCAL). El matching interno usa permisos de
  actividad del **usuario destinatario** del aviso (`PermisosActividades`).

## Errores conocidos

- `Algo falla` (bucle infinito / cola que no progresa)
- Mensaje de `RuntimeException` (p. ej. fallo de PID u otras)
- HTML de incidencias (no es `mensaje` del envelope): filas por cambio no procesado
  (`actividad N no encontrada`, excepciones de permiso, etc.)
- Texto CLI/tabla: `error al apuntar cambio usuario en` + host/fecha

## Casos De Uso

- `src\cambios\application\AvisosGenerarTabla`

## Frontend Relacionado

- Sin pantalla FE dedicada: el menú «generar tabla avisos» invoca la URL src y muestra `data.html`.
- Relacionado: tras generar, el usuario consulta avisos en `frontend/cambios/controller/avisos_generar.php`.
