---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Cargar datos de ficha actividad"
flujo: "actividades.actividad_ver.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
endpoints: ["/src/actividades/actividad_ver_datos"]
source: "docs/catalogo/actividades/flujos/actividad_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Cargar datos de ficha actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cargar datos de ficha actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Objetivo

Al abrir ver/editar/nuevo/planning, el sistema carga en servidor los datos necesarios para pintar la ficha sin acceder a `src/` desde el navegador.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
