---
id: "personas.stgr_update"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/stgr_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/stgr_update.php"
entrada: ["post.ctx_update:string", "post.nivel_stgr:string"]
entrada_obligatoria: ["post.ctx_update", "post.nivel_stgr"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
errores: ["No existe la clase de la persona", "No se encuentra la persona", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/personas/view/stgr_cambio.phtml"]
casos_uso: ["src\\personas\\application\\StgrUpdate"]
tags: ["personas", "stgr", "update"]
estado_revision: "revisado"
---

# Stgr Update

Actualiza el campo `nivel_stgr` de una persona.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Deriva `obj_pau` de `id_tabla` (`n`→PersonaN, `a`→Agd, `s`→S, `sssc`, `x`→Nax, códigos Ex/Sacd).
Carga persona, asigna `nivel_stgr` y persiste. Linaje: `apps/personas/controller/stgr_update.php`.

## Endpoint

- URL: `/src/personas/stgr_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/stgr_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_update` | `string` | controller | Sí | Cápsula HashB con `id_nom` e `id_tabla` |
| `nivel_stgr` | `string` | controller | Sí | Valor numérico del desplegable |

## Salida

- Helper: `ContestarJson::enviar($error_txt, 'ok')`.
- Éxito: `data: "ok"`.

## Permisos

- Frontend: acción «modificar stgr» requiere `have_perm_oficina('est')` en el listado.

## Errores conocidos

- `No existe la clase de la persona` (`id_tabla` no mapeado)
- `No se encuentra la persona`
- `hay un error, no se ha guardado` (+ detalle repositorio)

## Casos De Uso

- `src\personas\application\StgrUpdate`

## Frontend Relacionado

- `frontend/personas/view/stgr_cambio.phtml` (`fnjs_guardar_stgr`)
