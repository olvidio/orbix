---
id: "inventario.equipajes_del_doc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Quitar un documento de una valija"
capacidad: "inventario.equipajes_del_doc.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_del_doc"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Del Doc

## Objetivo De Usuario

Sacar un documento de una valija del equipaje.

## Punto De Entrada

Menú **Inventario > equipajes > hacer equipajes**, en una valija con el botón **quitar documento**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Quitar un documento de una valija

Pasos propuestos:
1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **quitar documento**.
3. Marcar los documentos a quitar y pulsar el botón de quitar (**Cancel** para salir sin cambios).
4. Los documentos dejan de estar en la valija; si falla se muestra el aviso **respuesta**.

Endpoints asociados:
- `/src/inventario/equipajes_del_doc`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_del_doc`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
