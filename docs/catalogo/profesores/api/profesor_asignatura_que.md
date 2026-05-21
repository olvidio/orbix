---
id: "profesores.profesor_asignatura_que"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/profesor_asignatura_que"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_que.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/profesor_asignatura_que.php"]
casos_uso: []
tags: ["profesores", "profesor", "asignatura", "que"]
estado_revision: "generado"
---

# Profesor Asignatura Que

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/profesor_asignatura_que`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_que.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/profesores/controller/profesor_asignatura_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.