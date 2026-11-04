---
id: "ubis.ubis_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Buscar un centro o una casa"
capacidad: "ubis.ubis_buscar.gestionar"
pantallas_principales: ["ubis.pantalla.ubis_buscar"]
fragmentos: ["ubis.pantalla.ubis_buscar"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_buscar_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Buscar

## Objetivo De Usuario

Buscar un centro o una casa por nombre, población, región o país, y llegar a su ficha.

## Punto De Entrada

Menú **scdl > direcciones > buscar** (Calendario o dre > centros y casas > buscar; exterior > casas > buscar).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_buscar`

## Escenarios Inferidos

### Buscar un centro o una casa

Pasos propuestos:
1. Entrar en **buscar**. Aparece el formulario **Buscar centro o casa**.
2. Escribir parte del **Nombre de la casa o centro**, la **Población**, o elegir **Región** o **País**. Se puede marcar «buscar ubis fuera de uso» para incluir los dados de baja.
3. Pulsar **buscar**: aparece la tabla de resultados.
4. Pulsar el nombre para abrir la ficha.
5. Si no hay resultados sale «no existe este centro o casa».

Endpoints asociados:
- `/src/ubis/ubis_buscar_data`

### Buscar con más opciones

Pasos propuestos:
1. En el formulario, pulsar **buscar otras opciones**.
2. Elegir **Tipo** (Centro, Casa o Todos) y **Localización** (de la propia r/dl, de otra r/dl o todos), además del nombre y la población.
3. Pulsar **buscar**.

Endpoints asociados:
- `/src/ubis/ubis_buscar_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.b_buscar`
- `html.b_mas`
- `html.cmb`
- `html.labor[]`
- `html.loc`
- `html.ok`
- `html.opcion`
- `html.select[]`
- `html.simple`
- `html.tipo`
- `post.loc`
- `post.simple`
- `post.tipo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_update_div`
- `fnjs_ver_solo`

## Endpoints Del Flujo

- `/src/ubis/ubis_buscar_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar
