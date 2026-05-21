---
id: "inventario.equipajes_lista_activ_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Equipajes Lista Activ Periodo"
capacidad: "inventario.equipajes_lista_activ_periodo.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_lista_activ_periodo"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_lista_activ_periodo"]
estado_revision: "generado"
---

# Flujo - Gestionar Equipajes Lista Activ Periodo

Propuesta generada automaticamente desde la capacidad `inventario.equipajes_lista_activ_periodo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EquipajesListaActivPeriodo. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_lista_activ_periodo`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_cdc`
- `post.inicio`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_nombrar_equipaje`

## Endpoints Del Flujo

- `/src/inventario/equipajes_lista_activ_periodo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
