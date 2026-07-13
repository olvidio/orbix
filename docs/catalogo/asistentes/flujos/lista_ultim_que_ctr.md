---
id: "asistentes.lista_ultim_que_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Lista Ultim Que Ctr"
capacidad: "asistentes.lista_ultim_que_ctr.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.lista_ultim_que_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_ultim_que_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Ultim Que Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Elegir centro para informe de última asistencia.


## Punto De Entrada

Pantalla `lista_ultim_que_ctr` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_ultim_que_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_enviar_formulario`

## Endpoints Del Flujo

- `/src/asistentes/lista_ultim_que_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vsg > crt/cv > informes de seguimiento
- **Pills2:** vsg > crt/cv > s que no han ido / corresponde ir
