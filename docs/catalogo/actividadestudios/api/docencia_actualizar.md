---
id: "actividadestudios.docencia_actualizar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/docencia_actualizar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/docencia_actualizar.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/actualizar_docencia.php"]
casos_uso: ["src\\actividadestudios\\application\\DocenciaActualizar"]
tags: ["actividadestudios", "docencia", "actualizar"]
estado_revision: "generado"
---

# Docencia Actualizar

Ejecuta {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/docencia_actualizar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/docencia_actualizar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | application | No | application |
| `empiezamin` | `string` | application | No | application |
| `periodo` | `string` | application | No | application |
| `year` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Actualiza el dossier `d_docencia_stgr` con la informacion docente derivada de las actividades terminadas del periodo indicado.

## Casos De Uso

- `src\actividadestudios\application\DocenciaActualizar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/actualizar_docencia.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.