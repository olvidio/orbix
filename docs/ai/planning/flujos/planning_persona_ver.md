---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Ver el calendario de una persona"
flujo: "planning.planning_persona_ver.gestionar.flujo"
preguntas: ["Como ver el calendario de una persona en Ver el calendario de una persona?"]
pantallas_principales: ["planning.pantalla.planning_persona_que", "planning.pantalla.planning_persona_select"]
fragmentos: ["planning.pantalla.planning_persona_ver"]
endpoints: ["/src/planning/planning_persona_ver_data"]
source: "docs/catalogo/planning/flujos/planning_persona_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el calendario de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el calendario de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el calendario de una persona en Ver el calendario de una persona?

## Donde Entrar

- Planning por persona (filtros) (`planning.pantalla.planning_persona_que`)
- Listado de personas (planning) (`planning.pantalla.planning_persona_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el calendario de una persona

1. Aparece el calendario con los meses del periodo (enero - 2027, febrero - 2027…) y una fila por persona; cada actividad es una barra con su tipo. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
2. **Descargar html** guarda el calendario como página web.

Referencias tecnicas para verificar la respuesta:
- `/src/planning/planning_persona_ver_data`

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_persona_que`
- `planning.pantalla.planning_persona_select`
- `planning.pantalla.planning_persona_ver`

## Objetivo

Ver el calendario de actividades de las personas elegidas.

## Errores Documentados

- `Faltan fechas de periodo`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
