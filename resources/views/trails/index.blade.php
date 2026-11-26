
@extends('layouts.app', ['title' => 'Explore trails'])

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h2">Explore trails</h1>
        <p class="muted">Search, filter and explore routes. Results update without a page reload.</p>
    </div>

    @auth
        <a class="btn btn-forest" href="{{ route('trails.create') }}">+ Add trail</a>
    @else
        <a class="btn btn-forest" href="{{ route('login') }}">Log in to add a trail</a>
    @endauth
</div>

<div class="row g-2 mb-3">
    <div class="col-md-8">
        <label class="visually-hidden" for="trailSearch">Search trails</label>
        <input id="trailSearch" class="form-control form-control-lg"
               placeholder="Search by trail, location…" autocomplete="off">
    </div>

    <div class="col-md-4">
        <label class="visually-hidden" for="difficultyFilter">Filter difficulty</label>
        <select id="difficultyFilter" class="form-select form-select-lg">
            <option value="">All difficulties</option>
            <option value="Easy">Easy</option>
            <option value="Moderate">Moderate</option>
            <option value="Hard">Hard</option>
        </select>
    </div>
</div>

<p id="resultCount" class="muted small" aria-live="polite">
    {{ $trails->total() }} trails
</p>

<div id="trailResults" class="row g-3">
    @forelse($trails as $trail)
        <div class="col-sm-6 col-lg-4" data-item-card>
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <span class="badge text-bg-success align-self-start">
                        {{ $trail->difficulty }}
                    </span>

                    <h2 class="h5 mt-2">{{ $trail->name }}</h2>

                    <p class="muted">
                        {{ $trail->location }} · {{ $trail->distance_km }} km
                    </p>

                    <p>{{ \Illuminate\Support\Str::limit($trail->description, 110) }}</p>

                    <div class="d-flex flex-wrap gap-2 mt-auto">
                        <a class="btn btn-sm btn-forest"
                           href="{{ route('trails.show', $trail) }}">
                            View details
                        </a>

                        @auth
                            @if((isset(Auth::user()->role) && Auth::user()->role === 'admin') || ($trail->user_id && (int)$trail->user_id === (int)Auth::id()))
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('trails.edit', $trail) }}">
                                    Edit
                                </a>

                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-ajax-delete
                                        data-url="{{ route('trails.destroy', $trail) }}"
                                        data-confirm="Delete this trail?">
                                    Delete
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="muted">No trails have been added yet.</p>
        </div>
    @endforelse
</div>

<div id="trailPagination" class="d-flex justify-content-center gap-2 mt-4"></div>
@endsection

@push('scripts')
<script>
(() => {
    const input = document.getElementById('trailSearch');
    const filter = document.getElementById('difficultyFilter');
    const results = document.getElementById('trailResults');
    const count = document.getElementById('resultCount');
    const pages = document.getElementById('trailPagination');

    if (!input || !filter || !results || !count || !pages) {
        return;
    }

    let timer = null;
    let currentPage = 1;
    let latestRequest = 0;

    // Escape HTML to prevent unsafe content being inserted into the page.
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    const currentUserId = @json(auth()->id());

    const isAdmin = @json(
        auth()->check() &&
        isset(auth()->user()->role) &&
        auth()->user()->role === 'admin'
    );

    function render(rows) {
        results.innerHTML = rows.length
            ? rows.map(t => {
                const canManage =
                    isAdmin ||
                    (
                        currentUserId !== null &&
                        Number(t.user_id) === Number(currentUserId)
                    );

                const trailUrl = @json(url('/trails')) + '/' + Number(t.id);

                return `
                    <div class="col-sm-6 col-lg-4" data-item-card>
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">

                                <span class="badge text-bg-success align-self-start">
                                    ${esc(t.difficulty)}
                                </span>

                                <h2 class="h5 mt-2">${esc(t.name)}</h2>

                                <p class="muted">
                                    ${esc(t.location)} · ${esc(t.distance_km)} km
                                </p>

                                <p>
                                    ${esc(
                                        (t.description || '').length > 110
                                            ? t.description.slice(0, 107) + '…'
                                            : (t.description || '')
                                    )}
                                </p>

                                <div class="d-flex flex-wrap gap-2 mt-auto">
                                    <a class="btn btn-sm btn-forest"
                                       href="${trailUrl}">
                                        View details
                                    </a>

                                    ${canManage ? `
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="${trailUrl}/edit">
                                            Edit
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-ajax-delete
                                                data-url="${trailUrl}"
                                                data-confirm="Delete this trail?">
                                            Delete
                                        </button>
                                    ` : ''}
                                </div>

                            </div>
                        </div>
                    </div>
                `;
            }).join('')
            : `
                <div class="col-12">
                    <p class="muted">No matching trails found.</p>
                </div>
            `;
    }

    async function load(page = 1) {
        const requestNumber = ++latestRequest;
        currentPage = page;
        results.classList.add('loading');

        const params = new URLSearchParams({
            q: input.value.trim(),
            difficulty: filter.value,
            page: String(page)
        });

        try {
            const response = await fetch(
                @json(route('trails.index')) + '?' + params.toString(),
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Unable to load trails.');
            }

            const data = await response.json();

            // Ignore outdated AJAX responses.
            if (requestNumber !== latestRequest ||
                params.get('q') !== input.value.trim() ||
                params.get('difficulty') !== filter.value) {
                return;
            }

            render(data.data || []);

            count.textContent =
                `${data.total} trail${data.total === 1 ? '' : 's'} found`;

            pages.innerHTML = '';

            if (data.last_page > 1) {
                for (let i = 1; i <= data.last_page; i++) {
                    const button = document.createElement('button');

                    button.type = 'button';

                    button.className =
                        'btn ' +
                        (
                            i === data.current_page
                                ? 'btn-forest'
                                : 'btn-outline-secondary'
                        );

                    button.textContent = i;
                    button.setAttribute('aria-label', 'Page ' + i);

                    button.addEventListener('click', () => load(i));

                    pages.appendChild(button);
                }
            }

        } catch (error) {
            if (requestNumber !== latestRequest ||
                params.get('q') !== input.value.trim() ||
                params.get('difficulty') !== filter.value) {
                return;
            }
            console.error(error);

            results.innerHTML = `
                <div class="col-12">
                    <p class="text-danger">
                        Could not load trails. Please try again.
                    </p>
                </div>
            `;
        } finally {
            if (requestNumber === latestRequest) {
                results.classList.remove('loading');
            }
        }
    }

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => load(1), 250);
    });

    filter.addEventListener('change', () => load(1));

    load({{ $trails->currentPage() }});

})();
</script>
@endpush
