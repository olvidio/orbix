---
id: "asistentes.lista_ultim_que_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Preparar el informe de última asistencia"
capacidad: "asistentes.lista_ultim_que_ctr.gestionar"
pantallas_principales: ["asistentes.pantalla.lista_ultim_que_ctr"]
fragmentos: ["asistentes.pantalla.lista_ultim_que_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_ultim_que_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Ultim Que Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Elegir centro y curso para el informe de última asistencia (quién no ha ido al crt o a la cv).

## Punto De Entrada

Menú **vsg > crt / cv > s que no han ido al crt / cv > este curso / el curso pasado**, **s que han de hacer crt interno** o **el crt de cel**.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_ultim_que_ctr`

## Escenarios Inferidos

### Preparar el informe de última asistencia

Pasos propuestos:
1. Entrar en la opción que interese (este curso o el curso pasado).
2. Elegir el centro.
3. Se muestra el informe.

Endpoints asociados:
- `/src/asistentes/lista_ultim_que_ctr_data`

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
