@extends('layouts.header')


@section('content')

    <form class="modal--custom" id="image_modal">
        <input type="file" name="image" style="display: none;" id="image_input">
        <img class="pointer rounded" style="max-height: 20vh;" id="select_image">

        <div class="mt-2">
            <label class="fw-bold">Наименование</label>
            <input type="text" class="form-control" name="name" value="" placeholder="Наименование" required>
        </div>

        <div class="row justify-content-between mx-0 mt-2">
            <button class="col-md-auto col-sm-12 btn color--primary" id="add_image" type="button">Добавить</button>
            <button class="col-md-auto col-sm-12 btn color--primary modal__close" type="button">Отмена</button>
        </div>
    </form>

    <div class="d-flex flex-column align-items-center">
        <div class="fw-bold text--big" id="album_name"></div>
        <div id="album_time"></div>
    </div>
    <div class="row mx-0 justify-content-end">
        <button class="col-auto btn color--primary" id="open_add" type="button">Добавить изображение</button>
    </div>
    <div id="images" class="row mx-0">

    </div>

@endsection

@section('scripts')
    <script src="/frontend/js/album.js" type="module"></script>
@endsection
