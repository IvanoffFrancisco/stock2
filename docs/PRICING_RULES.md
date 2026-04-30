# PRICING_RULES.md

## Lógica principal

El sistema trabaja con tres listas de precios:

- Lista 1
- Lista 2
- Lista 3

## Regla central

La lista seleccionada arriba en el pedido manda sobre todos los productos agregados.

Esto significa:

- Si se selecciona Lista 1, todos los productos usan Lista 1
- Si se selecciona Lista 2, todos los productos usan Lista 2
- Si se selecciona Lista 3, todos los productos usan Lista 3

## La cantidad no cambia la lista

Aunque un producto tenga muchas o pocas unidades, la lista aplicada no debe cambiar automáticamente.

Ejemplo:

Si el usuario selecciona Lista 3 y agrega 15 unidades, el producto debe calcularse con Lista 3 aunque esa lista normalmente corresponda a otra cantidad.

## Precio manual

Si el usuario carga un valor en `precio_unitario`, ese valor manda sobre la lista.

## Prioridad de cálculo

1. Precio unitario manual
2. Lista seleccionada en el pedido

## Prohibido

- Cambiar automáticamente la lista por cantidad
- Usar una lista distinta a la seleccionada
- Ignorar el precio manual
- Mezclar listas dentro del mismo pedido sin indicación explícita
- Recalcular usando reglas anteriores basadas en cantidad

## Objetivo

Mantener una lógica clara:

> El usuario elige la lista arriba, y esa lista se aplica a todos los productos salvo que cargue precio manual.