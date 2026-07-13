---
id: "inventario.lista_de_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Lista De Ctr"
capacidad: "inventario.lista_de_ctr.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.traslado_doc_que"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_de_ctr"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista De Ctr

Propuesta generada automaticamente desde la capacidad `inventario.lista_de_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaDeCtr. Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.traslado_doc_que`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ubi`
- `form.id_ubi_new`
- `form.sel`
- `html.ok`

Acciones JavaScript:
- `fnjs_busca_docs`
- `fnjs_busca_lugares`
- `fnjs_busca_lugares_destino`
- `fnjs_busca_lugares_origen`
- `fnjs_crearSelectDesdeJson`
- `fnjs_guardar`
- `fnjs_put_desplegable_lugares`

## Endpoints Del Flujo

- `/src/inventario/lista_de_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.
