---
id: "misas.guardar_encargo_centro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Guardar Encargo Centro"
capacidad: "misas.guardar_encargo_centro.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_centro"]
estado_revision: "generado"
---

# Flujo - Gestionar Guardar Encargo Centro

Propuesta generada automaticamente desde la capacidad `misas.guardar_encargo_centro.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona GuardarEncargoCentro. Inserta o actualiza un EncargoCtr (relacion encargo ↔ centro). - Si id_item esta vacio se crea un nuevo EncargoCtr con uuid v4. - Si id_item es un uuid valido se carga el existente y se modifica. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

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

- `/src/misas/guardar_encargo_centro`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
