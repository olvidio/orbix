---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Elegir el lugar de una actividad"
flujo: "actividades.actividad_select_ubi_desplegable.gestionar.flujo"
preguntas: ["Como elegir el lugar de una actividad en Elegir el lugar de una actividad?"]
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: []
endpoints: ["/src/actividades/actividad_select_ubi_desplegable"]
source: "docs/catalogo/actividades/flujos/actividad_select_ubi_desplegable.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el lugar de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el lugar de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el lugar de una actividad en Elegir el lugar de una actividad?

## Donde Entrar

- Seleccionar lugar (popup) (`actividades.pantalla.actividad_select_ubi`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el lugar de una actividad

1. En la ventana de lugar, elegir el tipo de búsqueda (por ejemplo historial o región) y quién organiza.
2. Se carga el desplegable de casas o ubicaciones posibles.
3. Al elegir una, se rellenan los datos de lugar en la pantalla desde la que se abrió la ventana.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_select_ubi_desplegable`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_select_ubi`

## Objetivo

En la ventana para elegir lugar, al escoger buscar por historial o por región, cargar las casas o ubicaciones candidatas antes de confirmar.

## Errores Documentados

- `opción no definida: tipo=…`
- `falta saber quien organiza (modo freq sin dl_org)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
