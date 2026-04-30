# MODULES.md

## Módulos del sistema

El sistema está organizado en módulos principales:

- Clientes
- Productos
- Precios
- Pedidos
- Ventas
- Categorías
- Métricas

## Clientes

Módulo encargado de administrar los datos de los clientes.

Debe permitir:
- Crear clientes
- Listar clientes
- Editar datos de clientes
- Usar clientes dentro de pedidos y ventas

## Productos

Módulo encargado de administrar los productos disponibles.

Debe permitir:
- Crear productos
- Editar productos
- Listar productos
- Filtrar por nombre
- Filtrar por molino
- Asociar productos con categorías
- Mantener campos importantes como nombre, tipo, kilogramos, molino y stock

## Precios

Módulo encargado de administrar precios por producto.

El sistema trabaja con listas de precios:

- Lista 1
- Lista 2
- Lista 3

Debe permitir:
- Crear precios
- Editar precios
- Filtrar precios por producto
- Filtrar precios por molino
- Mantener relación con productos

## Pedidos

Módulo central del sistema.

Un pedido se crea cargando:
- Cliente
- Fecha de pedido
- Forma de pago
- Lista de precios
- Descuento, si corresponde
- Productos
- Unidades

El pedido se guarda en:
- pedidos
- pedidos_detalles

La lógica de precios debe respetar `PRICING_RULES.md`.

## Ventas

Módulo relacionado con pedidos confirmados o entregados.

Debe mantener relación con:
- ventas
- ventas_detalles
- pedidos
- productos
- stock

Los datos de ventas son sensibles y no deben modificarse sin cuidado.

## Categorías

Módulo para clasificar productos.

Debe permitir:
- Crear categorías
- Editar categorías
- Eliminar categorías solo si no rompe relaciones existentes

## Métricas

Módulo para visualizar información de ventas y rendimiento.

Puede incluir:
- Ventas por mes
- Ventas por vendedor
- Ventas por molino
- Cantidad de pedidos
- Filtros por fecha
- Comparación con meses anteriores

## Flujo general

productos → precios → pedidos → ventas

Este flujo no debe romperse.