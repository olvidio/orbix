---
id: "actividadplazas.peticiones_guardar"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_guardar.php"
entrada: ["post.actividades:array", "post.id_nom:integer", "post.sactividad:string"]
entrada_obligatoria: ["id_nom", "sactividad"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_nom / sactividad", "hay un error, no se han guardado todas las peticiones"]
frontend_referencias: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesGuardar"]
tags: ["actividadplazas", "peticiones", "guardar"]
estado_revision: "revisado"
---

# Peticiones Guardar

Guarda las peticiones de plaza de una persona para un tipo de actividad: borra todas las anteriores y
crea las nuevas en el orden recibido.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Valida `id_nom` y `sactividad`.
- Elimina todas las peticiones existentes de esa persona + tipo.
- Recorre `actividades` (lista ordenada de `id_activ`, ignorando ceros) y crea/actualiza cada
  `PlazaPeticion` fijando `orden` incremental y `tipo = sactividad`.
- Si falla el guardado de alguna, corta y devuelve error.

## Endpoint

- URL: `/src/actividadplazas/peticiones_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Si | Persona propietaria de las peticiones |
| `sactividad` | `string` | controller | Si | Tipo de actividad (`ca`/`cv`/`crt`) |
| `actividades` | `array` | controller | No | Lista ordenada de `id_activ` (vía `inputStringList`); el orden define la prioridad |

## Salida

- Helper: `ContestarJson::enviar` (`enviar($mensaje, 'ok')`: string vacío en éxito, mensaje en error).
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Borra todas las peticiones previas de `{id_nom, tipo}` antes de crear las nuevas.

## Errores conocidos

- `faltan parametros id_nom / sactividad`
- `hay un error, no se han guardado todas las peticiones`

## Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend
  (`peticiones_activ.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PeticionesGuardar`

## Frontend Relacionado

- `frontend/actividadplazas/controller/peticiones_activ.php` (URL emitida como `url_guardar`).
