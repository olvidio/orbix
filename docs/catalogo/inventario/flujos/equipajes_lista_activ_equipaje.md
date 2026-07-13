---
id: "inventario.equipajes_lista_activ_equipaje.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Equipajes Lista Activ Equipaje"
capacidad: "inventario.equipajes_lista_activ_equipaje.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_lista_activ_equipaje"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Lista Activ Equipaje

Propuesta generada automaticamente desde la capacidad `inventario.equipajes_lista_activ_equipaje.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EquipajesListaActivEquipaje. Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

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
- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`

## Endpoints Del Flujo

- `/src/inventario/equipajes_lista_activ_equipaje`

## Errores Conocidos

No se han documentado errores en la capacidad.
