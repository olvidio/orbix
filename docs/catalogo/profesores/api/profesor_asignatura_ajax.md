---
id: "profesores.profesor_asignatura_ajax"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/profesor_asignatura_ajax"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_ajax.php"
entrada: ["post.id_asignatura:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/profesor_asignatura_ajax.php"]
casos_uso: ["src\\profesores\\application\\ProfesoresAsignaturaLista"]
tags: ["profesores", "profesor", "asignatura", "ajax"]
estado_revision: "generado"
---

# Profesor Asignatura Ajax

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/profesor_asignatura_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_asignatura` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\profesores\application\ProfesoresAsignaturaLista`

## Frontend Relacionado

- `frontend/profesores/controller/profesor_asignatura_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.