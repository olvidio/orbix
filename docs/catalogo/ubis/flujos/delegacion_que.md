---
id: "ubis.delegacion_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Elegir la delegación de destino"
capacidad: "ubis.delegacion_que.gestionar"
pantallas_principales: ["ubis.pantalla.delegacion_que"]
fragmentos: ["ubis.pantalla.delegacion_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/delegacion_que_data"]
estado_revision: "revisado"
---

# Flujo - Delegacion Que

## Objetivo De Usuario

Ofrecer las delegaciones a las que se pueden trasladar los centros o casas marcados.

## Punto De Entrada

Menú **scdl > direcciones > listados** (también Calendario o dre > centros y casas > listados).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.delegacion_que`

## Escenarios Inferidos

### Elegir la delegación de destino

Pasos propuestos:
1. En el listado, marcar los centros o casas a trasladar.
2. Pulsar trasladar.
3. En **Trasladar a**, elegir la delegación y confirmar.

Endpoints asociados:
- `/src/ubis/delegacion_que_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_cmb_id_dl`
- `fnjs_trasladar`

## Endpoints Del Flujo

- `/src/ubis/delegacion_que_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados
