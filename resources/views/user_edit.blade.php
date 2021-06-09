@extends('layouts.header')

@section('content')

    <form id="edit_form">
        <input type="file" name="image" value="" style="display: none;">
        <div>
            <img id="edit_user_image" style="max-height: 20vh;">
        </div>
        <div>
            <label>Имя</label>
            <input type="text" class="form-control" name="name" value="" placeholder="Имя" required>
        </div>
        <div class="mt-2">
            <label>Изменить пароль</label>
            <input type="password" class="form-control" name="password" value="" placeholder="Пароль">
        </div>
        <div class="mt-2">
            <button class="btn color--primary" type="button" id="save_user">Сохранить</button>
        </div>
    </form>

@endsection

@section('scripts')
    <script src="/frontend/js/user_edit.js" type="module"></script>
@endsection
