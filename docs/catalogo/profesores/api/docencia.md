---
id: "profesores.docencia"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/docencia"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/profesores/infrastructure/ui/http/controllers/docencia.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_DocenciaListaData"
respuesta_data: ["id_tabla:string, a_cabeceras: array<int, string>, a_valores: array<int, array<int, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/docencia.php"]
casos_uso: ["src\\profesores\\application\\DocenciaLista"]
tags: ["profesores", "docencia"]
estado_revision: "generado"
---

# Docencia

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/docencia`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/profesores/infrastructure/ui/http/controllers/docencia.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `profesores_DocenciaListaData`):
  - `id_tabla` (`string, a_cabeceras: array<int, string>, a_valores: array<int, array<int, mixed>>`)

## Casos De Uso

- `src\profesores\application\DocenciaLista`

## Frontend Relacionado

- `frontend/profesores/controller/docencia.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.