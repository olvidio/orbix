---
id: "cartaspresentacion.carta_presentacion.gestionar"
tipo: "capacidad"
modulo: "cartaspresentacion"
nombre: "Gestionar Carta Presentacion"
entidades: ["CartaPresentacion"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/cartaspresentacion/carta_presentacion_eliminar", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update"]
pantallas: ["frontend/cartaspresentacion/controller/cartas_presentacion_form.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartaPresentacionEliminar", "src\\cartaspresentacion\\application\\CartaPresentacionFormData", "src\\cartaspresentacion\\application\\CartaPresentacionUpdate"]
tags: ["carta", "carta_presentacion", "cartaspresentacion", "data", "eliminar", "form", "presentacion", "update"]
estado_revision: "generado"
---

# Gestionar Carta Presentacion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `carta_presentacion`.

## Objetivo Funcional

Gestiona CartaPresentacion. Crea / actualiza una CartaPresentacion. Datos del formulario de modificacion de una CartaPresentacion (valida permisos: solo dl propia o cr). Elimina una CartaPresentacion.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `ver_formulario`

## Endpoints

- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`

## Pantallas Relacionadas

- `frontend/cartaspresentacion/controller/cartas_presentacion_form.php`

## Casos De Uso Detectados

- `src\cartaspresentacion\application\CartaPresentacionEliminar`
- `src\cartaspresentacion\application\CartaPresentacionFormData`
- `src\cartaspresentacion\application\CartaPresentacionUpdate`

## Pistas Desde Endpoints

- Endpoint backend: crea / actualiza una `CartaPresentacion`.
- Endpoint backend: datos del formulario de modificacion de una `CartaPresentacion` (valida permisos: solo dl propia o `cr`).
- Endpoint backend: elimina una `CartaPresentacion`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
