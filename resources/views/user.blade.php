@extends('layouts.header')


@section('content')

    <form class="modal--custom" id="album_modal">

        <div>
            <label class="text--middle">Наименование</label>
            <input class="form-control" name="name" placeholder="Наименование" value="" required>
        </div>
        <div class="row justify-content-between mx-0 mt-2">
            <button class="col-md-auto col-sm-12 btn color--primary" id="add_album" type="button">Добавить</button>
            <button class="col-md-auto col-sm-12 btn btn-secondary modal__close">Отмена</button>
        </div>

    </form>

    <div class="modal--custom d-flex flex-column" id="follower_modal">
        <h3 id="followers_title"></h3>
        <div id="followers_list" class="mt-2">

        </div>
        <div class="mt-2">
            <button class="btn btn-secondary modal__close" type="button">Закрыть</button>
        </div>
    </div>

    <form id="upload_form" style="display: none;">
        <input type="file" name="image" id="select_image">
    </form>
    <div class="d-flex flex-column align-items-center">
        <img id="user_image" src="/frontend/images/no_image.webp">
        <div class="user_name fw-bold text--big mt-2"></div>
        <div class="text--middle mt-2" id="user_email"></div>
        <div class="d-flex align-items-center justify-content-centers">

            <div class="p-1">
                <div class="p-1 rounded border--primary pointer followers" data-type="followers">
                    <div id="followers_count" class="text-center fw-bold"></div>
                    <div>Подписчики</div>
                </div>
            </div>
            <div class="p-1">
                <div class="p-1 rounded border--primary pointer followers" data-type="subscribes">
                    <div id="subscribes_count" class="text-center fw-bold"></div>
                    <div>Подписки</div>
                </div>
            </div>

        </div>
        <div class="mt-2">
            <button class="btn color--primary for_other for_auth" id="follow">Подписаться</button>
            <button class="btn color--primary for_other for_auth" id="unfollow">Отписаться</button>
        </div>
        <div class="mt-2">
            <button class="btn color--primary exit for_user for_auth">Выйти</button>
        </div>
    </div>
    <div class="row justify-content-end">
        <button class="col-auto btn color--primary for_user" id="open_add" type="button">Добавить доску</button>
    </div>
    <div class="row mx-0" id="albums">

    </div>

@endsection

@section('scripts')
    <script type="module" src="/frontend/js/user.js"></script>
@endsection
