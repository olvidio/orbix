---
id: "inventario.cabecera_pie_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Cabecera Pie Txt"
capacidad: "inventario.cabecera_pie_txt.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.cabecera_pie_txt", "inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar", "guardar"]
endpoints: ["/src/inventario/cabecera_pie_txt", "/src/inventario/cabecera_pie_txt_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Cabecera Pie Txt

Propuesta generada automaticamente desde la capacidad `inventario.cabecera_pie_txt.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CabeceraPieTxt. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.cabecera_pie_txt`
- `inventario.pantalla.equipajes_imprimir`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.cabecera`
- `form.cabeceraB`
- `form.firma`
- `form.pie`
- `html.cabecera`
- `html.cabeceraB`
- `html.firma`
- `html.pie`
- `post.id_equipaje`

Acciones JavaScript:
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`

## Endpoints Del Flujo

- `/src/inventario/cabecera_pie_txt`
- `/src/inventario/cabecera_pie_txt_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
