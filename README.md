<p align="center">
    <a href="https://omega-mvc.github.io" target="_blank">
        <img src="https://github.com/omega-mvc/omega-assets/blob/main/images/logo-omega.png" alt="Omega Logo">
    </a>
</p>

<p align="center">
    <a href="https://omega-mvc.github.io">Documentation</a> |
    <a href="https://github.com/omega-mvc/omega-mvc.github.io/blob/main/README.md#changelog">Changelog</a> |
    <a href="https://github.com/omega-mvc/omega/blob/main/CONTRIBUTING.md">Contributing</a> |
    <a href="https://github.com/omega-mvc/omega/blob/main/CODE_OF_CONDUCT.md">Code Of Conduct</a> |
    <a href="https://github.com/omega-mvc/omega/blob/main/LICENSE">License</a>
</p>

# Omega Starter Application
Welcome to **omega**, a minimal MVC framework designed to streamline your PHP development process. This lightweight framework offers essential features for building web applications while maintaining simplicity and ease of use.

## 🪐 Feature
- MVC structure
- Application Container (power with [php-di](https://github.com/PHP-DI/PHP-DI))
- Router Support
- Models builder
- Query builder
- CLI command
- Service Provider and Middleware
- Templator (template engine)

## Quick Start (4 Steps)

### 1 Create Your Project
```bash
composer create-project omega-mvc/omega project-name
```

### 2️ Jump In
```bash
cd project-name
```

### 3️ Build Assets
```bash
npm install
npm run build
```

### 4️ Launch!
```bash
php omega serve
```

**That's it!** Your app is now running. Let's build something awesome.


## Building Your First Feature

We'll create a user profile feature from scratch.

### Step 1: Create Database Schema
```bash
php omega make:migration profiles
php omega db:create  # Only if database doesn't exist yet
```

Define your table structure:
```php
// database/migration/<timestamp>_profiles.php
Schema::table('profiles', function (Create $column) {
    $column('user')->varChar(32);
    $column('real_name')->varChar(100);
    $column->primaryKey('user');
})
```

Run the migration:
```bash
php omega migrate
```

### Step 2: Generate Your Model
```bash
php omega make:model Profile --table-name profiles
```

### Step 3: Create a Controller
```bash
php omega make:controller Profile
```

Add your logic:
```php
// app/Controller/ProfileController.php
public function handle(MyPDO $pdo): Response
{
    return view('profile', [
        'name' => Profile::find('omega-mvc', $pdo)->real_name
    ]);
}
```

### Step 4: Design Your View
```bash
php omega make:view profile
```

```php
// resources/views/profile.template.php
{% extend('base/base.template.php') %}
{% section('title', 'Welcome {{ $name }}') %}

{% section('content') %}
    <h1>Hello, {{ $name }}! 👋</h1>
{% endsection %}
```

### Step 5: Register Your Route
```php
// route/web.php
Router::get('/profile', [ProfileController::class, 'index']);
```

**Done!** Visit `/profile` and see your work in action.


## 🔥 Pro Move: API with Attributes

Skip the route files entirely. Use attributes for clean, self-documented APIs:

```bash
php omega make:services Profile
```

```php
// app/Services/ProfileServices.php
#[Get('/api/v1/profile')]
#[Name('api.v1.profile')]
#[Middleware([AuthMiddleware::class])]
public function index(MyPDO $pdo): array
{
    $data = Cache::remember('profile', 3600, fn () => [
        'name'   => Profile::find('omega-mvc', $pdo)->real_name,
        'status' => 200,
    ]);

    return JsonResponse($data);
}
```
then register this route attribute.
```php
Router::register([
    ProfileServices::class,
    // add more class
]);
```

This automatically creates your route with middleware—no extra configuration needed!

**Equivalent traditional route:**
```php
Route::get('/api/v1/profile', [ProfileServices::class, 'index'])
    ->name('api.v1.profile')
    ->middleware([AuthMiddleware::class]);
```

## ⚡ Performance Optimization

Ready for production? Cache everything:

```bash
php omega-mvc view:cache    # Cache compiled templates
php omega-mvc config:cache  # Cache configuration
php omega-mvc route:cache   # Cache all routes
```

## Official Documentation

The official documentation for Omega is available [here](https://omega-mvc.github.io)

## Contributing

If you'd like to contribute to the Omega example application package, please follow our [contribution guidelines](CONTRIBUTING.md).

## License

This project is open-source software licensed under the [GNU General Public License v3.0](LICENSE).
