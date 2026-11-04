---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Crear un equipaje nuevo"
flujo: "inventario.equipajes_nuevo.gestionar.flujo"
preguntas: ["Como crear un equipaje nuevo en Crear un equipaje nuevo?"]
pantallas_principales: ["inventario.pantalla.equipajes_nuevo"]
fragmentos: []
endpoints: ["/src/inventario/equipajes_nuevo_guardar"]
source: "docs/catalogo/inventario/flujos/equipajes_nuevo.md"
estado_revision: "generado"
---

# Ayuda IA - Crear un equipaje nuevo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear un equipaje nuevo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear un equipaje nuevo en Crear un equipaje nuevo?

## Donde Entrar

- Nuevo equipaje (`inventario.pantalla.equipajes_nuevo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear un equipaje nuevo

1. Abrir **nuevo equipaje**.
2. En **periodo de selección de actividades** elegir el periodo (**todo el año**, un trimestre, **curso ca**, **curso crt** u **otro**), el año, o las fechas **entre … y …**.
3. Pulsar **ver las casas donde se realizan las actividades**.
4. Elegir la casa en el desplegable que aparece; se muestran las actividades de esa casa en el periodo.
5. Marcar las actividades que van en el mismo equipaje y pulsar **seleccionar**.
6. El **nombre del equipaje** viene propuesto como «casa (fechas)»; se puede cambiar. Pulsar **guardar**.
7. La pantalla pasa a **hacer equipajes** con el equipaje ya creado.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_nuevo_guardar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_nuevo`

## Objetivo

Crear un equipaje: la documentación que se lleva a una casa para las actividades de unas fechas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
