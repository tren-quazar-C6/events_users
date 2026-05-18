# Módulo: Cartelera (Catalog)

**Archivo:** `resources/views/catalog.blade.php`
**Ruta:** `GET /catalog` → `route('catalog')`
**Mock de datos:** `resources/mocks/events.json`
**Variable de vista:** `$events` (array completo del JSON)

---

## Secciones

### 1. Hero

Sección de presentación con buscador y imagen decorativa.

**Componentes:**
- Badge "Teatro para todos" con icono `theater_comedy`
- Título H1 con énfasis tipográfico en `text-primary italic`
- Subtítulo descriptivo
- Buscador: input de texto + botón "Buscar" — actualmente sin backend de búsqueda
- Columna derecha (oculta en móvil): imagen decorativa con border radius 32px y efecto `rotate-2 hover:rotate-0`
- Tarjeta flotante sobre la imagen con info del próximo estreno

---

### 2. Destacados (Bento Grid)

Cuadrícula tipo bento que combina tres tipos de tarjeta.

**Grid base:** `grid grid-cols-1 md:grid-cols-4`

**Tarjetas:**

| Tarjeta | Ocupa | Datos | Descripción |
|---------|-------|-------|-------------|
| Hero feature | `col-span-2 row-span-2` | `$events['featured']['hero']` | Tarjeta grande con gradiente, badge, título, descripción y CTA |
| Grid lateral | `col-span-1` × 2 | `$events['featured']['grid']` | `@foreach` sobre array de 2 ítems con categoría, título y metadata |
| Promo familiar | `col-span-2` | Estático | "Descuento Familiar" — 4 entradas al precio de 3 |

**Acceso a datos:**
```php
$events['featured']['hero']['title']      // título del evento hero
$events['featured']['hero']['badge']      // texto del badge
$events['featured']['grid'][n]['title']   // título de tarjeta lateral
$events['featured']['grid'][n]['category']
$events['featured']['grid'][n]['meta']    // ej: "12 - 24 Jun"
$events['featured']['grid'][n]['meta_icon'] // nombre del Material Symbol
```

---

### 3. Próximas Funciones

Lista de eventos próximos con navegación al detalle.

**Iteración:** `@foreach ($events['upcoming'] as $event)`

**Columnas por tarjeta de evento:**

| Columna | Datos | Descripción |
|---------|-------|-------------|
| Fecha | `$event['day']`, `$event['month']` | Cuadro con día grande y mes en minúsculas |
| Título y venue | `$event['title']`, `$event['subtitle']` | Nombre de la obra y recinto |
| Horarios | `$event['times']` | `@foreach` de chips con cada horario |
| CTAs | `$event['id']` | "Ver info" → `route('events.show', $event['id'])` / "Comprar Tickets" (sin acción definida desde catalog) |

---

### 4. Newsletter

Formulario de suscripción por email. Actualmente estático (sin backend). Contiene input `type="email"` y botón submit dentro de `<form>`.

---

## Interacciones

| Acción | Destino |
|--------|---------|
| "Ver info" en tarjeta de evento | `GET /events/{id}` → detalle del evento |
| "Comprar Tickets" en tarjeta | Sin ruta asignada (acción pendiente de implementar desde catalog) |
| Buscador | Sin backend (solo UI) |
| Newsletter | Sin backend (solo UI) |
