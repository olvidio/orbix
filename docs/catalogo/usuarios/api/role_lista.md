---
id: "usuarios.role_lista"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "custom_json"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/role_lista.php"]
casos_uso: ["src\\usuarios\\application\\rolesLista"]
tags: ["usuarios", "role", "lista"]
estado_revision: "revisado"
errores: ["Usuario no encontrado"]
---

# Role Lista

Lista roles con grupmenus concatenados y nivel permiso del operador.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista roles con grupmenus concatenados y nivel permiso del operador.

## Endpoint

- URL: `/src/usuarios/role_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_cabeceras`: role,sf,sv,pau,dmz,grup menu
  - `a_botones`: modificar/borrar según permiso
  - `a_valores`: filas
  - `permiso`: 0|1|2

## Errores conocidos
- `Usuario no encontrado`

## Permisos

Superadmin permiso=1 (CRUD); admin permiso=2 (modificar grupmenu); resto lectura filtrada sfsv.

## Casos De Uso

- `src\usuarios\application\rolesLista`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/role_lista.php"]`).
