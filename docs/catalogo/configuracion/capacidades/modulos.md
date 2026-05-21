---
id: "configuracion.modulos.gestionar"
tipo: "capacidad"
modulo: "configuracion"
nombre: "Gestionar Modulos"
entidades: ["Modulos", "ModulosUpdateAction"]
acciones: ["crear_actualizar", "ver_formulario"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
pantallas: ["frontend/configuracion/controller/modulos_form.php", "frontend/configuracion/controller/modulos_update.php"]
casos_uso: ["src\\configuracion\\application\\ModulosFormData", "src\\configuracion\\application\\ModulosUpdateAction"]
tags: ["configuracion", "data", "form", "modulos", "update"]
estado_revision: "generado"
---

# Gestionar Modulos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `modulos`.

## Objetivo Funcional

Gestiona Modulos, ModulosUpdateAction. Alta / baja / modificación de módulos (respuesta texto plano para AJAX legacy). JSON para {.

## Acciones Detectadas

- `crear_actualizar`
- `ver_formulario`

## Endpoints

- `/src/configuracion/modulos_form_data`
- `/src/configuracion/modulos_update`

## Pantallas Relacionadas

- `frontend/configuracion/controller/modulos_form.php`
- `frontend/configuracion/controller/modulos_update.php`

## Casos De Uso Detectados

- `src\configuracion\application\ModulosFormData`
- `src\configuracion\application\ModulosUpdateAction`

## Pistas Desde Endpoints

- Alta / baja / modificación de módulos (respuesta texto plano para AJAX legacy).
- JSON para {

## Errores Conocidos

- `hay un error, no se ha eliminado`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
