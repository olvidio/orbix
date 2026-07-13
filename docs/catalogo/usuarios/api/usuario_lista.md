---
id: "usuarios.usuario_lista"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_lista.php"
entrada: ["post.username:string"]
entrada_obligatoria: []
respuesta: "custom_json"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_lista.php"]
casos_uso: ["src\\usuarios\\application\\usuariosLista"]
tags: ["usuarios", "usuario", "lista"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "no tiene permisos para ver esto"]
---

# Usuario Lista

Lista usuarios web filtrable; oculta roles según sfsv y permiso id_role≤3.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista usuarios web filtrable; oculta roles según sfsv y permiso id_role≤3.

## Endpoint

- URL: `/src/usuarios/usuario_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `username` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_cabeceras`: usuario,nombre,role,email,accion
  - `a_botones`: borrar
  - `a_valores`: filas con sel id# y link editar

## Errores conocidos
- `Usuario no encontrado`
- `no tiene permisos para ver esto`

## Permisos

id_role≤3; si >3 error permisos.

## Casos De Uso

- `src\usuarios\application\usuariosLista`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_lista.php"]`).
