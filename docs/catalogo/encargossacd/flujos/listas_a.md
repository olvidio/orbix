---
id: "encargossacd.listas_a.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Listar la atención de los centros"
capacidad: "encargossacd.listas_a.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_a"]
fragmentos: ["encargossacd.pantalla.listas_a"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_a_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas A

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_a.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Sacar el listado de atención de centros de sv y sf (cr 1/14, 10,a).

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_a`

## Escenarios Inferidos

### Listar la atención de los centros

Pasos propuestos:
1. Entrar en **listados**.
2. En «listados de encargos para cr» elegir **atención ctr sv** o **atención ctr sf**.
3. Se muestra el listado, listo para imprimir: cabecera con el **Curso** (por ejemplo 1/9/2026 - 31/8/2027), «dlb» y la referencia «ref. cr 1/14, 10, a)»; luego «1. ctr de n» con cada centro y sus sacerdotes. Si un sacerdote asignado ya no existe, el listado lo dice («No encuentro a nadie con id_nom: …»); hay que corregir ese encargo.

Endpoints asociados:
- `/src/encargossacd/listas_a_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sf`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_a_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

