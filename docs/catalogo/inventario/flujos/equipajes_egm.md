---
id: "inventario.equipajes_egm.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Equipajes Egm"
capacidad: "inventario.equipajes_egm.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_doc_casa", "inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_egm"]
estado_revision: "generado"
---

# Flujo - Gestionar Equipajes Egm

Propuesta generada automaticamente desde la capacidad `inventario.equipajes_egm.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EquipajesEgm. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_doc_casa`
- `inventario.pantalla.equipajes_imprimir`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_equipaje`

Acciones JavaScript:
- `fnjs_eliminar_grupo`
- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_nuevo_grupo`
- `fnjs_ver_equipaje`

## Endpoints Del Flujo

- `/src/inventario/equipajes_egm`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
