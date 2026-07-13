---
id: "inventario.equipajes_lista_activ_sel.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Equipajes Lista Activ Sel"
capacidad: "inventario.equipajes_lista_activ_sel.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_form_nuevo"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_lista_activ_sel"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Lista Activ Sel

Propuesta generada automaticamente desde la capacidad `inventario.equipajes_lista_activ_sel.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EquipajesListaActivSel. Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_form_nuevo`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.nom_equipaje`
- `html.nom_equipaje`
- `post.id_cdc`
- `post.nom_equip`
- `post.sel`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/inventario/equipajes_lista_activ_sel`

## Errores Conocidos

No se han documentado errores en la capacidad.
