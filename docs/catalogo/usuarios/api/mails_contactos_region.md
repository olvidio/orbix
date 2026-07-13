---
id: "usuarios.mails_contactos_region"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/mails_contactos_region"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/mails_contactos_region.php"
entrada: ["post.region:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_usuariosRegionContactosData"
respuesta_data: ["error:string, data: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/mails_contactos_region.php"]
casos_uso: ["src\\usuarios\\application\\usuariosRegionContactos"]
tags: ["usuarios", "mails", "contactos", "region"]
estado_revision: "revisado"
errores: []
---

# Mails Contactos Region

Devuelve contactos email de usuarios regionales con permisos de oficina relevantes (pantalla recuperación).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve contactos email de usuarios regionales con permisos de oficina relevantes (pantalla recuperación).

## Endpoint

- URL: `/src/usuarios/mails_contactos_region`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/mails_contactos_region.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `success`: true
  - `contactos`: mapa nom→{email,cargo} filtrado por perm oficina est/sm/agd

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Público en flujo recovery; consulta esquema remoto por región.

## Casos De Uso

- `src\usuarios\application\usuariosRegionContactos`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/mails_contactos_region.php"]`).
