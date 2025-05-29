# Componentes de Contabilidad

Este directorio contiene los componentes modulares para la vista de contabilidad, creados a partir de la división del archivo `index.blade.php` original de 1200+ líneas.

## Estructura de Componentes

### 1. `filtros-periodo.blade.php`
- **Propósito**: Filtros de período (mes/año) para seleccionar datos
- **Variables esperadas**: `$mes_actual`, `$anio_actual`
- **Funcionalidad**: Selectores de mes y año con formulario de filtrado

### 2. `tabla-compras.blade.php`
- **Propósito**: Tabla de registro de compras
- **Variables esperadas**: `$compras['compras']` (array de facturas de compra)
- **Campos de datos**:
  - `supplier_name`: Nombre del proveedor
  - `invoice_date`: Fecha de la factura
  - `invoice_number`: Número de factura
  - `products[]`: Array de productos con `description`
  - `emission_type`: Tipo de emisión (mostrado junto a cada producto)
  - `subtotal_without_taxes`: Subtotal sin impuestos
  - `total_value`: Valor total
  - `authorization_number`: Número de autorización para generar enlace RIDE

### 3. `asientos-compras.blade.php`
- **Propósito**: Asientos contables generados por las compras
- **Variables esperadas**: `$compras['compras']`
- **Funcionalidad**: Muestra asientos contables colapsables por cada factura de compra

### 4. `tabla-ventas.blade.php`
- **Propósito**: Tabla de registro de ventas
- **Variables esperadas**: `$ventas['ventas']` (array de facturas de venta)
- **Campos de datos**:
  - `customer_name`: Nombre del cliente
  - `invoice_date`: Fecha de la factura
  - `invoice_number`: Número de factura
  - `products[]`: Array de productos con `description`
  - `emission_type`: Tipo de emisión (mostrado junto a cada producto)
  - `subtotal_without_taxes`: Subtotal sin impuestos
  - `total_value`: Valor total
  - `subtotal_0_iva`: Subtotal con 0% IVA
  - `subtotal_exempt_iva`: Subtotal exento de IVA
  - `authorization_number`: Número de autorización para generar enlace RIDE

### 5. `asientos-ventas.blade.php`
- **Propósito**: Asientos contables generados por las ventas
- **Variables esperadas**: `$ventas['ventas']`
- **Funcionalidad**: Muestra asientos contables colapsables por cada factura de venta

### 6. `resumen-general.blade.php`
- **Propósito**: Resumen general y balance del período
- **Variables esperadas**: `$compras['compras']`, `$ventas['ventas']`
- **Funcionalidad**: 
  - Resumen de compras y ventas
  - Balance general del período
  - Cálculo de utilidad/pérdida y margen

### 7. `tabla-registros.blade.php`
- **Propósito**: Tabla de registros contables manuales
- **Variables esperadas**: `$registros` (array de registros manuales)
- **Funcionalidad**: Tabla con registros contables ingresados manualmente

## Estructura de Datos Esperada

### Formato de Compras (`$compras['compras']`)
```php
[
    [
        'supplier_name' => 'PROVEEDOR S.A.',
        'ruc' => '1234567890001',
        'invoice_number' => '001-001-000001234',
        'authorization_number' => '...',
        'authorization_date' => '25/05/2025 11:50:02',
        'environment' => 'PRODUCCIÓN',
        'emission_type' => 'NORMAL',
        'customer_name' => 'Cliente',
        'customer_id' => '1234567890',
        'invoice_date' => '25/05/2025',
        'products' => [
            [
                'code' => '001',
                'quantity' => 6.00,
                'description' => 'Producto ejemplo',
                'unit_price' => 5.00,
                'total_price' => 30.00
            ]
        ],
        'subtotal_no_iva' => 0.00,
        'subtotal_exempt_iva' => 39.00,
        'subtotal_without_taxes' => 39.00,
        'total_discount' => 0.00,
        'total_value' => 39.00,
        'payment_method' => '01-SIN UTILIZACION DEL SISTEMA FINANCIERO',
        'ride_url' => 'http://...' // opcional
    ]
]
```

### Formato de Ventas (`$ventas['ventas']`)
```php
[
    [
        'supplier_name' => 'MI EMPRESA S.A.',
        'business_name' => 'Mi Negocio',
        'ruc' => '1234567890001',
        'invoice_number' => '001-100-000001459',
        'authorization_number' => '...',
        'authorization_date' => '27/05/2025 20:31:39',
        'environment' => 'PRODUCCIÓN',
        'emission_type' => 'NORMAL',
        'customer_name' => 'Cliente Final',
        'customer_id' => '1234567890',
        'invoice_date' => '27/05/2025',
        'products' => [
            [
                'code' => '19552',
                'quantity' => 1.00,
                'description' => 'Producto vendido',
                'unit_price' => 15.70,
                'total_price' => 15.70
            ]
        ],
        'subtotal_0_iva' => 15.70,
        'subtotal_no_iva' => 0.00,
        'subtotal_exempt_iva' => 0.00,
        'subtotal_without_taxes' => 15.70,
        'total_discount' => 0.00,
        'total_value' => 15.70,
        'ride_url' => 'http://...' // opcional
    ]
]
```

## Uso en la Vista Principal

En `index.blade.php`:

```blade
@include('components.contabilidad.filtros-periodo')
@include('components.contabilidad.tabla-compras')
@include('components.contabilidad.asientos-compras')
@include('components.contabilidad.tabla-ventas')
@include('components.contabilidad.asientos-ventas')
@include('components.contabilidad.resumen-general')
@include('components.contabilidad.tabla-registros')
```

## Beneficios de la Modularización

1. **Mantenibilidad**: Cada componente se puede editar independientemente
2. **Reutilización**: Los componentes pueden usarse en otras vistas
3. **Legibilidad**: Código más organizado y fácil de entender
4. **Colaboración**: Múltiples desarrolladores pueden trabajar en componentes diferentes
5. **Testing**: Componentes individuales pueden probarse por separado

## Cambios Realizados

### v1.2 - Mejoras en Interfaz y Enlaces RIDE (28/05/2025)
- **Cambios en diseño**:
  - Eliminada la columna "Tipo Tarifa" de las tablas
  - Movido el tipo de emisión junto a cada producto/servicio como badge pequeño
  - Mejora en la distribución del espacio en las tablas
- **Corrección de enlaces RIDE**:
  - Implementado generación automática de enlaces RIDE usando `authorization_number`
  - URL del SRI: `https://celcer.sri.gob.ec/comprobantes-electronicos-internet/publico/comprobantes-electronicos.jspa?comprobante={authorization_number}`
  - Enlaces funcionales para consultar documentos electrónicos oficiales
- **Archivos modificados**:
  - `tabla-compras.blade.php`: Removida columna, reorganizado tipo de emisión, enlaces RIDE
  - `tabla-ventas.blade.php`: Removida columna, reorganizado tipo de emisión, enlaces RIDE

### v1.1 - Corrección de Campos de Datos (28/05/2025)
- **Problema identificado**: Los componentes esperaban nombres de campos en español (`proveedor`, `fecha`, `cliente`) pero los datos reales usan nombres en inglés (`supplier_name`, `invoice_date`, `customer_name`)
- **Solución aplicada**:
  - Actualizados todos los componentes para usar los nombres de campo correctos del JSON
  - Corregidos los cálculos de IVA usando `subtotal_without_taxes` en lugar de `subtotal_value`
  - Actualizada la referencia de productos de `nombre` a `description`
  - Corregidos los totales en las tablas para usar los campos correctos
- **Archivos modificados**:
  - `tabla-compras.blade.php`
  - `tabla-ventas.blade.php`
  - `asientos-compras.blade.php`
  - `asientos-ventas.blade.php`
  - `resumen-general.blade.php`

### Mapeo de Campos Corregidos:

| Campo Anterior | Campo Correcto | Descripción |
|----------------|----------------|-------------|
| `proveedor` | `supplier_name` | Nombre del proveedor |
| `cliente` | `customer_name` | Nombre del cliente |
| `fecha` | `invoice_date` | Fecha de factura |
| `numero` | `invoice_number` | Número de factura |
| `tipo_tarifa` | `emission_type` | Tipo de emisión (ahora junto a productos) |
| `subtotal_value` | `subtotal_without_taxes` | Subtotal sin impuestos |
| `iva_value` | `(calculado)` | IVA = total_value - subtotal_without_taxes |
| `producto.nombre` | `producto.description` | Descripción del producto |
| `ride_url` | `authorization_number` | Enlace RIDE generado desde número de autorización |

## Enlaces RIDE

Los enlaces RIDE (Representación Impresa de Documentos Electrónicos) se manejan de forma inteligente:

### 1. **Archivos PDF Locales (Prioridad)**
Si existe el archivo PDF localmente, se enlaza directamente:
- **Compras**: `public/compra/{authorization_number}.pdf`
- **Ventas**: `public/venta/{authorization_number}.pdf`

**Estructura de archivos esperada:**
```
public/
├── compra/
│   ├── 2505202501170589970400120010010000010192024582810.pdf
│   ├── 2505202501170680532000120020010000007530000007211.pdf
│   └── 2705202501179319242400120010010000100171234567812.pdf
└── venta/
    ├── 2705202501171887565900120011000000014570000024013.pdf
    ├── 2705202501171887565900120011000000014590000024014.pdf
    └── 2705202501171887565900120011000000014600000024011.pdf
```

```php
// Verificación automática de archivos locales
$pdfPath = public_path('compra/' . $compra['authorization_number'] . '.pdf');
$pdfExists = file_exists($pdfPath);

@if($pdfExists)
    <a href="{{ asset('compra/' . $compra['authorization_number'] . '.pdf') }}" 
       target="_blank" 
       class="btn btn-sm btn-success">
        <i class="fas fa-file-pdf"></i> RIDE Local
    </a>
@endif
```

### 2. **Enlace SRI (Respaldo)**
Si no existe el archivo PDF local, se muestra enlace al portal del SRI:
```php
<a href="https://celcer.sri.gob.ec/comprobantes-electronicos-internet/publico/comprobantes-electronicos.jspa?comprobante={{ $compra['authorization_number'] }}" 
   target="_blank" 
   class="btn btn-sm btn-outline-secondary">
    <i class="fas fa-external-link-alt"></i> RIDE SRI
</a>
```

### 3. **Indicadores Visuales**
- **🟢 RIDE Local (Compras)**: Botón verde sólido - PDF disponible localmente 
- **🔵 RIDE Local (Ventas)**: Botón azul sólido - PDF disponible localmente
- **⚪ RIDE SRI**: Botón gris outline - Enlace al portal del SRI
- **❌ N/A**: Texto gris - Sin número de autorización disponible

### 4. **Lógica de Priorización**
El sistema verifica automáticamente:
1. ¿Existe `authorization_number`? → Si no: mostrar "N/A"
2. ¿Existe archivo PDF local? → Si sí: botón verde/azul "RIDE Local"
3. Solo queda opción SRI → Botón gris "RIDE SRI"

## Notas Importantes

- Asegúrate de que el controlador pase las variables `$compras` y `$ventas` con la estructura correcta
- Los datos deben venir en formato JSON con los nombres de campo en inglés
- El cálculo de IVA se realiza automáticamente restando el subtotal del total
- Los componentes son compatibles con Bootstrap 5 y FontAwesome
