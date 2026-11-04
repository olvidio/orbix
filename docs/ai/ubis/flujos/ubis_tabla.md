---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Trabajar con los resultados de la búsqueda"
flujo: "ubis.ubis_tabla.gestionar.flujo"
preguntas: ["Como trabajar con los resultados de la búsqueda en Trabajar con los resultados de la búsqueda?"]
pantallas_principales: ["ubis.pantalla.ubis_tabla"]
fragmentos: ["ubis.pantalla.ubis_tabla"]
endpoints: ["/src/ubis/ubis_tabla_data"]
source: "docs/catalogo/ubis/flujos/ubis_tabla.md"
estado_revision: "generado"
---

# Ayuda IA - Trabajar con los resultados de la búsqueda

Usa este documento para responder preguntas de usuario sobre como trabajar con `Trabajar con los resultados de la búsqueda`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como trabajar con los resultados de la búsqueda en Trabajar con los resultados de la búsqueda?

## Donde Entrar

- Ubis Tabla (`ubis.pantalla.ubis_tabla`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Trabajar con los resultados de la búsqueda

1. Tras buscar, aparece «Tabla de toda las casas y centros».
2. Pulsar un nombre para abrir su ficha.
3. Para crear uno nuevo, usar el botón **Centro** o **Casa** (solo aparecen si se buscó con un tipo y una localización distintos de «todos»; con «todos» la pantalla solo ofrece **buscar otras opciones** y avisa «OJO!: para crear un centro/casa debe especificar el tipo de centro/casa»).
4. Para quitar uno, marcarlo y pulsar eliminar; confirmar «¿Está seguro que desea borrar este ubi?».

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/ubis_tabla_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_tabla`
- `ubis.pantalla.ubis_tabla`

## Objetivo

Mostrar la tabla de centros y casas encontrados y permitir abrir, crear, eliminar o trasladar.

## Errores Documentados

- `debe poner algún criterio de búsqueda`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
