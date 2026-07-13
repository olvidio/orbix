---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Cartas Presentacion"
flujo: "cartaspresentacion.cartas_presentacion.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_lista"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
source: "docs/catalogo/cartaspresentacion/flujos/cartas_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA - Cartas Presentacion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cartas Presentacion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion_lista`

## Objetivo

Consultar todas las cartas de presentación organizadas por tipo de labor, delegación y población.

## Errores Documentados

- `Centros con tipo_labor mal configurado aparecen en aviso al pie (html_errores), no como error AJAX.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
