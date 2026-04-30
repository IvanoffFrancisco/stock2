# DATABASE.md

## 📦 Tablas principales

- categorias
- clientes
- productos
- precios_productos
- pedidos
- pedidos_detalles
- ventas
- ventas_detalles
- movimientos_stock
- usuario

## 🔗 Relaciones clave

- productos → precios_productos
- pedidos → pedidos_detalles
- ventas → ventas_detalles

## ⚠️ Reglas importantes

- No modificar tablas existentes sin migración
- No eliminar columnas
- Mantener integridad de relaciones

## 📊 Datos sensibles

- pedidos
- ventas

Estos datos NO deben alterarse bajo ninguna circunstancia