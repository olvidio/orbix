---
id: "misas.cuadricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Cuadricula"
capacidad: "misas.cuadricula.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["crear_actualizar"]
endpoints: ["/src/misas/cuadricula_update"]
estado_revision: "revisado"
---

# Flujo - Cuadricula

## Objetivo De Usuario

Asigna, actualiza o borra un EncargoDia en una celda de la cuadrícula y recalcula metadatos de color/texto para la fila SACD y la celda misa.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar plan. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plan.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/misas/cuadricula_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/cuadricula_update`

## Errores Conocidos

- `Falta el id_item`
- `Este día tiene más de dos Misas`
- `Este día tiene dos Misas`
- `Este día no tiene ninguna Misa`
- `Tiene dos Misas a primera hora`
- `No está en la zona y tiene Misa a primera hora`
- `Está en `
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plan
