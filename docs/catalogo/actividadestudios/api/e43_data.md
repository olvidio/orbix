---
id: "actividadestudios.e43_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/e43_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/e43_data.php"
entrada: ["post.append_blank_footer:mixed", "post.id_activ:integer", "post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_E43CertificadoDataData"
respuesta_data: ["msg_err:string", "nom:string", "txt_nacimiento:string", "dl_origen:string", "dl_destino:string", "txt_actividad:string", "matriculas:integer", "aAsignaturasMatriculadas:list<array{nom_asignatura: mixed, nota: string, f_acta: string, acta: string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/e43.php"]
casos_uso: ["src\\actividadestudios\\application\\E43CertificadoData"]
tags: ["actividadestudios", "e43", "data"]
estado_revision: "generado"
---

# E43 Data

Datos certificado E43 (pantalla e imprimible).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/e43_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/e43_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `append_blank_footer` | `mixed` | application | No | application |
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_nom` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_E43CertificadoDataData`):
  - `msg_err` (`string`)
  - `nom` (`string`)
  - `txt_nacimiento` (`string`)
  - `dl_origen` (`string`)
  - `dl_destino` (`string`)
  - `txt_actividad` (`string`)
  - `matriculas` (`integer`)
  - `aAsignaturasMatriculadas` (`list<array{nom_asignatura: mixed, nota: string, f_acta: string, acta: string}>`)

## Casos De Uso

- `src\actividadestudios\application\E43CertificadoData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/e43.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.