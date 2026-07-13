---
id: "actividadplazas.peticiones_eliminar"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_eliminar.php"
entrada: ["post.id_nom:integer", "post.sactividad:string"]
entrada_obligatoria: ["id_nom", "sactividad"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_nom / sactividad", "hay un error, no se ha podido eliminar"]
frontend_referencias: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesEliminar"]
tags: ["actividadplazas", "peticiones", "eliminar"]
estado_revision: "revisado"
---

# Peticiones Eliminar

Elimina todas las peticiones de plaza de una persona para un tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Valida `id_nom` y `sactividad`.
- Recupera todas las `PlazaPeticion` de esa persona + tipo y las elimina una a una.
- Si falla alguna eliminación, corta y devuelve error.

## Endpoint

- URL: `/src/actividadplazas/peticiones_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Si | Persona propietaria de las peticiones |
| `sactividad` | `string` | controller | Si | Tipo de actividad (`ca`/`cv`/`crt`) |

## Salida

- Helper: `ContestarJson::enviar` (`enviar($mensaje, 'ok')`: string vacío en éxito, mensaje en error).
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina todas las peticiones de plaza para un `{id_nom, tipo}`.

## Errores conocidos

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`

## Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend
  (`peticiones_activ.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PeticionesEliminar`

## Frontend Relacionado

- `frontend/actividadplazas/controller/peticiones_activ.php` (URL emitida como `url_eliminar`).
