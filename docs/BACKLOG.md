# BACKLOG.md

## Pendientes principales

### 1. Mejorar página de precios

Objetivo:
- Mejorar diseño de la página de precios
- Ordenar mejor la información
- Agregar filtros útiles
- Preparar exportación a PDF

Tareas:
- [x] Mejorar tabla de precios
- [x] Agregar filtros por producto
- [x] Agregar filtros por molino
- [x] Agregar filtros por categoría
- [x] Agregar botón para exportar lista de precios en PDF
- [x] Crear vista PDF para lista de precios
- [x] Crear método en Controller para exportar PDF

---

### 2. Filtros en movimientos de stock

Objetivo:
Facilitar la búsqueda y control de movimientos.

Filtros deseados:
- [ ] Fecha desde / fecha hasta
- [ ] Pedido
- [ ] Vendedor
- [ ] Producto
- [ ] Molino
- [ ] Tipo de movimiento
- [ ] Cliente, si corresponde

---

### 3. Mejorar select de productos en nuevo pedido

Objetivo:
Hacer más fácil buscar productos al crear un pedido.

Tareas:
- [ ] Mejorar buscador de productos
- [ ] Permitir búsqueda por nombre
- [ ] Permitir búsqueda por molino
- [ ] Mostrar datos útiles del producto
- [ ] Evitar seleccionar productos incorrectos
- [ ] Mantener compatibilidad con el formulario actual

---

### 4. Mejorar select de clientes en nuevo pedido

Objetivo:
Hacer más fácil buscar clientes al crear un pedido.

Tareas:
- [ ] Mejorar buscador de clientes
- [ ] Permitir búsqueda por nombre
- [ ] Permitir búsqueda por teléfono, si existe
- [ ] Permitir búsqueda por dirección, si existe
- [ ] Mantener compatibilidad con el formulario actual

---

### 5. Corregir tarjeta de stock bajo

Objetivo:
Mostrar solo productos con stock bajo.

Regla:
- La tarjeta de stock bajo debe listar únicamente productos cuyo stock actual sea menor o igual al stock mínimo configurado.

Tareas:
- [ ] Revisar consulta actual
- [ ] Confirmar campo de stock mínimo
- [ ] Mostrar solo productos con stock bajo
- [ ] Evitar mezclar productos sin stock si corresponde mostrarlos aparte

---

### 6. Corregir tarjeta sin stock

Objetivo:
Mostrar solo productos sin stock.

Regla:
- La tarjeta sin stock debe listar únicamente productos con stock igual a 0.

Tareas:
- [ ] Revisar consulta actual
- [ ] Separar lógica de stock bajo y sin stock
- [ ] Mostrar solo productos con stock 0
- [ ] Evitar duplicados entre tarjetas
