---
id: "casas.grupo.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Grupo"
entidades: ["GrupoCasa"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/casas/grupo_eliminar", "/src/casas/grupo_form_data", "/src/casas/grupo_lista_data", "/src/casas/grupo_update"]
pantallas: ["frontend/casas/controller/grupo.php", "frontend/casas/controller/grupo_form.php", "frontend/casas/controller/grupo_lista.php", "frontend/casas/view/grupo.phtml"]
casos_uso: ["src\\casas\\application\\GrupoCasaEliminar", "src\\casas\\application\\GrupoCasaFormData", "src\\casas\\application\\GrupoCasaListaData", "src\\casas\\application\\GrupoCasaUpdate"]
tags: ["casas", "data", "eliminar", "form", "grupo", "lista", "update"]
estado_revision: "generado"
---

# Gestionar Grupo

Propuesta generada automaticamente a partir de endpoints con prefijo comun `grupo`.

## Objetivo Funcional

Gestiona GrupoCasa. Crea o actualiza un GrupoCasa. Datos del formulario GrupoCasa (nuevo/editar). Elimina un GrupoCasa. Listado de GrupoCasa (relaciones padre ↔ hijo).

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `listar`
- `ver_formulario`

## Endpoints

- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_form_data`
- `/src/casas/grupo_lista_data`
- `/src/casas/grupo_update`

## Pantallas Relacionadas

- `frontend/casas/controller/grupo.php`
- `frontend/casas/controller/grupo_form.php`
- `frontend/casas/controller/grupo_lista.php`
- `frontend/casas/view/grupo.phtml`

## Casos De Uso Detectados

- `src\casas\application\GrupoCasaEliminar`
- `src\casas\application\GrupoCasaFormData`
- `src\casas\application\GrupoCasaListaData`
- `src\casas\application\GrupoCasaUpdate`

## Pistas Desde Endpoints

- Endpoint backend: crea o actualiza un `GrupoCasa`.
- Endpoint backend: datos del formulario `GrupoCasa` (nuevo/editar).
- Endpoint backend: elimina un `GrupoCasa`.
- Endpoint backend: listado de `GrupoCasa` (relaciones padre ↔ hijo).

## Errores Conocidos

- `Hay un error, no se ha eliminado.`
- `Hay un error, no se ha guardado.`
- `No puede ser la misma casa`
- `debe indicar el grupo a eliminar`
- `debe indicar las dos casas`
- `no se encuentra el grupo`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
