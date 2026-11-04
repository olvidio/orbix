---
id: "encargossacd.ctr_ficha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Consultar y modificar la ficha de un centro"
capacidad: "encargossacd.ctr_ficha.gestionar"
pantallas_principales: ["encargossacd.pantalla.ctr_ficha"]
fragmentos: ["encargossacd.pantalla.ctr_ficha", "encargossacd.pantalla.ctr_ficha_update"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/encargossacd/ctr_ficha_data", "/src/encargossacd/ctr_ficha_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ctr Ficha

Propuesta generada automaticamente desde la capacidad `encargossacd.ctr_ficha.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver y modificar la atención sacerdotal de un centro: sacerdote titular, suplente, colaboradores, dedicación y exigencias.

## Punto De Entrada

Menú **dre > Encargos > ficha ctr** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.ctr_ficha_update`

## Escenarios Inferidos

### Consultar y modificar la ficha de un centro

Pasos propuestos:
1. Entrar en **ficha ctr**.
2. Elegir el **grupo de ctrs** (sv, sf, sss+, igl, cgi/oc…) y luego el **centro**.
3. Se abre la ficha con el **sacd titular**, el **Suplente**, los colaboradores, la dedicación (mañanas, tarde 1ª y 2ª hora), las exigencias del centro y las observaciones.
4. Cambiar lo que proceda y pulsar **guardar**. Si titular y suplente son el mismo sacerdote avisa «El sacd titular y suplente deben ser distintos»; no se guarda hasta corregirlo.

Endpoints asociados:
- `/src/encargossacd/ctr_ficha_data`
- `/src/encargossacd/ctr_ficha_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.filtro_ctr`
- `form.id_ubi`
- `post.filtro_ctr`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_lista_ctrs`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/ctr_ficha_data`
- `/src/encargossacd/ctr_ficha_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr


## Ruta de menú

- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr

