---
id: "inventario.lista_docs_perdidos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar los documentos perdidos"
capacidad: "inventario.lista_docs_perdidos.gestionar"
pantallas_principales: ["inventario.pantalla.docs_perdidos"]
fragmentos: ["inventario.pantalla.docs_perdidos"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_perdidos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs Perdidos

## Objetivo De Usuario

Consultar los documentos dados por perdidos.

## Punto De Entrada

Menú **Inventario > inventarios > lista docs perdidos**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.docs_perdidos`

## Escenarios Inferidos

### Consultar los documentos perdidos

Pasos propuestos:
1. Abrir **lista docs perdidos**.
2. Leer el listado: **centro - lugar**, **documento**, **número** y **fecha perdido**.

Endpoints asociados:
- `/src/inventario/lista_docs_perdidos`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/lista_docs_perdidos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > lista docs perdidos
- **Pills2:** sin entrada de menú en el índice
