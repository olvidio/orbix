---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Carta Presentacion"
flujo: "cartaspresentacion.carta_presentacion.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_form"]
endpoints: ["/src/cartaspresentacion/carta_presentacion_eliminar", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update"]
source: "docs/catalogo/cartaspresentacion/flujos/carta_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA - Carta Presentacion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Carta Presentacion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion_form`

## Objetivo

Dar de alta, modificar o quitar los datos de presentación de un centro concreto.

## Errores Documentados

- `Faltan id_ubi o id_direccion`
- `No puede modificar datos de otra dl`
- `Carta de presentacion no encontrada`
- `Hay un error, no se ha guardado. / Hay un error, no se ha borrado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
