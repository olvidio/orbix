---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Modificar un acta"
flujo: "notas.acta_modificar.gestionar.flujo"
preguntas: ["Como modificar un acta en Modificar un acta?"]
pantallas_principales: ["notas.pantalla.acta_ver"]
fragmentos: ["notas.pantalla.acta_ver"]
endpoints: ["/src/notas/acta_modificar"]
source: "docs/catalogo/notas/flujos/acta_modificar.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar un acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar un acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar un acta en Modificar un acta?

## Donde Entrar

- Acta Ver (`notas.pantalla.acta_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar un acta

1. Abrir el acta desde **actas**.
2. Cambiar la fecha, libro, página, línea, lugar, observaciones, la asignatura o los examinadores; el número de acta ya no se cambia. La fecha es obligatoria.
3. Pulsar **Guardar cambios acta**. Un acta que ya tiene el PDF firmado adjunto no se puede modificar.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_modificar`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_ver`
- `notas.pantalla.acta_ver`

## Objetivo

Guardar los cambios de un acta existente.

## Errores Documentados

- `No se encuentra el acta`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
