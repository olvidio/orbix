---
id: "actividadestudios.lista_clases_ca_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/lista_clases_ca_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/lista_clases_ca_data.php"
entrada: ["post.id_activ:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_ListaClasesCaDataData"
respuesta_data: ["msg_err:string", "nom_activ:string", "nom_director_est:string", "datos_asignatura:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/lista_clases_ca.php"]
casos_uso: ["src\\actividadestudios\\application\\ListaClasesCaData"]
tags: ["actividadestudios", "lista", "clases", "ca", "data"]
estado_revision: "generado"
---

# Lista Clases Ca Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/lista_clases_ca_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/lista_clases_ca_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_ListaClasesCaDataData`):
  - `msg_err` (`string`)
  - `nom_activ` (`string`)
  - `nom_director_est` (`string`)
  - `datos_asignatura` (`array`)

## Casos De Uso

- `src\actividadestudios\application\ListaClasesCaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/lista_clases_ca.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.