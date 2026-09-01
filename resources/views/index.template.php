{% extend('base/base.template.php') %}

{% section('title', 'Omega — A modern PHP MVC framework') %}

{% section('content') %}
<nav class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-gray-950/80 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="#top" class="flex items-center gap-2 text-white">
            <svg class="h-8 w-8 text-indigo-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="currentColor" fill-opacity="0.15"/>
            </svg>
            <span class="text-xl font-bold tracking-tight">Omega</span>
        </a>
        <div class="hidden items-center gap-8 text-sm text-gray-300 md:flex">
            <a href="#features" class="transition hover:text-white">Features</a>
            <a href="#architecture" class="transition hover:text-white">Architecture</a>
            <a href="#docs" class="transition hover:text-white">Documentation</a>
            <a href="#footer" class="transition hover:text-white">About</a>
        </div>
        <a href="#cta" class="rounded-full bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:opacity-90">
            Get Started
        </a>
    </div>
</nav>

<header id="top" class="relative overflow-hidden bg-gray-950 pt-32 pb-24 text-white">
    <div class="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-indigo-600/30 blur-3xl"></div>
    <div class="pointer-events-none absolute top-24 -right-24 h-96 w-96 rounded-full bg-fuchsia-600/25 blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-0 left-1/2 h-64 w-144 -translate-x-1/2 rounded-full bg-cyan-500/15 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-6 text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-xs font-medium text-indigo-300">
            Omega MVC Framework
            <span class="text-white/40">v{{ \Composer\InstalledVersions::getPrettyVersion('omega-mvc/framework') }}</span>
        </span>

        <h1 class="mx-auto mt-8 max-w-4xl text-5xl font-bold leading-tight tracking-tight sm:text-6xl lg:text-7xl">
            The PHP framework for the
            <span class="bg-gradient-to-r from-indigo-400 via-fuchsia-400 to-cyan-300 bg-clip-text text-transparent">modern web.</span>
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-300 sm:text-xl">
            Omega {{ $title }} — {{ $say }} Build fast, expressive PHP 8.4 applications with a clean template
            engine, a battle-tested router, persistent database connections and a service container done right.
        </p>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="#cta" class="rounded-xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-7 py-3.5 text-base font-semibold text-white shadow-xl shadow-indigo-500/30 transition hover:opacity-90">
                Get Started
            </a>
            <a href="#architecture" class="rounded-xl border border-white/15 bg-white/5 px-7 py-3.5 text-base font-semibold text-white backdrop-blur transition hover:border-white/30 hover:bg-white/10">
                Explore the docs
            </a>
        </div>

        <dl class="mx-auto mt-16 grid max-w-3xl grid-cols-2 gap-6 sm:grid-cols-4">
            <div class="text-center">
                <dt class="text-sm text-gray-400">Subpackages</dt>
                <dd class="text-3xl font-bold text-white">28</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-400">Test files</dt>
                <dd class="text-3xl font-bold text-white">342+</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-400">Required PHP</dt>
                <dd class="text-3xl font-bold text-white">8.4</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-400">License</dt>
                <dd class="text-3xl font-bold text-white">GPL-3.0</dd>
            </div>
        </dl>
    </div>
</header>

<section id="features" class="bg-white py-24">
    <div class="mx-auto max-w-7xl px-6">
        <h2 class="text-center text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
            Everything you need, <span class="text-indigo-600">nothing you don't.</span>
        </h2>
        <p class="mx-auto mt-4 max-w-2xl text-center text-lg text-gray-500">
            Twenty-eight focused packages, one cohesive stack. Pick the pieces you need and compose them freely.
        </p>

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="group rounded-2xl border border-gray-200 p-8 transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-500/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600/10 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 19 19 4M15 19a6 6 0 0 0 6-6H11v8zM5 10a6 6 0 0 1 6-6v6H5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Expressive routing</h3>
                <p class="mt-2 text-gray-500">Write clean, media-type aware routes with wildcard and named parameters. Group, cache and respond exactly how you expect.</p>
            </div>

            <div class="group rounded-2xl border border-gray-200 p-8 transition hover:-translate-y-1 hover:border-fuchsia-300 hover:shadow-xl hover:shadow-fuchsia-500/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-fuchsia-600/10 text-fuchsia-600 transition group-hover:bg-fuchsia-600 group-hover:text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Purpose-built template engine</h3>
                <p class="mt-2 text-gray-500">Layouts, sections, includes and custom directives compile straight to PHP. Craft pages with your own style, no magic required.</p>
            </div>

            <div class="group rounded-2xl border border-gray-200 p-8 transition hover:-translate-y-1 hover:border-cyan-300 hover:shadow-xl hover:shadow-cyan-500/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-600/10 text-cyan-600 transition group-hover:bg-cyan-600 group-hover:text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12a7 7 0 0 1 14 0M12 5v3M5 12H2m20 0h-3M12 21v-3m-5.66 0 1-1.5m11.32 1.5-1-1.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Database without friction</h3>
                <p class="mt-2 text-gray-500">Persistent PDO with automatic reconnect, a fluent query builder and migrations — CRUD that respects your time.</p>
            </div>

            <div class="group rounded-2xl border border-gray-200 p-8 transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-xl hover:shadow-amber-500/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-600/10 text-amber-600 transition group-hover:bg-amber-600 group-hover:text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 7h14M5 12h14M5 17h14M9 3l1 2m4-2-1 2M9 21l1-2m4 2-1-2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">First-class caching</h3>
                <p class="mt-2 text-gray-500">File, memory, APCu, Redis and Memcached behind one tiny interface, resolving drivers lazily the moment you need them.</p>
            </div>

            <div class="group rounded-2xl border border-gray-200 p-8 transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600/10 text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 17l8-10 8 10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Rich HTTP layer</h3>
                <p class="mt-2 text-gray-500">PSR-7 aware requests and responses, streaming, JSON helpers and a RoadRunner worker built in for persistent servers.</p>
            </div>

            <div class="group rounded-2xl border border-gray-200 p-8 transition hover:-translate-y-1 hover:border-rose-300 hover:shadow-xl hover:shadow-rose-500/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-600/10 text-rose-600 transition group-hover:bg-rose-600 group-hover:text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3s6 6 6 10a6 6 0 1 1-12 0c0-4 6-10 6-10z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Secure by design</h3>
                <p class="mt-2 text-gray-500">Environment isolation, validation, throttling, CSRF-aware helpers — sensible defaults out of the box, hardened at the edges.</p>
            </div>
        </div>
    </div>
</section>

<section id="architecture" class="bg-gray-50 py-24">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-2">
        <div>
            <h2 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                Routing done in <span class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 bg-clip-text text-transparent">ten seconds.</span>
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                Define a route, point it at a controller or a closure, and render a view. From zero to a working
                page in a single file.
            </p>
            <ul class="mt-8 space-y-4">
                <li class="flex items-start gap-3 text-gray-700">
                    <svg class="mt-1 h-5 w-5 shrink-0 text-indigo-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Named routes and reusable wildcard parameters
                </li>
                <li class="flex items-start gap-3 text-gray-700">
                    <svg class="mt-1 h-5 w-5 shrink-0 text-indigo-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Controllers, groups, prefixes and middleware pipelines
                </li>
                <li class="flex items-start gap-3 text-gray-700">
                    <svg class="mt-1 h-5 w-5 shrink-0 text-indigo-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Response helpers for HTML, JSON, redirects and streams
                </li>
            </ul>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-950 shadow-2xl">
            <div class="flex items-center gap-2 border-b border-gray-800 px-5 py-3">
                <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                <span class="ml-3 text-xs text-gray-500">routes/web.php</span>
            </div>
            {% raw %}<pre class="overflow-x-auto p-6 text-sm leading-relaxed text-gray-300"><code><span class="text-fuchsia-400">Router</span>::get('/', [<span class="text-cyan-300">IndexController::class</span>, 'handle'])
    -&gt;name('home.page');

<span class="text-fuchsia-400">Router</span>::post('/api/users', fn (<span class="text-cyan-300">$request</span>) =&gt;
    $request-&gt;json(['ok' =&gt; <span class="text-emerald-300">true</span>]));</code></pre>{% endraw %}
        </div>
    </div>
</section>

<section id="cta" class="bg-white py-24">
    <div class="mx-auto max-w-5xl px-6">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-fuchsia-600 px-10 py-16 text-center text-white shadow-2xl shadow-indigo-500/30">
            <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Ready to build something great?</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-indigo-100">
                Clone the starter app, boot the dev server and ship your first page in minutes.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="#top" class="rounded-xl bg-white px-7 py-3.5 text-base font-semibold text-indigo-700 shadow-lg transition hover:bg-indigo-50">
                    Get started now
                </a>
                <a href="#features" class="rounded-xl border border-white/30 px-7 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">
                    See the features
                </a>
            </div>
        </div>
    </div>
</section>

<footer id="footer" class="bg-gray-950 py-16 text-gray-400">
    <div class="mx-auto flex max-w-7xl flex-col items-center gap-8 px-6 sm:flex-row sm:justify-between">
        <div class="flex items-center gap-2">
            <svg class="h-7 w-7 text-indigo-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="currentColor" fill-opacity="0.15"/>
            </svg>
            <span class="text-lg font-bold text-white">Omega</span>
        </div>
        <nav class="flex flex-wrap justify-center gap-6 text-sm">
            <a href="#features" class="transition hover:text-white">Features</a>
            <a href="#architecture" class="transition hover:text-white">Architecture</a>
            <a href="https://omegamvc.github.io" class="transition hover:text-white">Documentation</a>
            <a href="https://www.gnu.org/licenses/gpl-3.0-standalone.html" class="transition hover:text-white">License</a>
        </nav>
        <p class="text-sm">
            &copy; {{ date('Y') }} Omega MVC Framework. Released under the GPL-3.0 license.
        </p>
    </div>
</footer>
{% endsection %}