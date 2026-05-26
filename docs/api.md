# API interna — events_users

Rutas de API consumidas por otros microservicios del sistema.

## Autenticación

Todas las rutas de esta API usan un **Bearer token estático** compartido entre servicios.

El token se configura en `.env`:

```ini
TICKETS_API_SECRET=<string-aleatorio-largo>
```

Cada request debe incluir el header:

```
Authorization: Bearer <valor-de-TICKETS_API_SECRET>
```

Si el header está ausente o el token no coincide, la respuesta es `401 Unauthorized`.

---

## Endpoints

### PATCH `/api/tickets/{code}/use`

Marca un ticket como usado. Lo llama `events_access` después de escanear el QR en la puerta del teatro.

**Parámetros de ruta**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `code` | string | Código único del ticket (`BTC-XXXXXX`) |

**Headers requeridos**

```
Authorization: Bearer {TICKETS_API_SECRET}
```

**Respuestas**

| HTTP | Body JSON | Cuándo |
|------|-----------|--------|
| `200` | `{"status": "ok", "code": "BTC-XXXXXX"}` | Ticket marcado como usado correctamente |
| `401` | `{"status": "unauthorized"}` | Token ausente o incorrecto |
| `404` | `{"status": "not_found"}` | No existe un ticket con ese código |
| `409` | `{"status": "already_used"}` | El ticket ya fue escaneado anteriormente |
| `422` | `{"status": "cancelled"}` | El ticket está cancelado y no puede usarse |

**Ejemplo de request**

```http
PATCH /api/tickets/BTC-HP6RWA/use HTTP/1.1
Host: <host-de-events_users>
Authorization: Bearer 5fb8ba678e5450f769f814748bbef...
```

---

## Implementación en events_access (ASP.NET Core)

`events_access` es el portal de validación de entradas. Al escanear el QR de un ticket, debe llamar a este endpoint. A continuación el código mínimo necesario.

### 1. Configurar el secret en `appsettings.json`

```json
{
  "TicketsApi": {
    "BaseUrl": "http://<host-de-events_users>",
    "Secret": "<mismo-valor-que-TICKETS_API_SECRET-en-events_users>"
  }
}
```

### 2. Llamar al endpoint desde el controlador de escaneo

```csharp
// ScanController.cs
using System.Net.Http.Headers;

public class ScanController : Controller
{
    private readonly IHttpClientFactory _httpClientFactory;
    private readonly IConfiguration _config;

    public ScanController(IHttpClientFactory httpClientFactory, IConfiguration config)
    {
        _httpClientFactory = httpClientFactory;
        _config = config;
    }

    public async Task<IActionResult> Scan(string code)
    {
        var client = _httpClientFactory.CreateClient();

        var baseUrl = _config["TicketsApi:BaseUrl"];
        var secret  = _config["TicketsApi:Secret"];

        client.DefaultRequestHeaders.Authorization =
            new AuthenticationHeaderValue("Bearer", secret);

        var response = await client.PatchAsync(
            $"{baseUrl}/api/tickets/{code}/use",
            content: null
        );

        return response.StatusCode switch
        {
            System.Net.HttpStatusCode.OK         => View("ScanSuccess"),
            System.Net.HttpStatusCode.Conflict   => View("AlreadyUsed"),    // 409
            System.Net.HttpStatusCode.UnprocessableEntity => View("Cancelled"), // 422
            System.Net.HttpStatusCode.NotFound   => View("InvalidTicket"),  // 404
            _                                    => View("Error"),
        };
    }
}
```

### 3. Registrar IHttpClientFactory en `Program.cs`

```csharp
builder.Services.AddHttpClient();
```

---

## Archivos relevantes en events_users

| Archivo | Responsabilidad                                     |
|---------|-----------------------------------------------------|
| `app/Http/Middleware/VerifyApiSecret.php` | Valida el Bearer token en cada request de API       |
| `app/Http/Controllers/Api/TicketController.php` | Lógica de `markAsUsed`                              |
| `routes/api.php` | Registro de la ruta `PATCH /api/tickets/{code}/use` |
| `config/services.php` | Expone `TICKETS_API_SECRET` desde `.env`            |
| `.env` | Contiene el valor real del token (fb)               |
| `.env.example` | Placeholder vacío para nuevos entornos              |
