# Módulo: Eventos

## Vista de detalle de evento

**Archivo:** `resources/views/events/show.blade.php`
**Ruta:** `GET /events/{id}` → `route('events.show', $id)`
**Mock de datos:** `resources/mocks/events.json` — sección `upcoming`
**Variable de vista:** `$event` (objeto del evento resuelto por `id`)

### Resolución del evento

```php
$event = collect($events['upcoming'])->firstWhere('id', (int) $id);
abort_if(!$event, 404);
```

Si no existe un evento con ese `id` en `upcoming`, retorna HTTP 404.

---

### Secciones

#### Hero (split layout)

Distribución 50/50 entre columna de texto e imagen.

**Columna texto:**
- Breadcrumb "Volver a cartelera" → `route('catalog')`
- Badge de categoría: `$event['category']`
- Título H1: `$event['title']`
- Metadata: duración (`$event['duration']`), venue (`$event['venue']`), autor (`$event['author']`)
- Primer párrafo de sinopsis: `$event['synopsis'][0]`

**Columna imagen:**
- Placeholder con icono `curtains` y gradiente decorativo

#### Contenido + Sidebar (grid 8/4)

**Columna izquierda (8 cols):**

| Sección | Contenido |
|---------|-----------|
| Sinopsis | `@foreach ($event['synopsis'] as $paragraph)` — todos los párrafos |
| Galería de producción | Grid 2×4 con placeholders de 4 formatos: foto grande, foto ancha, dos fotos pequeñas |

**Sidebar reserva (4 cols, sticky top-28):**

| Elemento | Descripción |
|----------|-------------|
| Selector de fecha | `@foreach ($event['dates'])` — botones con `x-data="{ selectedDate: 0 }"` — estado activo con Alpine `:class` |
| Selector de horario | `@foreach ($event['times'])` — botones pill con `selectedTime` Alpine |
| Precio | `$event['price']` — texto "Desde $ X" |
| CTA comprar | Enlace → `route('events.seats', $event['id'])` |
| Aviso | "Quedan pocas entradas" |

**Estado Alpine del sidebar:**
```js
x-data="{ selectedDate: 0, selectedTime: 0 }"
```
Los índices `selectedDate` y `selectedTime` controlan el estilo activo de cada botón mediante `:class`. No tienen efecto de navegación; son estrictamente UI.

---

## Vista de selección de asientos

**Archivo:** `resources/views/events/seats.blade.php`
**Ruta:** `GET /events/{id}/seats` → `route('events.seats', $id)`
**Mock de datos:** `resources/mocks/events.json` — misma resolución que `show`
**Variable de vista:** `$event`

---

### Secciones

#### Header informativo

- Breadcrumb → `route('events.show', $event['id'])`
- Badge "Selección de Asientos"
- Título del evento, fecha (`$event['dates'][0]`), horario (`$event['times'][0]`), recinto
- Leyenda de estados: Disponible / Seleccionado / Ocupado / Bloqueado

#### Mapa de asientos (Alpine.js)

**Contenedor Alpine (`x-data`):**

El componente define su estado completo en el atributo `x-data`:

```js
{
  rows: [
    { label: 'A', section: 'Platea Central', seats: [...] },
    // B, C, D, E
  ],
  pricePerSeat: <int de $event['price']>,
  fee: 3500,
  toggleSeat(rowIdx, seatIdx) { ... },
  seatClass(seat) { ... },
  get selected() { ... },
  get subtotal() { ... },
  get total() { ... },
  fmt(n) { ... }
}
```

**Estructura del array `rows`:**

Cada fila contiene 15 elementos: 12 asientos + 3 entradas `null` que representan pasillos.

```js
{ label: 'A', section: 'Platea Central', seats: [
  {n:1, s:'a'}, {n:2, s:'a'}, {n:3, s:'o'}, {n:4, s:'o'}, null,  // pasillo
  {n:5, s:'sel'}, ..., null,  // pasillo
  {n:9, s:'o'}, ...
]}
```

**Estados de asiento (`seat.s`):**

| Valor | Significado | Clase visual | Interactivo |
|-------|------------|--------------|-------------|
| `'a'` | Disponible | `bg-primary-container hover:scale-110 cursor-pointer` | Sí |
| `'sel'` | Seleccionado | `bg-tertiary-container ring-2 ring-tertiary ring-offset-2` | Sí (deselecciona) |
| `'o'` | Ocupado | `bg-secondary-fixed-dim opacity-60 cursor-not-allowed` | No |
| `'b'` | Bloqueado | `bg-surface-dim cursor-not-allowed` | No |
| `null` | Pasillo | `w-4` (spacer transparente) | No |

**Método `seatClass(seat)`:**
Retorna la cadena de clases Tailwind correspondiente al estado del asiento. Se usa en `:class="seatClass(seat)"`. Evita la sintaxis de objeto `:class="{...}"` que requeriría escapar comillas dentro del atributo HTML.

**Método `toggleSeat(rowIdx, seatIdx)`:**
Muta `rows[rowIdx].seats[seatIdx].s` entre `'a'` y `'sel'`. Los estados `'o'` y `'b'` son ignorados. Alpine detecta la mutación a través del Proxy reactivo y re-evalúa `seatClass()` y los getters afectados.

**Getter `selected`:**
Recorre todo el array `rows` y retorna los asientos con `s === 'sel'` como objetos `{ row, num, section }`.

**Getter `subtotal`:** `selected.length * pricePerSeat`

**Getter `total`:** `subtotal + fee` (el cargo solo aplica si hay asientos seleccionados)

**`fmt(n)`:** Formatea un número como peso colombiano: `'$ ' + n.toLocaleString('es-CO')`

---

#### Sidebar de pedido (sticky top-28)

| Elemento | Contenido dinámico |
|----------|-------------------|
| Lista de asientos | `<template x-for="seat in selected">` — muestra fila, número y sección |
| Estado vacío | `<template x-if="selected.length === 0">` — texto orientativo |
| Subtotal | `x-text="fmt(subtotal)"` |
| Cargo por servicio | Visible solo si `selected.length > 0` |
| Total | `x-text="fmt(total)"` |
| CTA "Continuar" | Deshabilitado (`:disabled`, `opacity-40`) cuando `selected.length === 0` |

#### Imagen decorativa

Placeholder con icono `theater_comedy` y gradiente oscuro en la parte inferior. Equivale al "Visión desde el escenario" del diseño de referencia.

---

## Flujo de navegación

```
/catalog
  └─ [Ver info] → /events/{id}           (show.blade.php)
                      └─ [Comprar entradas] → /events/{id}/seats  (seats.blade.php)
```
