---
id: "asistentes.lista_ultima_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Consultar quién no ha asistido recientemente"
capacidad: "asistentes.lista_ultima_activ.gestionar"
pantallas_principales: ["asistentes.pantalla.lista_ultima_activ"]
fragmentos: ["asistentes.pantalla.lista_ultima_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_ultima_activ_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Ultima Activ

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Informe de las personas s que no han asistido recientemente al crt o a la cv.

## Punto De Entrada

Menú **vsg > crt / cv > s que no han ido ...**.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_ultima_activ`

## Escenarios Inferidos

### Consultar quién no ha asistido recientemente

Pasos propuestos:
1. Entrar en la opción **s que no han ido al crt** (o a la **cv**) del curso actual o del anterior.
2. Se muestra la lista con la fecha de su última asistencia.

Endpoints asociados:
- `/src/asistentes/lista_ultima_activ_data`

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
