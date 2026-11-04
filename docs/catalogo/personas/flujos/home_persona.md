---
id: "personas.home_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Ver la ficha de una persona"
capacidad: "personas.home_persona.gestionar"
pantallas_principales: ["personas.pantalla.home_persona"]
fragmentos: ["personas.pantalla.home_persona"]
acciones: ["ver_resumen", "ir_ficha", "ir_dossiers"]
endpoints: ["/src/personas/home_persona_data"]
estado_revision: "revisado"
---

# Flujo - Ver cabecera de persona

Muestra el resumen de una persona y enlaces a ficha y dossiers.

## Objetivo De Usuario

Ver la cabecera y los dossiers de una persona.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**, **n de paso**, **agd r/dl**; también en vsm, vsg y scdl). Pulsar el nombre de la persona.

## Escenarios Inferidos

### Ver la ficha de una persona

Pasos propuestos:
1. Se muestra el nombre, la delegación y el centro (por ejemplo «dlb - Diagonal»), nacimiento, santo, celebra, profesión, stgr, teléfonos y e-mail, con el enlace **editar**.
2. Debajo, «Relación de dossiers»: actividades a las que asiste, asignaturas que cursa, cargos en actividades, notas del stgr (tessera), stgr (docencia, certificados, títulos, etc.), traslados y última asistencia. Pulsar uno lo abre.

Endpoints asociados:
- `/src/personas/home_persona_data`

## Endpoints Del Flujo

- `/src/personas/home_persona_data`

## Errores Conocidos

- `No se encuentra la persona`
- Aviso: persona no válida

## Ruta de menú

- sin entrada de menú en el índice.
