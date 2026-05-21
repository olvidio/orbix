---
id: "misas.eliminar_encargo_centro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Eliminar Encargo Centro"
capacidad: "misas.eliminar_encargo_centro.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/eliminar_encargo_centro"]
estado_revision: "generado"
---

# Flujo - Gestionar Eliminar Encargo Centro

Propuesta generada automaticamente desde la capacidad `misas.eliminar_encargo_centro.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EliminarEncargoCentro. Elimina un EncargoCtr por su uuid. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_centros`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ctr`
- `form.id_enc`
- `form.id_item`
- `form.id_zona`
- `html.nuevo`
- `post.id_zona`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_nuevo`
- `fnjs_prepara_select_encargo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/eliminar_encargo_centro`

## Errores Conocidos

- ``Falta el identificador del encargo-centro a eliminar``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
