---
id: "inventario.equipajes_eliminar_grupo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Eliminar una valija"
capacidad: "inventario.equipajes_eliminar_grupo.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_eliminar_grupo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Eliminar Grupo

## Objetivo De Usuario

Borrar una valija entera del equipaje.

## Punto De Entrada

Menú **Inventario > equipajes > hacer equipajes**, en la valija con el botón **eliminar valija**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar una valija

Pasos propuestos:
1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **eliminar valija**.
3. Confirmar la pregunta «¿Está seguro de borrar este grupo?».
4. La valija desaparece del equipaje y los documentos que llevaba dejan de estar en ella (siguen existiendo).

Endpoints asociados:
- `/src/inventario/equipajes_eliminar_grupo`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_eliminar_grupo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
