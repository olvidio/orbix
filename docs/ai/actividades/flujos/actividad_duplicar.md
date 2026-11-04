---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Duplicar una actividad"
flujo: "actividades.actividad_duplicar.gestionar.flujo"
preguntas: ["Como duplicar una actividad en Duplicar una actividad?"]
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_duplicar"]
source: "docs/catalogo/actividades/flujos/actividad_duplicar.md"
estado_revision: "generado"
---

# Ayuda IA - Duplicar una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Duplicar una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como duplicar una actividad en Duplicar una actividad?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Duplicar una actividad

1. Marcar la actividad en la lista y pulsar **duplicar**.
2. Confirmar el aviso «Seguro que desea duplicar esta actividad».
3. La copia se llama «dup » + el nombre original y conserva fechas y plazas; la lista se vuelve a cargar con la búsqueda por defecto, así que hay que buscar de nuevo para ver la copia.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_duplicar`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_select`

## Objetivo

Crear una copia de una actividad con las mismas fechas, plazas y demás datos.

## Errores Documentados

- `no se ha seleccionado ninguna actividad`
- `actividad no encontrada`
- `no se puede duplicar actividades que no sean de la propia dl`
- `hay un error, no se ha guardado + detalle`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
