# PDF_GUIDELINES.md

## PDFs actuales

El sistema genera PDF de remito usando Dompdf.

Vista principal:

- app/Views/pdf/remito.php

## PDFs futuros deseados

A futuro se desea poder exportar lista de precios en PDF para entregar a clientes.

Ese PDF debería permitir mostrar:

- Productos
- Molino
- Categoría
- Kilogramos
- Lista 1
- Lista 2
- Lista 3

## Reglas para Dompdf

- Evitar imágenes pesadas
- Evitar logos PNG muy grandes
- Usar rutas absolutas seguras
- Verificar existencia del archivo antes de cargarlo
- No depender de rutas temporales del sistema operativo
- Evitar CSS excesivamente complejo
- Mantener HTML simple y compatible con Dompdf

## Problemas conocidos

### Memoria

Ya hubo errores de memoria al generar PDF.

Por eso:

- No insertar imágenes pesadas
- No usar PNG enormes
- Optimizar el logo
- Evitar tablas extremadamente pesadas sin control

### Logo PNG

El logo a veces se encuentra y a veces no.

Reglas:

- Validar si el archivo existe antes de usarlo
- Usar una ruta consistente desde `FCPATH`
- Tener fallback si el logo no existe
- No romper el PDF si falta el logo

## Reglas para modificar PDFs

Antes de modificar un PDF:

1. Revisar Controller que lo genera
2. Revisar View usada por Dompdf
3. No cambiar nombres de variables sin revisar origen
4. Mantener compatibilidad con datos existentes

## Objetivo

Generar PDFs simples, estables y livianos.