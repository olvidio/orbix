---
id: "encargossacd.sacd_ficha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Consultar y modificar la ficha de un sacerdote"
capacidad: "encargossacd.sacd_ficha.gestionar"
pantallas_principales: ["encargossacd.pantalla.sacd_ficha"]
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ficha_data", "/src/encargossacd/sacd_ficha_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Ficha

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ficha.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver y modificar los encargos de un sacerdote y su dedicación en cada uno.

## Punto De Entrada

Menú **dre > Encargos > ficha sacd** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha sacd).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ficha_ajax`

## Escenarios Inferidos

### Consultar y modificar la ficha de un sacerdote

Pasos propuestos:
1. Entrar en **ficha sacd**; la pantalla se titula «fase real».
2. Elegir el **tipo** (n, agd, sss+, cp) y luego el sacerdote en **Sacd:**.
3. Aparecen sus encargos agrupados en **titular**, **titular (no cl)**, **suplente** y **colaborador** (por ejemplo «Atención de un ctr (Raset)», «descanso», «estudio», «oficial dre»). Cada uno tiene tres casillas numéricas: **mañanas**, **tarde 1ª hora** y **tarde 2ª hora**. El número es la cantidad de días (a la semana) que el sacerdote cubre esa franja.
4. Para añadir otro encargo, elegir en **añadir encargo** (estudio, descanso, retiro, otros).
5. Escribir o cambiar las **observaciones** si hace falta y pulsar **Guardar**.

Endpoints asociados:
- `/src/encargossacd/sacd_ficha_data`
- `/src/encargossacd/sacd_ficha_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dedic_m`
- `form.dedic_t`
- `form.dedic_v`
- `form.enc_num`
- `form.id_tipo_enc`
- `form.mas`
- `form.observ`
- `html.dedic_m[<?= $j ?>]`
- `html.dedic_t[<?= $j ?>]`
- `html.dedic_v[<?= $j ?>]`
- `html.enc_num`
- `html.ok`
- `post.filtro_sacd`
- `post.id_nom`
- `post.que`

Acciones JavaScript:
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_mas_enc`
- `fnjs_update_div`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ficha_data`
- `/src/encargossacd/sacd_ficha_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** dre > Encargos > ficha sacd
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha sacd


## Ruta de menú

- **Legacy:** dre > Encargos > ficha sacd
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha sacd

