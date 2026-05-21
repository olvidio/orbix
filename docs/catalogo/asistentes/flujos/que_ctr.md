---
id: "asistentes.que_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Que Ctr"
capacidad: "asistentes.que_ctr.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.que_ctr_lista"]
acciones: ["listar"]
endpoints: ["/src/asistentes/que_ctr_lista_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Que Ctr

Propuesta generada automaticamente desde la capacidad `asistentes.que_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona QueCtr. JSON para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.que_ctr_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/asistentes/que_ctr_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.btn_ok`
- `html.n_agd`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_otro`

## Endpoints Del Flujo

- `/src/asistentes/que_ctr_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
