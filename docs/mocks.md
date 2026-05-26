# Mock Data

Los mocks son archivos JSON en `resources/mocks/` que simulan respuestas de base de datos. Se cargan en los cierres de rutas con:

```php
$data = json_decode(file_get_contents(resource_path('mocks/filename.json')), true);
```

---

## events.json

**Ruta:** `resources/mocks/events.json`
**Usado en:** `/catalog`, `/events/{id}`, `/events/{id}/seats`

### Estructura

```json
{
  "featured": {
    "hero": {
      "id": 1,
      "title": "string",
      "badge": "string",
      "description": "string",
      "image": null
    },
    "grid": [
      {
        "id": 2,
        "title": "string",
        "category": "string",
        "meta": "string",
        "meta_icon": "material-symbol-name",
        "image": null
      }
    ]
  },
  "upcoming": [
    {
      "id": 4,
      "title": "string",
      "subtitle": "string",
      "category": "string",
      "author": "string",
      "venue": "string",
      "city": "string",
      "duration": "string",
      "price": "45.000",
      "day": "14",
      "month": "JUN",
      "times": ["19:30", "21:00"],
      "synopsis": ["párrafo 1", "párrafo 2"],
      "dates": [
        { "dow": "Vie", "day": "13", "month": "Jun" }
      ]
    }
  ]
}
```

### Notas de campos

| Campo | Formato | Observación |
|-------|---------|-------------|
| `id` | `int` | Identificador para resolución de rutas (`/events/{id}`) |
| `price` | `string` con puntos como miles | Se convierte a `int` en seats via `(int) str_replace('.', '', $event['price'])` |
| `times` | `string[]` | Horarios como strings "HH:MM" |
| `dates` | `object[]` | Cada objeto tiene `dow` (día semana abrev.), `day` (número), `month` (mes abrev.) |
| `synopsis` | `string[]` | Párrafos separados; `synopsis[0]` se usa en el hero de `show.blade.php` |
| `image` | `null` | Placeholder — no hay imágenes reales implementadas |

### Eventos disponibles

| ID | Título | Precio |
|----|--------|--------|
| 4 | Bodas de Sangre | $ 45.000 |
| 5 | El Rey León | $ 85.000 |
| 6 | La Casa de Bernarda Alba | $ 38.000 |

Los IDs 1, 2 y 3 existen en `featured` pero no en `upcoming`; una solicitud a `/events/1` retorna 404.

---

## tickets.json

**Ruta:** `resources/mocks/tickets.json`
**Usado en:** `/dashboard/tickets`

```json
{
  "upcoming": [
    {
      "id": 1,
      "event_title": "string",
      "category": "string",
      "date_label": "string",
      "venue": "string",
      "city": "string",
      "seat": "Fila A, Asiento 06",
      "image": null
    }
  ],
  "past": []
}
```

La sección `"past"` está vacía, lo que activa el empty state en el panel de tickets pasados.

---

## user.json

**Ruta:** `resources/mocks/user.json`
**Usado en:** Sin uso activo en vistas actuales

Contiene datos de perfil de un usuario de prueba. Previsto para la vista de perfil cuando se implemente.

---

## pqrs.json

**Ruta:** `resources/mocks/pqrs.json`
**Usado en:** Sin uso activo en vistas actuales

Contiene entradas de peticiones, quejas, reclamos y sugerencias. Previsto para un módulo de soporte/PQRS futuro.
