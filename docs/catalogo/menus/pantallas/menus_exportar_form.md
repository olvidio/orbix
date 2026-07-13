---
id: "menus.pantalla.menus_exportar_form"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "menus"
nombre: "Menus Exportar Form"
controller: "frontend/menus/controller/menus_exportar_form.php"
vistas: ["frontend/menus/view/menus_exportar_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/menus/menus_exportar"]
capacidades: ["menus.menus_exportar.gestionar"]
campos: ["form.nombre", "html.btn_ok", "html.nombre"]
acciones: ["fnjs_enviar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Exportar menús a plantilla

Guarda el menú actual del esquema como plantilla ref en BD pública.

## Tipo

- Subtipo: `pantalla_principal`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ADMIN LOCAL > Exportar menús

## Manual De Usuario

1. Indicar nombre plantilla. 2. Exportar (opción sobreescribir en formulario).
