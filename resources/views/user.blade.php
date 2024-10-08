@extends('layouts.header')


@section('content')

    <div id="liked_modal" class="d-flex flex-column modal--right__full">
        <div>
            <button class="btn btn-secondary modal__close" type="button">Закрыть</button>
        </div>
        <h3 class="mt-2">Избранное</h3>

        <div id="liked_list" class="overflow-auto row mx-0 p-4">
        </div>
    </div>

    <div id="edit_album_modal" class="modal--right" data-id="">

        <div>
            <button class="btn btn-secondary modal__close" type="button">Закрыть</button>
        </div>

        <form id="edit_album_form">
            <input type="file" name="image" id="edit_album_file" style="display: none;">
            <div class="d-flex flex-row justify-content-center">
                <img id="edit_album_preview" class="pointer rounded border--primary border--bold"
                     style="max-height: 20vh;">
            </div>
            <div class="mt-2">
                <label class="fw-bold">Наименование</label>
                <input type="text" name="name" class="form-control" value="" placeholder="Наименование" maxlength="256"
                       required>
            </div>
            <div class="mt-2">
                <button class="btn color--primary" id="save_album" type="button">Сохранить</button>
            </div>


        </form>

    </div>

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
        <div class="mt-2 d-flex">
            <div class="for_user for_auth px-1">
                <button class="btn color--primary"><a href="/user/edit">Редактировать</a></button>
            </div>
            <div class="for_user for_auth px-1">
                <button class="btn color--primary exit">Выйти</button>
            </div>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="for_user col-auto">
            <button class="btn color--primary" type="button" id="to_liked">Избранное</button>
        </div>
        <div class="col-auto for_user px-2">
            <button class="btn color--primary" id="open_add" type="button">Добавить доску</button>
        </div>
    </div>
    <div class="row mx-0" id="albums">

    </div>

@endsection

@section('scripts')
    <script type="module" src="/frontend/js/user.js"></script>
@endsection
