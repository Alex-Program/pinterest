@extends('layouts.header')


@section('content')

    <form id="upload_form" style="display: none;">
        <input type="file" name="image" id="select_image">
    </form>
    <div class="d-flex flex-column align-items-center">
        <img id="user_image" src="/frontend/images/no_image.webp">
        <div class="user_name fw-bold text--big mt-2"></div>
        <div class="text--middle mt-2" id="user_email"></div>
    </div>

@endsection

@section('scripts')
    <script type="module" src="/frontend/js/user.js"></script>
@endsection
