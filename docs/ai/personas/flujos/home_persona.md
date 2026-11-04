---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Ver la ficha de una persona"
flujo: "personas.home_persona.gestionar.flujo"
preguntas: ["Como ver la ficha de una persona en Ver la ficha de una persona?"]
pantallas_principales: ["personas.pantalla.home_persona"]
fragmentos: ["personas.pantalla.home_persona"]
endpoints: ["/src/personas/home_persona_data"]
source: "docs/catalogo/personas/flujos/home_persona.md"
estado_revision: "generado"
---

# Ayuda IA - Ver la ficha de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver la ficha de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver la ficha de una persona en Ver la ficha de una persona?

## Donde Entrar

- Cabecera de persona (`personas.pantalla.home_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver la ficha de una persona

1. Se muestra el nombre, la delegación y el centro (por ejemplo «dlb - Diagonal»), nacimiento, santo, celebra, profesión, stgr, teléfonos y e-mail, con el enlace **editar**.
2. Debajo, «Relación de dossiers»: actividades a las que asiste, asignaturas que cursa, cargos en actividades, notas del stgr (tessera), stgr (docencia, certificados, títulos, etc.), traslados y última asistencia. Pulsar uno lo abre.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/home_persona_data`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.home_persona`
- `personas.pantalla.home_persona`

## Objetivo

Ver la cabecera y los dossiers de una persona.

## Errores Documentados

- `No se encuentra la persona`
- `Aviso: persona no válida`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
