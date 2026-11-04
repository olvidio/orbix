---
id: "personas.personas_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Abrir los datos personales de una persona"
capacidad: "personas.personas_editar.gestionar"
pantallas_principales: ["personas.pantalla.personas_editar"]
fragmentos: ["personas.pantalla.personas_editar"]
acciones: ["obtener_datos", "alta"]
endpoints: ["/src/personas/personas_editar_data"]
estado_revision: "revisado"
---

# Flujo - Abrir ficha de persona

Carga del formulario de alta o edición según colectivo y permisos.

## Objetivo De Usuario

Abrir los datos personales de una persona para consultarlos o cambiarlos, o dar de alta una persona.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**, **n de paso**, **agd r/dl**; también en vsm, vsg y scdl). Ficha de la persona > **editar**.

## Escenarios Inferidos

### Abrir los datos personales de una persona

Pasos propuestos:
1. En la ficha pulsar **editar**. Se abre «DATOS PERSONALES» con **Delegación**, **Trato**, **Nombre**, **Apelativo**, **nx1**, **Primer apellido**, **nx2**, **Segundo Apellido**, **Lugar de nacimiento**, **Fecha Nacimiento**, **Situacion**, **Fecha situación**, **Idioma preferido**, **Profesión**, **sacd**, **Stgr**, **eap**, **inc**, **Fecha inc**, **ce**, **Lugar ce**, **Año inicio ce**, **Año fin ce** y **Observaciones**.
2. Al lado aparece el **Centro-sede**.

Endpoints asociados:
- `/src/personas/personas_editar_data`

## Endpoints Del Flujo

- `/src/personas/personas_editar_data`

## Errores Conocidos

- `No se ha pasado el id_nom`
- `No se encuentra la persona`

## Ruta de menú

- sin entrada de menú en el índice.
