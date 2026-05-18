# Design System — Butaca

El design system de Butaca está definido en `resources/css/app.css` dentro del bloque `@theme` de Tailwind CSS 4. Cada token `--color-*` genera automáticamente utilidades `bg-*`, `text-*`, `border-*` y `ring-*`.

## Paleta de colores

### Primario (verde sage)
| Token | Clase Tailwind | Valor hex | Uso |
|-------|---------------|-----------|-----|
| `--color-primary` | `bg-primary` / `text-primary` | `#32694e` | Acciones principales, CTAs, texto destacado |
| `--color-on-primary` | `text-on-primary` | `#ffffff` | Texto sobre fondos primarios |
| `--color-primary-container` | `bg-primary-container` | `#7bb394` | Asientos disponibles, badges, chips |
| `--color-on-primary-container` | `text-on-primary-container` | `#08452e` | Texto sobre primary-container |

### Secundario (mint)
| Token | Clase Tailwind | Valor hex | Uso |
|-------|---------------|-----------|-----|
| `--color-secondary-container` | `bg-secondary-container` | `#cae6d6` | Chips de categoría, bordes suaves |
| `--color-on-secondary-container` | `text-on-secondary-container` | `#4f685b` | Texto sobre secondary-container |
| `--color-secondary-fixed-dim` | `bg-secondary-fixed-dim` | `#b1cdbd` | Asientos ocupados |

### Terciario (terra/peach)
| Token | Clase Tailwind | Valor hex | Uso |
|-------|---------------|-----------|-----|
| `--color-tertiary` | `text-tertiary` / `ring-tertiary` | `#815437` | Ring de asientos seleccionados |
| `--color-tertiary-container` | `bg-tertiary-container` | `#d39b7a` | Asientos seleccionados, iconos de pedido |
| `--color-on-tertiary-container` | `text-on-tertiary-container` | `#5a3319` | Texto sobre tertiary-container |

### Superficies
| Token | Clase Tailwind | Valor hex | Uso |
|-------|---------------|-----------|-----|
| `--color-background` | `bg-background` | `#fcf9f4` | Fondo general de la app |
| `--color-surface-container-lowest` | `bg-surface-container-lowest` | `#ffffff` | Cards principales, modales |
| `--color-surface-container-low` | `bg-surface-container-low` | `#f6f3ee` | Fondos de secciones alternas |
| `--color-surface-container` | `bg-surface-container` | `#f0ede9` | Elementos de lista, nav inactivo |
| `--color-surface-container-high` | `bg-surface-container-high` | `#ebe8e3` | — |
| `--color-surface-container-highest` | `bg-surface-container-highest` | `#e5e2dd` | Elementos de detalle en cards |
| `--color-surface-dim` | `bg-surface-dim` | `#dcdad5` | Asientos bloqueados |

### Texto
| Token | Clase Tailwind | Valor hex | Uso |
|-------|---------------|-----------|-----|
| `--color-on-surface` | `text-on-surface` | `#1c1c19` | Texto principal |
| `--color-on-surface-variant` | `text-on-surface-variant` | `#404943` | Texto secundario, metadata |
| `--color-outline` | `text-outline` | `#707973` | Labels de filas en mapa de asientos |
| `--color-outline-variant` | `border-outline-variant` | `#c0c9c1` | Separadores |

### Error
| Token | Clase Tailwind | Valor hex | Uso |
|-------|---------------|-----------|-----|
| `--color-error` | `text-error` | `#ba1a1a` | Mensajes de validación |
| `--color-error-container` | `bg-error-container` | `#ffdad6` | Fondo de bloques de error |

---

## Tipografía

Todas las fuentes se cargan desde Google Fonts en el layout (`layouts/app.blade.php`) y en las vistas standalone de auth.

| Token | Clase Tailwind | Fuente | Uso típico |
|-------|---------------|--------|-----------|
| `--font-display` | `font-display` | Bricolage Grotesque | Logo "Butaca", títulos hero |
| `--font-headline-lg` / `--font-headline-md` | `font-headline-lg` / `font-headline-md` | Bricolage Grotesque | Títulos de sección y card |
| `--font-body-lg` / `--font-body-md` | `font-body-lg` / `font-body-md` | Plus Jakarta Sans | Párrafos, descripciones |
| `--font-label-lg` / `--font-label-sm` | `font-label-lg` / `font-label-sm` | Plus Jakarta Sans | Botones, etiquetas, badges |

### Escala de tamaños
| Token | Clase Tailwind | Tamaño |
|-------|---------------|--------|
| `--text-display` | `text-display` | 48px |
| `--text-headline-lg` | `text-headline-lg` | 32px |
| `--text-headline-md` | `text-headline-md` | 24px |
| `--text-body-lg` | `text-body-lg` | 18px |
| `--text-body-md` | `text-body-md` | 16px |
| `--text-label-lg` | `text-label-lg` | 14px |
| `--text-label-sm` | `text-label-sm` | 12px |

---

## Efectos globales

### `.grain-overlay`
Textura de ruido sobre toda la página. Se aplica con un `<div class="fixed inset-0 grain-overlay z-0">` en el layout y en las vistas standalone.

### `.spotlight-glow`
Gradiente radial verde suave para resaltar áreas hero. Se usa como pseudo-fondo decorativo en la landing y secciones vacías.

### `.seat-grid`
Aplica `perspective: 1000px` al contenedor del mapa de asientos, habilitando efectos de profundidad CSS futuros.

### `.stage-curve`
`border-bottom-left-radius: 50% 20%; border-bottom-right-radius: 50% 20%` — da forma curva al elemento que representa el borde del escenario.

---

## Tokens legados (Tickify)

Mantenidos en `app.css` por compatibilidad con estilos anteriores. No deben usarse en vistas nuevas.

```
--color-sage, --color-sage-light, --color-sage-dark, --color-cream, --color-peach, --color-coral
```
