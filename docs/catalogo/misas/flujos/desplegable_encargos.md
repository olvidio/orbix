---
id: "misas.desplegable_encargos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Desplegable Encargos"
capacidad: "misas.desplegable_encargos.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/desplegable_encargos"]
estado_revision: "generado"
---

# Flujo - Gestionar Desplegable Encargos

Propuesta generada automaticamente desde la capacidad `misas.desplegable_encargos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DesplegableEncargos. Payload JSON para el desplegable dinamico de encargos de una zona. Sigue el contrato de desplegables de refactor.md: - id : id del <select> que montara fnjs_construir_desplegable. - opciones : map id_enc => desc_enc de los encargos con id_tipo_enc >= 8100 de la zona. - selected : id_enc preseleccionado ('' si no aplica). - blanco : true si se quiere opcion en blanco. - val_blanco: valor de la opcion en blanco. - action : handler onchange opcional (vacio por defecto).

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

- `/src/misas/desplegable_encargos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
