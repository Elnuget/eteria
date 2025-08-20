# API de Datos de Ventas Farmacéuticas - Prueba Técnica

## Descripción
Esta API proporciona acceso a los datos de ventas farmacéuticas para la prueba técnica. Los datos incluyen información sobre productos farmacéuticos vendidos en diferentes ciudades de Ecuador.

## Endpoints Disponibles

### 1. Obtener Datos de Ventas
**GET** `/api/prueba-tecnica-farma/datos-ventas`

#### Parámetros de consulta (opcionales):
- `producto`: Filtrar por nombre de producto (búsqueda parcial, case-insensitive)
- `ciudad`: Filtrar por ciudad (búsqueda parcial, case-insensitive)
- `mes`: Filtrar por mes en formato `YYYY-MM` (ej: `2025-01`)
- `canal`: Filtrar por canal de venta (búsqueda parcial, case-insensitive)

#### Ejemplos de uso:

##### Obtener todos los datos:
```bash
GET /api/prueba-tecnica-farma/datos-ventas
```

##### Filtrar por producto:
```bash
GET /api/prueba-tecnica-farma/datos-ventas?producto=DolorFree
```

##### Filtrar por ciudad y mes:
```bash
GET /api/prueba-tecnica-farma/datos-ventas?ciudad=Quito&mes=2025-01
```

##### Filtrar por canal de venta:
```bash
GET /api/prueba-tecnica-farma/datos-ventas?canal=Farmacia
```

#### Respuesta exitosa (200):
```json
{
  "success": true,
  "metadata": {
    "descripcion": "Datos de ventas farmacéuticas para prueba técnica",
    "formato": "JSON",
    "codificacion": "UTF-8",
    "fecha_creacion": "2025-01-31",
    "moneda": "USD"
  },
  "ventas": [
    {
      "id": 1,
      "fecha": "2025-01-31",
      "pais": "Ecuador",
      "ciudad": "Quito",
      "producto": "DolorFree 500mg",
      "unidades_vendidas": 1200,
      "precio_unitario": 4.50,
      "canal_venta": "Farmacia",
      "total_venta": 5400.00
    }
  ],
  "resumen": {
    "total_registros": 6,
    "total_unidades_vendidas": 7680,
    "total_ingresos": 30530.00,
    "productos_unicos": ["DolorFree 500mg", "VitaBoost C1000"],
    "ciudades_unicas": ["Quito", "Guayaquil", "Cuenca"],
    "canales_venta": ["Farmacia", "E-commerce"]
  },
  "filtros_aplicados": {
    "producto": null,
    "ciudad": null,
    "mes": null,
    "canal": null
  }
}
```

#### Respuesta de error (404):
```json
{
  "error": "Archivo de datos no encontrado"
}
```

#### Respuesta de error (500):
```json
{
  "error": "Error interno del servidor: [mensaje de error]"
}
```

## Datos Disponibles

### Productos:
- **DolorFree 500mg**: Analgésico vendido principalmente en farmacias
- **VitaBoost C1000**: Suplemento vitamínico vendido principalmente por e-commerce

### Ciudades:
- **Quito**: Capital de Ecuador
- **Guayaquil**: Puerto principal de Ecuador
- **Cuenca**: Ciudad histórica del sur

### Canales de Venta:
- **Farmacia**: Venta tradicional en establecimientos físicos
- **E-commerce**: Venta online

### Período de Datos:
- **Enero 2025**: Datos del 31 de enero
- **Febrero 2025**: Datos del 28 de febrero

## Casos de Uso para la Prueba Técnica

### 1. Informe Mensual de Ventas
```javascript
// Obtener ventas de enero 2025
fetch('/api/prueba-tecnica-farma/datos-ventas?mes=2025-01')
  .then(response => response.json())
  .then(data => {
    // Procesar datos para generar informe PDF/Excel
  });
```

### 2. Análisis por Producto
```javascript
// Obtener ventas de DolorFree
fetch('/api/prueba-tecnica-farma/datos-ventas?producto=DolorFree')
  .then(response => response.json())
  .then(data => {
    // Analizar rendimiento del producto
  });
```

### 3. Comparativa por Canal
```javascript
// Obtener ventas por farmacia vs e-commerce
Promise.all([
  fetch('/api/prueba-tecnica-farma/datos-ventas?canal=Farmacia'),
  fetch('/api/prueba-tecnica-farma/datos-ventas?canal=E-commerce')
]).then(responses => {
  // Comparar canales de venta
});
```

## Integración con IA

Los datos pueden ser utilizados con servicios de IA para:

1. **Análisis de tendencias**: Identificar patrones de venta
2. **Predicciones**: Estimar ventas futuras
3. **Recomendaciones**: Sugerir estrategias de marketing
4. **Clasificación**: Categorizar productos por rendimiento

### Ejemplo con OpenAI:
```javascript
const ventasData = await fetch('/api/prueba-tecnica-farma/datos-ventas').then(r => r.json());

const prompt = `
Analiza estos datos de ventas farmacéuticas y genera un informe ejecutivo:
${JSON.stringify(ventasData.ventas)}

Incluye:
- Tendencias principales
- Productos más rentables
- Recomendaciones estratégicas
`;

// Enviar a OpenAI API para análisis
```

## Archivos de Datos

Los datos también están disponibles en formato de archivos:

- **JSON**: `/public/data/datos_ventas.json`
- **CSV**: `/public/data/datos_ventas.csv`

Estos archivos pueden ser descargados directamente o utilizados como respaldo para el procesamiento offline.
