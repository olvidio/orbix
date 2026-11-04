---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Modificar una actividad"
flujo: "actividades.actividad_editar.gestionar.flujo"
preguntas: ["Como modificar una actividad en Modificar una actividad?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar"]
endpoints: ["/src/actividades/actividad_editar"]
source: "docs/catalogo/actividades/flujos/actividad_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar una actividad en Modificar una actividad?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar una actividad

1. Marcar la actividad en la lista y pulsar **datos**.
2. Cambiar los campos que hagan falta (fechas, horas, plazas, observaciones, publicado…).
3. Pulsar **Guardar cambios**.
4. Si se guarda bien, se vuelve a la lista de la búsqueda; no hay mensaje de confirmación.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_editar`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`

## Objetivo

Modificar los datos de una actividad existente (fechas, lugar, plazas, observaciones, etc.) sin cambiar su tipo.

## Errores Documentados

- `sesión de permisos no disponible`
- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado + detalle`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
