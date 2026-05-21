---
id: "menus.menus_get_page.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Menus Get Page"
capacidad: "menus.menus_get_page.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.menus_get"]
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_get_page_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Menus Get Page

Propuesta generada automaticamente desde la capacidad `menus.menus_get_page.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona MenusGetPage. Datos para frontend/menus/controller/menus_get.php (formulario o listado).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `menus.pantalla.menus_get`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.$campos_chk`
- `form.filtro_grupo`
- `form.gm_new`
- `form.id_menu`
- `form.id_metamenu`
- `form.orden`
- `form.parametros`
- `form.perm_menu`
- `form.txt_menu`
- `html.bnada`
- `html.btodo`
- `html.orden`
- `html.parametros`
- `html.txt_menu`
- `post.filtro_grupo`
- `post.id_menu`
- `post.nuevo`

Acciones JavaScript:
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_lista_menus`
- `fnjs_selectAll`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/menus/menus_get_page_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
