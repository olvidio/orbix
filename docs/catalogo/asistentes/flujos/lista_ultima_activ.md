---
id: "asistentes.lista_ultima_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Lista Ultima Activ"
capacidad: "asistentes.lista_ultima_activ.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.lista_ultima_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_ultima_activ_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Ultima Activ

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Informe de personas s sin asistencia reciente.


## Punto De Entrada

Pantalla `lista_ultima_activ` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_ultima_activ`

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
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/lista_ultima_activ_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** Destino del submit de lista_ultim_que_ctr
- **Pills2:** vsg > crt/cv > informes de seguimiento
