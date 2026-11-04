---
id: "dbextern.sincro_index.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Ver la situación de la sincronización"
capacidad: "dbextern.sincro_index.gestionar"
pantallas_principales: ["dbextern.pantalla.sincro_index"]
fragmentos: ["dbextern.pantalla.sincro_index"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/sincro_index_datos"]
estado_revision: "revisado"
---

# Flujo - Dashboard sincronización BDU

Carga inicial del dashboard «Actualizar datos desde BDU».

## Objetivo De Usuario

Al abrir la pantalla de sincronización, ver los contadores de la situación entre la BDU y Aquinate y entrar en cada subpantalla de resolución.

## Punto De Entrada

Menú **Actualizar datos desde BDU**, con el colectivo (n, a, s o sssc).

## Escenarios Inferidos

### Ver la situación de la sincronización

Pasos propuestos:
1. Abrir **Actualizar datos desde BDU** y elegir el colectivo.
2. Aparece la tabla de 9 puntos con el número de casos de cada uno y los enlaces **ver** o **ejecutar**.
3. Si el usuario no tiene permiso o no se encuentra la delegación en listas, sale el aviso correspondiente.

Endpoints asociados:
- `/src/dbextern/sincro_index_datos`

## Endpoints Del Flujo

- `/src/dbextern/sincro_index_datos`

## Errores Conocidos

- `No se encontró la delegación en listas`
- `no tiene permisos`
- `No existe la clase de la persona`

## Ruta de menú

- **Legacy:** vsm/dagd/vsg/dre > … > Actualizar datos desde BDU (según `tipo`)
- **Pills2:** PERSONAS > Numerarios / Agregados / Supernumerarios > Actualizar datos desde BDU
