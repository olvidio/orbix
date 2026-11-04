---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Generar las actividades de un nuevo curso"
flujo: "actividades.actividad_nuevo_curso_ejecutar.gestionar.flujo"
preguntas: ["Como generar las actividades de un nuevo curso en Generar las actividades de un nuevo curso?"]
pantallas_principales: ["actividades.pantalla.actividad_nuevo_curso"]
fragmentos: []
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
source: "docs/catalogo/actividades/flujos/actividad_nuevo_curso_ejecutar.md"
estado_revision: "generado"
---

# Ayuda IA - Generar las actividades de un nuevo curso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Generar las actividades de un nuevo curso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como generar las actividades de un nuevo curso en Generar las actividades de un nuevo curso?

## Donde Entrar

- Generar actividades del nuevo curso (`actividades.pantalla.actividad_nuevo_curso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Generar las actividades de un nuevo curso

1. Leer los avisos sobre lo que se borra y lo que se crea.
2. Elegir el **año de destino** y el **año de origen**.
3. Enviar el formulario y esperar a que termine.
4. Revisar el resumen o, si se ha pedido, el listado de actividades creadas.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_nuevo_curso`

## Objetivo

Confirmar los años de origen y destino y lanzar la generación de las actividades del nuevo curso. Puede tardar varios minutos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
