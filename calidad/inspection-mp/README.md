# Inspección MP

## Endpoints
- `POST /calidad/inspection-mp/import.php` (equivalente funcional a `POST /inspection-mp/import`)
- `GET /calidad/inspection-mp/export.php?id={id}` (equivalente funcional a `GET /inspection-mp/export/{id}`)

## Ejemplo JSON de inserción
```json
{
  "producer_id": 10,
  "inspector_id": 4,
  "inspection_date": "2026-04-13",
  "product": "Arándano",
  "is_organic": 1,
  "total_pallets": 2,
  "details": [
    {
      "pallet_number": "P-001",
      "sample": "M-01",
      "variety_id": 2,
      "defects": {"1": 0.5, "2": 1.2},
      "calibers": {"1": 60, "2": 40},
      "measurement": {"weight": 10.2, "temperature": 2.1, "brix": 12.3, "baxlo": 8.1, "average": 8.17}
    }
  ]
}
```

## Ejemplo de importación Excel
Encabezados esperados en fila 1:

`producer_id | inspector_id | inspection_date | product | is_organic | pallet_number | sample | variety_id | weight | temperature | brix | baxlo | average`

Cada fila representa un pallet.
