# Mount Lebanon Flowers (PHP, mock data, public pages only)

PHP 8.0+ · no database · no Composer needed (a small autoloader is in `app/bootstrap.php`).

## Run it

```bash
php -S localhost:8000 -t public
# open http://localhost:8000
```

On Apache, point the virtual host / document root at `public/` (the included `.htaccess` sends everything to `index.php`).

## Routes

| Method | Path                     | Handler                                   | Returns |
|--------|--------------------------|-------------------------------------------|---------|
| GET    | `/`                      | `PageController@home`                     | HTML |
| GET    | `/flowers`               | `FlowerPageController@index`              | HTML (`?q=&region=&edible=true|false`) |
| GET    | `/flowers/{slug}`        | `FlowerPageController@show`               | HTML (404 page if unknown) |
| GET    | `/about`                 | `PageController@about`                    | HTML |
| GET    | `/missing`               | `MissingFlowerController@show`            | HTML form |
| POST   | `/missing/whatsapp`      | `MissingFlowerController@send`            | 302 redirect to `wa.me` |
| POST   | `/api/v1/get-flowers`    | `Api\FlowersController@getFlowers`        | JSON (body: `q`, `region`, `edible`, all optional) |
| GET    | `/api/v1/flowers/{slug}` | `Api\FlowersController@show`              | JSON |
| GET    | `/api/v1/regions`        | `Api\FlowersController@regions`           | JSON |

## Where things are

```
config/config.php          WhatsApp number, social links, regions, site name, base_url
routes/web.php, api.php    all routes
app/Core/                  Router, Request, Response, View
app/Controllers/           page controllers (+ Api/)
app/Models/                Flower, Region  -> return MOCK data
app/Data/flowers.php       the mock flowers (same fields as your static model)
app/Services/WhatsApp.php  builds the wa.me link and message
resources/views/           layouts/, partials/, pages/, errors/
public/                    index.php (front controller), assets/css, assets/js, assets/img
public/assets/css/
  base.css                 tokens (colors), reset, typography, form fields, page container
  layout.css               header, nav, hamburger menu, footer
  components.css           buttons, flower cards, tags, color dots, empty state
  pages/home.css           hero, bouquet, region cards
  pages/catalog.css        search / filter bar
  pages/flower.css         gallery, facts, breadcrumb
  pages/missing.css        "can't find a flower" form box
```

### CSS loading
`base`, `layout` and `components` load on every page. A controller adds page files with
`'styles' => ['home']` in the data it passes to `View::render()`; the layout turns each name into
`css/pages/<name>.css`. A new page without its own styles (like About) just omits `styles`.

## Things to change first

1. `config/config.php`: set `whatsapp_number` (country code first, no `+`) and the real social links.
2. Real photos: put files in `public/assets/img/flowers/` and list them in a flower's `images` array in `app/Data/flowers.php` (e.g. `['cyclamen-1.jpg', 'cyclamen-2.jpg']`). Empty array = generated placeholder illustrations.
3. Using your own router: replace `app/Core/Router.php`, keep the `get()` / `post()` / `dispatch()` calls and the `Controller@method` strings.

## Moving to a real database later

Only the methods in `app/Models/Flower.php` and `Region.php` change (`all`, `findBySlug`, `search`, `first`, `recent`). Controllers, views and the API keep working as long as each flower array keeps the same keys.
