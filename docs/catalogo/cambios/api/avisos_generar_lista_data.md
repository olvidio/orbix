---
id: "cambios.avisos_generar_lista_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/avisos_generar_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/cambios/infrastructure/ui/http/controllers/avisos_generar_lista_data.php"
entrada: ["post.aviso_tipo:integer", "post.id_usuario:integer", "post.is_admin:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/avisos_generar.php"]
casos_uso: ["src\\cambios\\application\\AvisosGenerarListaData"]
tags: ["cambios", "avisos", "generar", "lista", "data"]
estado_revision: "generado"
---

# Avisos Generar Lista Data

Endpoint backend: listado de avisos `CambioUsuario` (con `avisado=false`) para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla `avisos_generar`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/avisos_generar_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/avisos_generar_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `aviso_tipo` | `integer` | controller+application | No | controller+application |
| `id_usuario` | `integer` | controller+application | No | controller+application |
| `is_admin` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Efectos colaterales

- URLs y fragmentos hash de eliminación: {@see \frontend\cambios\helpers\AvisosGenerarListaRender}.

## Casos De Uso

- `src\cambios\application\AvisosGenerarListaData`

## Frontend Relacionado

- `frontend/cambios/controller/avisos_generar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.