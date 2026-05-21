---
id: "inventario.lista_tipo_doc.gestionar"
tipo: "capacidad"
modulo: "inventario"
nombre: "Gestionar Lista Tipo Doc"
entidades: ["TipoDocOpciones"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_tipo_doc"]
pantallas: ["frontend/inventario/controller/docs_asignar_que.php", "frontend/inventario/controller/equipajes_form_add.php"]
casos_uso: ["src\\inventario\\application\\TipoDocOpcionesData"]
tags: ["doc", "inventario", "lista", "lista_tipo_doc", "tipo"]
estado_revision: "generado"
---

# Gestionar Lista Tipo Doc

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_tipo_doc`.

## Objetivo Funcional

Gestiona TipoDocOpciones. Opciones del desplegable de tipos de documento (lista_tipo_doc.php).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/inventario/lista_tipo_doc`

## Pantallas Relacionadas

- `frontend/inventario/controller/docs_asignar_que.php`
- `frontend/inventario/controller/equipajes_form_add.php`

## Casos De Uso Detectados

- `src\inventario\application\TipoDocOpcionesData`

## Pistas Desde Endpoints

- Opciones del desplegable de tipos de documento (`lista_tipo_doc.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
