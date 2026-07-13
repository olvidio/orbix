---
id: "menus.pantalla.menus_importar_form"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "menus"
nombre: "Menus Importar Form"
controller: "frontend/menus/controller/menus_importar_form.php"
vistas: ["frontend/menus/view/menus_importar_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/menus/lista_templates", "/src/menus/menus_importar"]
capacidades: ["menus.lista_templates.gestionar", "menus.menus_importar.gestionar"]
campos: ["form.id_template_menu", "html.btn_ok"]
acciones: ["fnjs_enviar", "fnjs_importar"]
estado_revision: "revisado"
---

# Importar menús desde plantilla

Selecciona plantilla (`lista_templates`) e importa al esquema activo (destructivo).

## Tipo

- Subtipo: `pantalla_principal`

## Ruta de menú

- **Legacy:** sistema > menus > importar
- **Pills2:** ADMIN LOCAL > Importar menús

## Manual De Usuario

1. Elegir plantilla. 2. Confirmar importación (sobrescribe aux_* locales).
