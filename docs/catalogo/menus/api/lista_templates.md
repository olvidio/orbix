---
id: "menus.lista_templates"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/lista_templates"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/menus/infrastructure/ui/http/controllers/lista_templates.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/menus/controller/menus_importar_form.php"]
casos_uso: ["src\\menus\\application\\ListaTemplatesMenus"]
tags: ["menus", "lista", "templates"]
estado_revision: "revisado"
---

# Lista de plantillas de menú

Plantillas (`ref_*` en BD pública) disponibles para importar un conjunto de menús.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Salida

- `data.a_opciones`: mapa `id_template_menu` → nombre.

## Casos De Uso

- `src\menus\application\ListaTemplatesMenus`

## Frontend Relacionado

- `frontend/menus/controller/menus_importar_form.php`
