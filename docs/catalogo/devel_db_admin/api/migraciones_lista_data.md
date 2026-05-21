---
id: "devel_db_admin.migraciones_lista_data"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/migraciones_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_lista_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/migraciones_lista.php"]
casos_uso: ["src\\devel_db_admin\\application\\MigracionesListaData"]
tags: ["devel_db_admin", "migraciones", "lista", "data"]
estado_revision: "generado"
---

# Migraciones Lista Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/migraciones_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_lista_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\devel_db_admin\application\MigracionesListaData`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/migraciones_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.