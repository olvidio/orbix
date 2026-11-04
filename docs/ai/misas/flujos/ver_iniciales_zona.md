---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Cambiar iniciales y color de un sacerdote"
flujo: "misas.ver_iniciales_zona.gestionar.flujo"
preguntas: ["Como cambiar iniciales y color de un sacerdote en Cambiar iniciales y color de un sacerdote?"]
pantallas_principales: ["misas.pantalla.ver_iniciales_zona"]
fragmentos: ["misas.pantalla.ver_iniciales_zona"]
endpoints: ["/src/misas/ver_iniciales_zona_data"]
source: "docs/catalogo/misas/flujos/ver_iniciales_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar iniciales y color de un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar iniciales y color de un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar iniciales y color de un sacerdote en Cambiar iniciales y color de un sacerdote?

## Donde Entrar

- Ver Iniciales Zona (`misas.pantalla.ver_iniciales_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar iniciales y color de un sacerdote

1. En **Iniciales sacd**, elegir la zona.
2. Editar las **Iniciales** en la fila del sacerdote.
3. Elegir el **Color** en la paleta («Más colores», «Elegir otro color» o «Sin color»).

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_iniciales_zona_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_iniciales_zona`
- `misas.pantalla.ver_iniciales_zona`

## Objetivo

Ver y cambiar las iniciales y el color de cada sacerdote de la zona, que se usan en el plan.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
