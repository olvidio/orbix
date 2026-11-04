---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir el lugar dentro del centro"
flujo: "inventario.lista_lugares_de_ubi.gestionar.flujo"
preguntas: ["Como elegir el lugar dentro del centro en Elegir el lugar dentro del centro?"]
pantallas_principales: ["inventario.pantalla.traslado_doc_que"]
fragmentos: ["inventario.pantalla.traslado_doc_que"]
endpoints: ["/src/inventario/lista_lugares_de_ubi"]
source: "docs/catalogo/inventario/flujos/lista_lugares_de_ubi.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el lugar dentro del centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el lugar dentro del centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el lugar dentro del centro en Elegir el lugar dentro del centro?

## Donde Entrar

- Traslado de documentos — filtro (`inventario.pantalla.traslado_doc_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el lugar dentro del centro

1. En **traslado de doc**, elegir un centro (origen o destino).
2. Aparece el desplegable **seleccione un lugar** con los lugares de ese centro.
3. Si falla la carga se muestra «Error al cargar lugares de origen/destino»; volver a elegir el centro.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_lugares_de_ubi`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.traslado_doc_que`
- `inventario.pantalla.traslado_doc_que`

## Objetivo

Cargar los lugares (estancias) de un centro cuando se elige como origen o destino de un traslado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
