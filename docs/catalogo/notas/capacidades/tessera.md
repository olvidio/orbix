---
id: "notas.tessera.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Tessera"
entidades: ["Tessera"]
acciones: ["copiar"]
endpoints: ["/src/notas/tessera_copiar"]
pantallas: ["frontend/notas/controller/tessera_copiar_select.php", "frontend/notas/view/tessera_copiar_select.phtml"]
casos_uso: ["src\\notas\\application\\TesseraCopiar"]
tags: ["copiar", "notas", "tessera"]
estado_revision: "generado"
---

# Gestionar Tessera

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tessera`.

## Objetivo Funcional

Gestiona Tessera. Copia todas las PersonaNota de una persona origen hacia una persona destino. Utilizado por personas_select.phtml (pagina de traslado de tessera entre numerarios / supernumerarios). Devuelve una cadena con los errores (separados por <br>) o vacia si todo ha ido bien.

## Acciones Detectadas

- `copiar`

## Endpoints

- `/src/notas/tessera_copiar`

## Pantallas Relacionadas

- `frontend/notas/controller/tessera_copiar_select.php`
- `frontend/notas/view/tessera_copiar_select.phtml`

## Casos De Uso Detectados

- `src\notas\application\TesseraCopiar`

## Pistas Desde Endpoints

- Copia todas las `PersonaNota` de una persona origen hacia una persona destino. Utilizado por `personas_select.phtml` (pagina de traslado de tessera entre numerarios / supernumerarios). Devuelve una cadena con los errores (separados por `<br>`) o vacia si todo ha ido bien.

## Errores Conocidos

- `No se han recibido las personas de origen y destino`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
