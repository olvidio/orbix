---
id: "usuarios.borrar_pwd"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/borrar_pwd"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/borrar_pwd.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "borrar", "pwd"]
estado_revision: "revisado"
errores: ["No se pudieron obtener esquemas", "Sólo se puede borrar en la base de datos de pruebas", "hay un error, no se ha guardado"]
---

# Borrar Pwd

Herramienta de pruebas: resetea contraseñas al login en todos los esquemas (excepto superadmin id_role=1). Solo WEBDIR=pruebas o Docker.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Herramienta de pruebas: resetea contraseñas al login en todos los esquemas (excepto superadmin id_role=1). Solo WEBDIR=pruebas o Docker.

## Endpoint

- URL: `/src/usuarios/borrar_pwd`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/borrar_pwd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `actualizados`: número de usuarios actualizados

## Errores conocidos
- `No se pudieron obtener esquemas`
- `Sólo se puede borrar en la base de datos de pruebas`
- `hay un error, no se ha guardado`

## Permisos

Restringido a entorno pruebas/Docker en controller.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
