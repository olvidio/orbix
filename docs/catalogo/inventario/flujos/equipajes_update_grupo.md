---
id: "inventario.equipajes_update_grupo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Crear una valija con documentos"
capacidad: "inventario.equipajes_update_grupo.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_update_grupo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Update Grupo

## Objetivo De Usuario

Crear una valija o cambiar los documentos que lleva.

## Punto De Entrada

Menú **Inventario > equipajes > hacer equipajes**, con el botón **crear valija** o **seleccionar**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Crear una valija
Pasos propuestos:
1. Abrir **hacer equipajes** y elegir el equipaje.
2. En **maletas para** (casa), pulsar **nueva valija**: aparece una fila «valija1» (el número va subiendo) con una lista de maletas.
3. Elegir la maleta (por ejemplo «Maleta» o «Maleta Rialb 1 (color groc)»): se muestran los documentos que lleva.
4. La valija queda creada en el equipaje; aparecerá en la hoja impresa como «valija1: Maleta».

Endpoints asociados:
- `/src/inventario/equipajes_update_grupo`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_update_grupo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
