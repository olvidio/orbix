---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Ver el planning de una zona"
flujo: "planning.planning_zones_select.gestionar.flujo"
preguntas: ["Como ver el planning de una zona en Ver el planning de una zona?"]
pantallas_principales: ["planning.pantalla.planning_zones_que"]
fragmentos: ["planning.pantalla.planning_zones_select"]
endpoints: ["/src/planning/planning_zones_select_data"]
source: "docs/catalogo/planning/flujos/planning_zones_select.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el planning de una zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el planning de una zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el planning de una zona en Ver el planning de una zona?

## Donde Entrar

- Planning por zonas SACD (filtros) (`planning.pantalla.planning_zones_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el planning de una zona

1. Aparece el calendario con el nombre de la zona, los meses elegidos en columnas y una fila por sacerdote. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
2. **Descargar html** guarda el calendario como página web.

Referencias tecnicas para verificar la respuesta:
- `/src/planning/planning_zones_select_data`

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_zones_que`
- `planning.pantalla.planning_zones_select`

## Objetivo

Ver el calendario de los sacerdotes de una zona.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
