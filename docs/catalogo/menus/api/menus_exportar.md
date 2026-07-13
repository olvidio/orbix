---
id: "menus.menus_exportar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_exportar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_exportar.php"
entrada: ["post.nombre:string", "post.sobreescribir:string"]
entrada_obligatoria: ["nombre"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["ya existe"]
frontend_referencias: ["frontend/menus/controller/menus_exportar_form.php"]
casos_uso: []
tags: ["menus", "exportar", "template"]
estado_revision: "revisado"
---

# Exportar menús a plantilla (ref)

Copia `aux_grupmenu`, `aux_grupmenu_rol` y `aux_menus` (ok=true) del esquema activo a tablas `ref_*` en BD
pública, asociadas a una plantilla (`id_template_menu`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Si la plantilla existe y `sobreescribir` no es true → responde `mensaje: ya existe` y termina.
- Vacía e inserta en `ref_grupmenu`, `ref_grupmenu_rol`, `ref_menus`.
- Lógica inline con PDO (`GlobalPdo`); códigos de error internos `ExportarMenu.*`.

## Entrada

| Campo | Tipo | Notas |
|-------|------|-------|
| `nombre` | `string` | Nombre plantilla |
| `sobreescribir` | `string` | Truthy para reemplazar |

## Salida

- Éxito: `data: "ok"`; errores SQL acumulados en `mensaje`.

## Frontend Relacionado

- `frontend/menus/controller/menus_exportar_form.php`
