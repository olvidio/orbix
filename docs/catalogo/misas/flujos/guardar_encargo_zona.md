---
id: "misas.guardar_encargo_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Guardar Encargo Zona"
capacidad: "misas.guardar_encargo_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_zona"]
estado_revision: "generado"
---

# Flujo - Gestionar Guardar Encargo Zona

Propuesta generada automaticamente desde la capacidad `misas.guardar_encargo_zona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona GuardarEncargoZona. Inserta o actualiza un Encargo del grupo ZONAS_MISAS. - Si id_enc es 0 se crea uno nuevo con getNewId(). - Si hay valor, se carga el existente y se modifica. Devuelve un array con: - error: texto vacio si todo fue bien, mensaje del repositorio si no. - data : payload para el frontend con id_enc, lugar y el nombre del centro si se resolvio.

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

- `/src/misas/guardar_encargo_zona`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
