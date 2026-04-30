# AGENTS.md

## 🧠 Contexto del Proyecto
Sistema de gestión de stock, pedidos y ventas desarrollado en CodeIgniter 4.

## 🏗 Stack Tecnológico
- Backend: CodeIgniter 4 (PHP)
- Frontend: PHP Views (Server Rendered)
- Base de Datos: MariaDB
- PDFs: Dompdf
- Entorno: XAMPP / Local
- Ejecución: `php spark serve`

## 📁 Estructura Principal

El proyecto sigue arquitectura MVC:

- Controllers: lógica de negocio
- Models: acceso a base de datos
- Views: interfaz en PHP

Ejemplo de estructura real:
:contentReference[oaicite:0]{index=0}

## ⚙️ Convenciones

- Todas las operaciones pasan por Controllers
- No acceder a DB directamente desde Views
- Los cálculos se realizan en Controllers o Models
- Mantener consistencia con CodeIgniter 4

## 🚨 Reglas Clave

- No romper lógica existente
- No modificar estructura de base de datos sin justificación
- Mantener compatibilidad con datos existentes
- Respetar flujo: productos → precios → pedidos → ventas

## 🧩 Funcionalidades principales

- Gestión de productos
- Gestión de precios por listas
- Pedidos con cálculo dinámico
- Ventas
- Movimientos de stock
- Generación de PDFs (remitos)