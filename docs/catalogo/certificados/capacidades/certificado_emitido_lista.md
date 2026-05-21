---
id: "certificados.certificado_emitido_lista.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Emitido Lista"
entidades: ["CertificadoEmitidoLista"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_lista_datos"]
pantallas: ["frontend/certificados/controller/certificado_emitido_lista.php"]
casos_uso: []
tags: ["certificado", "certificado_emitido_lista", "certificados", "datos", "emitido", "lista"]
estado_revision: "generado"
---

# Gestionar Certificado Emitido Lista

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_emitido_lista`.

## Objetivo Funcional

Gestiona CertificadoEmitidoLista. Esta página muestra una tabla con los certificados.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/certificados/certificado_emitido_lista_datos`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_lista.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Esta página muestra una tabla con los certificados.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
