---
id: "profesores.lista_por_departamentos"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/lista_por_departamentos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/profesores/infrastructure/ui/http/controllers/lista_por_departamentos.php"
entrada: ["post.dl:array", "post.filtro:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/lista_por_departamentos.php"]
casos_uso: ["src\\profesores\\application\\ListaPorDepartamentos"]
tags: ["profesores", "lista", "por", "departamentos"]
estado_revision: "generado"
---

# Lista Por Departamentos

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/lista_por_departamentos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/profesores/infrastructure/ui/http/controllers/lista_por_departamentos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `array` | controller | No | controller |
| `filtro` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\profesores\application\ListaPorDepartamentos`

## Frontend Relacionado

- `frontend/profesores/controller/lista_por_departamentos.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.