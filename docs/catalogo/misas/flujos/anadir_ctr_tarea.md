---
id: "misas.anadir_ctr_tarea.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Anadir Ctr Tarea"
capacidad: "misas.anadir_ctr_tarea.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/misas/anadir_ctr_tarea"]
estado_revision: "revisado"
---

# Flujo - Anadir ctr tarea

## Objetivo De Usuario

Añade o elimina una fila de plantilla (centro asociado a tarea) en el editor de plantillas. Rama que=anadir crea Plantilla con semana=-1; rama quitar elimina por id_item.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar plantilla. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/anadir_ctr_tarea`

## Errores Conocidos

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `opción no definida en switch en %s, linea %s`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
