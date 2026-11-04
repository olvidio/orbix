---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Ver el listado de cartas"
flujo: "cartaspresentacion.cartas_presentacion.gestionar.flujo"
preguntas: ["Como ver el listado de cartas en Ver el listado de cartas?", "Como ver el listado filtrado en Ver el listado de cartas?"]
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_lista"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
source: "docs/catalogo/cartaspresentacion/flujos/cartas_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el listado de cartas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el listado de cartas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el listado de cartas en Ver el listado de cartas?
- Como ver el listado filtrado en Ver el listado de cartas?

## Donde Entrar

- Cartas Presentacion (`cartaspresentacion.pantalla.cartas_presentacion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el listado de cartas

1. Abrir **lista dl** o **lista todo**.
2. Aparecen las tablas agrupadas con los datos de contacto.
3. Los centros con el tipo de labor mal configurado salen en un aviso al final de la página.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Ver el listado filtrado

1. Abrir **buscar**, rellenar los filtros y pulsar **buscar**.
2. Los resultados aparecen debajo.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion`
- `cartaspresentacion.pantalla.cartas_presentacion_lista`

## Objetivo

Consultar todas las cartas de presentación agrupadas por tipo de labor, delegación y población.

## Errores Documentados

- `Centros con tipo_labor mal configurado aparecen en aviso al pie (html_errores), no como error AJAX.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
