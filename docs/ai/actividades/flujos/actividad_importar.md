---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Importar actividades de otra dl"
flujo: "actividades.actividad_importar.gestionar.flujo"
preguntas: ["Como importar actividades de otra dl en Importar actividades de otra dl?"]
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_importar"]
source: "docs/catalogo/actividades/flujos/actividad_importar.md"
estado_revision: "generado"
---

# Ayuda IA - Importar actividades de otra dl

Usa este documento para responder preguntas de usuario sobre como trabajar con `Importar actividades de otra dl`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como importar actividades de otra dl en Importar actividades de otra dl?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Importar actividades de otra dl

1. Abrir la opción de importar del menú.
2. Rellenar el formulario de búsqueda y pulsar buscar para localizar la actividad de origen.
3. En la lista, marcar las actividades que se quieren importar y pulsar **importar**.
4. Revisar los mensajes: sale uno por cada actividad importada o con error.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_importar`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_que`
- `actividades.pantalla.actividad_select`

## Objetivo

Buscar actividades de otra delegación o región, elegir una o varias e importarlas a la propia.

## Errores Documentados

- `hay un error, no se ha importado + detalle (por id fallido)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
