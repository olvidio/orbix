---
id: "menus.menus_importar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Menus Importar"
capacidad: "menus.menus_importar.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.menus_importar_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/menus_importar"]
estado_revision: "generado"
---

# Flujo - Gestionar Menus Importar

Propuesta generada automaticamente desde la capacidad `menus.menus_importar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona MenusImportar. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `menus.pantalla.menus_importar_form`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_template_menu`
- `html.btn_ok`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_importar`

## Endpoints Del Flujo

- `/src/menus/menus_importar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
