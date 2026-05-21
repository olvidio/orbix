---
id: "misas.eliminar_encargo_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Eliminar Encargo Zona"
capacidad: "misas.eliminar_encargo_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/eliminar_encargo_zona"]
estado_revision: "generado"
---

# Flujo - Gestionar Eliminar Encargo Zona

Propuesta generada automaticamente desde la capacidad `misas.eliminar_encargo_zona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EliminarEncargoZona. Elimina un Encargo por id. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_zona`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.descripcion_lugar`
- `form.encargo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_ubi`
- `form.id_zona`
- `form.idioma_enc`
- `form.observ`
- `form.orden`
- `form.prioridad`
- `html.nuevo`
- `post.id_zona`
- `post.orden`

Acciones JavaScript:
- `fnjs_generarNomEnc`
- `fnjs_nuevo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/eliminar_encargo_zona`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
