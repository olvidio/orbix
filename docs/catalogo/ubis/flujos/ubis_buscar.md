---
id: "ubis.ubis_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Ubis Buscar"
capacidad: "ubis.ubis_buscar.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_buscar"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_buscar_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ubis Buscar

Propuesta generada automaticamente desde la capacidad `ubis.ubis_buscar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UbisBuscarOpciones. Opciones de formulario para frontend/ubis/controller/ubis_buscar.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_buscar`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.b_buscar`
- `html.b_mas`
- `html.cmb`
- `html.labor[]`
- `html.loc`
- `html.ok`
- `html.opcion`
- `html.select[]`
- `html.simple`
- `html.tipo`
- `post.loc`
- `post.simple`
- `post.tipo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_update_div`
- `fnjs_ver_solo`

## Endpoints Del Flujo

- `/src/ubis/ubis_buscar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
