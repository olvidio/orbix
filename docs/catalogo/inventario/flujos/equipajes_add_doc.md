---
id: "inventario.equipajes_add_doc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Añadir un documento a una valija"
capacidad: "inventario.equipajes_add_doc.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_add_doc"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Add Doc

## Objetivo De Usuario

Meter un documento en una valija del equipaje.

## Punto De Entrada

Menú **Inventario > equipajes > hacer equipajes**, en una valija con el botón **añadir documento**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Añadir un documento a una valija

Pasos propuestos:
1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **añadir documento**.
3. Elegir el **tipo de documento**; se listan los ejemplares libres. Marcar el ejemplar y pulsar **añadir** (o **cancel**).
4. El documento aparece en la valija; si falla se muestra el aviso **respuesta**.

Endpoints asociados:
- `/src/inventario/equipajes_add_doc`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_add_doc`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
