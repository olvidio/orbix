---
id: "actividadestudios.matriculas_lista_otras_r_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matriculas_lista_otras_r_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_otras_r_data.php"
entrada: ["post.apellido1:string", "post.esquema:string", "post.esquema_region_stgr:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_MatriculasListaOtrasRDataData"
respuesta_data: ["titulo:string", "titulo_busqueda_por_apellidos:string", "msg_err:string", "aviso:string", "a_valores:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_lista_otras_r.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasListaOtrasRData"]
tags: ["actividadestudios", "matriculas", "lista", "otras", "r", "data"]
estado_revision: "generado"
---

# Matriculas Lista Otras R Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/matriculas_lista_otras_r_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_otras_r_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `apellido1` | `string` | controller+application | No | controller+application |
| `esquema` | `string` | controller | No | controller |
| `esquema_region_stgr` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_MatriculasListaOtrasRDataData`):
  - `titulo` (`string`)
  - `titulo_busqueda_por_apellidos` (`string`)
  - `msg_err` (`string`)
  - `aviso` (`string`)
  - `a_valores` (`array`)

## Casos De Uso

- `src\actividadestudios\application\MatriculasListaOtrasRData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matriculas_lista_otras_r.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.