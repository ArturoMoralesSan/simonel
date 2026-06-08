@if (Session::has('alert'))
    <p class="alert alert--{{ Session::get('alert')['type'] ?? 'success' }} alert--has-icon animate-single-pop mb-8">
        {!! Session::get('alert')['message'] !!}
    </p>
@endif
