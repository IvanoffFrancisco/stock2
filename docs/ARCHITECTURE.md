# ARCHITECTURE.md

## 🧩 Arquitectura General

El sistema sigue patrón MVC de CodeIgniter 4:

- Controllers → lógica
- Models → DB
- Views → interfaz

## 🔗 Relación entre módulos

productos → precios_productos → pedidos → ventas

## 📦 Módulos principales

- Productos
- Precios
- Pedidos
- Ventas
- Clientes
- Categorías
- Movimientos de Stock
- Métricas

## 🔄 Flujo de Pedidos

### Creación
Un pedido contiene:
- cliente
- fecha
- forma de pago
- lista de precios
- descuento
- productos + cantidades

### Persistencia
Se guarda en:
- pedidos
- pedidos_detalles

### Cálculo de precios

1. Se selecciona una lista de precios
2. TODOS los productos usan esa lista
3. La cantidad NO modifica la lista aplicada
4. Si existe `precio_unitario` manual → tiene prioridad

## 📄 PDFs

Se generan usando Dompdf desde:
- Views/pdf/remito.php