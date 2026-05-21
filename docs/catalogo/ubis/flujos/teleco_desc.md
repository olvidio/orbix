---
id: "ubis.teleco_desc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Teleco Desc"
capacidad: "ubis.teleco_desc.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.teleco_desc_lista_ajax"]
acciones: ["listar"]
endpoints: ["/src/ubis/teleco_desc_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Teleco Desc

Propuesta generada automaticamente desde la capacidad `ubis.teleco_desc.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TelecoDescLista. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_desc_lista_ajax`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/ubis/teleco_desc_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_tipo_teleco`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/teleco_desc_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
