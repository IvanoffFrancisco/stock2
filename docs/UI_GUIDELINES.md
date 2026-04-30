# UI_GUIDELINES.md

## Objetivo visual

Mantener una interfaz clara, ordenada y fácil de usar para administración de stock, pedidos y ventas.

## Framework visual

El sistema usa Bootstrap en las vistas PHP.

También puede tener estilos CSS propios en algunas páginas.

## Reglas generales

- Mantener el layout existente
- Mantener los iconos existentes
- Mantener tarjetas cuando ya existan
- No romper formularios existentes
- No cambiar nombres de inputs sin revisar el Controller
- No cambiar estructura de datos enviados al backend

## Mejoras permitidas

Se pueden proponer o aplicar mejoras en:

- Formularios
- Tablas
- Botones
- Iconos
- Tarjetas
- Espaciados
- Filtros
- Buscadores
- Estados visuales

## Regla importante

Si una mejora visual puede afectar lógica, datos o formularios, primero debe explicarse antes de cambiarse.

## Formularios

Los formularios deben ser:

- Claros
- Ordenados
- Responsivos
- Fáciles de completar
- Compatibles con los datos que espera el Controller

No se deben eliminar campos importantes.

## Tablas

Las tablas deben priorizar:

- Buena lectura
- Encabezados claros
- Acciones visibles
- Botones consistentes
- Filtros superiores cuando corresponda

## Tarjetas

Cuando una página ya usa tarjetas:

- Mantener ese estilo
- Mejorar iconos si suma claridad
- Evitar rediseños agresivos
- Mantener jerarquía visual

## Cambios visuales dudosos

Si Codex detecta un diseño raro o una mejora grande, debe sugerirla primero antes de aplicarla directamente.

Ejemplo:

> “Esta tabla podría organizarse mejor separando filtros, acciones y datos. Sugiero este cambio antes de modificar.”