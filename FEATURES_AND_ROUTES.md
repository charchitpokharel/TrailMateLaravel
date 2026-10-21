# TrailMate feature map

| Feature | Route | Behaviour |
|---|---|---|
| Home | `GET /` | Recent trails |
| Trail list/search/filter | `GET /trails?q=&difficulty=&page=` | HTML normally; JSON for AJAX requests |
| Create trail | `GET /trails/create`, `POST /trails` | MVC form; AJAX JSON submission supported |
| Trail detail | `GET /trails/{trail}` | Detail page, favourite toggle, delete |
| Edit/update | `GET /trails/{trail}/edit`, `PUT /trails/{trail}` | AJAX form submission supported |
| Delete | `DELETE /trails/{trail}` | AJAX JSON response supported |
| Search endpoint | `GET /api/trails/search?q=` | JSON search results |
| Favourites | `GET /favourites`, `POST /api/favourites/{trail}` | Session favourites; toggle returns JSON |
| Reviews | `GET /reviews/{trail}`, `POST /api/reviews/{trail}` | JSON list and AJAX submission |
| Weather | `GET /weather`, `GET /api/weather?lat=&lon=` | Forecast JSON from Open-Meteo |

The `/api/...` endpoints in this overlay are defined in `routes/web.php`, so they
use Laravel's web session/CSRF middleware. This is intentional for browser AJAX.
If you later need a stateless mobile-app API, create separate routes in `routes/api.php`
and protect them with appropriate API authentication.
