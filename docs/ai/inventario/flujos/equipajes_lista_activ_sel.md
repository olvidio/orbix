---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Nombrar el equipaje"
flujo: "inventario.equipajes_lista_activ_sel.gestionar.flujo"
preguntas: ["Como nombrar el equipaje en Nombrar el equipaje?"]
pantallas_principales: ["inventario.pantalla.equipajes_form_nuevo"]
fragmentos: ["inventario.pantalla.equipajes_form_nuevo"]
endpoints: ["/src/inventario/equipajes_lista_activ_sel"]
source: "docs/catalogo/inventario/flujos/equipajes_lista_activ_sel.md"
estado_revision: "generado"
---

# Ayuda IA - Nombrar el equipaje

Usa este documento para responder preguntas de usuario sobre como trabajar con `Nombrar el equipaje`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como nombrar el equipaje en Nombrar el equipaje?

## Donde Entrar

- Fragmento selección actividades (`inventario.pantalla.equipajes_form_nuevo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Nombrar el equipaje

1. Marcar las actividades y pulsar **seleccionar**.
2. En la ventana aparece el **nombre del equipaje** ya propuesto como «casa (fechas)»; se puede cambiar.
3. Pulsar **guardar** (o **cancel** para cerrar sin crear nada).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_lista_activ_sel`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_form_nuevo`
- `inventario.pantalla.equipajes_form_nuevo`

## Objetivo

Ponerle nombre al equipaje formado con las actividades marcadas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
