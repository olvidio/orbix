---
id: "actividadplazas.peticiones_incorporar"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_incorporar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_incorporar.php"
entrada: ["post.sactividad:string", "post.sasistentes:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PeticionesIncorporarData"
respuesta_data: ["incorporadas:integer", "mensaje_final:string"]
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadplazas/controller/incorporar_peticion.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesIncorporar"]
tags: ["actividadplazas", "peticiones", "incorporar"]
estado_revision: "revisado"
---

# Peticiones Incorporar

Incorpora la primera petición de plaza de cada persona como asistencia con plaza asignada (si la
actividad es de mi dl) o pedida (si es de otra dl), para un tipo y colectivo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Calcula el `id_tipo_activ` y el rango del curso vigente a partir de `sactividad` + `sasistentes`.
- Reúne las actividades candidatas (de mi dl y publicadas de otras dl) según el colectivo (`n`, `a`,
  `agd`; para `n` incluye también las de agd).
- Toma las peticiones con `orden = 1` del tipo, filtradas por el patrón de `id_nom` según colectivo.
- Por cada persona sin asistencia propia ya existente en esas actividades, crea un `Asistente` propio
  con plaza `ASIGNADA`, propietario `dl_org>mi_dele` y responsable mi dl (usando el repositorio de mi
  dl o el de fuera según organice mi dl u otra).
- Devuelve cuántas incorporó y un mensaje recordando que no se incorporan personas que ya tienen una
  actividad propia en el periodo.

## Endpoint

- URL: `/src/actividadplazas/peticiones_incorporar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_incorporar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sactividad` | `string` | controller | No | Tipo de actividad (`ca`/`cv`/`crt`) |
| `sasistentes` | `string` | controller | No | Colectivo (`n`, `a`, `agd`); determina actividades y filtro de personas |

## Salida

- Helper: `ContestarJson::enviar` (`data` serializada como string JSON; el front hace segundo `JSON.parse`, ver `incorporar_peticion.phtml`).
- Forma: `standard_envelope_string_data`.
- El controller extrae la clave `error` del resultado y la envía como `mensaje`; el resto es `data`.
- Payload en `data` (schema `actividadplazas_PeticionesIncorporarData`):
  - `incorporadas` (`integer`): nº de asistencias creadas.
  - `mensaje_final` (`string`): aviso con el periodo considerado (personas ya con actividad propia se omiten).

## Efectos colaterales

- Crea asistencias (`Asistente`) con plaza asignada/pedida para las personas incorporadas.

## Errores conocidos

- `hay un error, no se ha guardado` (fallo al guardar una asistencia; también puede propagarse el
  mensaje devuelto por la comprobación de plaza `setPlazaComprobando`).

## Permisos

- Sin control de permisos propio; la operación es sobre mi dl (`ConfigGlobal::mi_delef()`) y la
  autorización de oficina se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PeticionesIncorporar`

## Frontend Relacionado

- `frontend/actividadplazas/controller/incorporar_peticion.php` (botón "continuar", URL `url_incorporar`).
