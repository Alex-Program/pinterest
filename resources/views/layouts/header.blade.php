<html>
<head>
    <title>Pinterest</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="/frontend/css/bootstrap.css">
    <link rel="stylesheet" href="/frontend/css/header.css">
</head>


<body>

<div id="preloader">
    <div class='cssload-loader'>
        <div class='cssload-inner cssload-one'></div>
        <div class='cssload-inner cssload-two'></div>
        <div class='cssload-inner cssload-three'></div>
    </div>
</div>

<div id="image_edit_modal" class="modal--right" data-id="">
    <div>
        <button class="btn btn-secondary modal__close" type="button">Закрыть</button>
    </div>
    <form id="edit_image_form">
        <input type="file" name="image" id="edit_image_file" style="display: none;">
        <div class="d-flex flex-row justify-content-center">
            <img id="edit_image_preview" class="pointer rounded border--primary border--bold" style="max-height: 20vh;">
        </div>
        <div class="mt-2">
            <label class="fw-bold">Наименование</label>
            <input type="text" name="name" class="form-control" value="" placeholder="Наименование" required>
        </div>
        <div class="mt-2">
            <label class="fw-bold">Теги</label>
            <textarea class="form-control" name="tags" maxlength="1024" rows="4"></textarea>
        </div>
        <div class="mt-2">
            <label class="fw-bold">Описание</label>
            <textarea name="description" class="form-control" rows="10" maxlength="1024"></textarea>
        </div>
        <div class="mt-2">
            <button class="btn color--primary" type="button" id="save_image">Сохранить</button>
        </div>

    </form>
</div>

<div id="image_view_modal" class="modal--custom" data-id="">
    <div class="d-flex flex-column justify-content-between overflow-auto">
        <div class="d-flex flex-column overflow-auto px-3">
            <img class="w-100 rounded shadow" id="image_view">
            <div class="d-flex flex-row align-items-center justify-content-between">
                <a id="image_view_user_link" class="mt-2 d-flex mx-0 align-items-center">
                    <div class="img--rounded avatar--preview">
                        <img id="image_view_avatar" class="w-100">
                    </div>
                    <div id="image_view_user" class="px-2 fw-bold text--primary"></div>
                </a>


                <svg version="1.1" class="for_auth" id="like" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     width="437.775px" height="437.774px" viewBox="0 0 437.775 437.774"
                     style="enable-background:new 0 0 437.775 437.774;"
                     xml:space="preserve">
<g>
    <path d="M316.722,29.761c66.852,0,121.053,54.202,121.053,121.041c0,110.478-218.893,257.212-218.893,257.212S0,266.569,0,150.801
		C0,67.584,54.202,29.761,121.041,29.761c40.262,0,75.827,19.745,97.841,49.976C240.899,49.506,276.47,29.761,316.722,29.761z"/>
</g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
                    <g>
                    </g>
</svg>


            </div>
            <div id="image_view_name" class="fw-bold"></div>
            <div id="image_view_tags"></div>
            <div id="image_view_description" class="overflow-auto"></div>
        </div>

        <div>
            <div class="mt-2 row mx-0">
                <div class="col-auto px-0">
                    <button class="modal__close btn btn-secondary" type="button">Закрыть</button>
                </div>
                <div class="col-auto px-2">
                    <button class="btn color--primary for_auth" id="edit_image" type="button">Редактировать
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="px-3">
        <div class="d-flex flex-column h-100">
            <div id="comments"></div>
            <form id="comment_form" class="m-0 for_auth">
                <div class="mt-3">
                    <input type="text" name="comment" class="form-control" value="" placeholder="Добавить комментарий"
                           maxlength="128"
                           required>
                </div>
                <div class="mt-2">
                    <button class="btn color--primary" id="add_comment" type="button">Добавить</button>
                </div>
            </form>
        </div>


    </div>
</div>

<div id="main_container">

    <header class="row mx-0 justify-content-between p-4 shadow">
        <a href="/" class="col-auto text--primary text--middle fw-bold">Pinterest</a>

        <div class="search col">
            <input type="text" class="form-control" placeholder="Поиск" id="search_input">
        </div>

        <div class="row mx-0 col-auto align-items-center">

            <button class="btn color--primary for_guest" id="open_auth" type="button">Войти/Регистрация</button>
            <button class="btn color--primary for_auth d-flex flex-row" type="button">
                <a href="/user" class="d-flex flex-row align-items-center">
                    <div class="img--rounded avatar--preview">
                        <img class="user_avatar w-100" src="">
                    </div>
                    <span class="user_name px-2"></span>
                </a>
            </button>

        </div>
    </header>

    <div class="modal--custom flex-nowrap row mx-0 bg-white" id="auth_modal">

        <form id="login_form">
            <div class="row mx-0 justify-content-end">
                <button class="btn col-auto color--primary toggle" type="button" data-type="second">Регистрация 🡆
                </button>
            </div>
            <div>
                <label class="fw-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email" value="" required>
            </div>
            <div class="mt-2">
                <label class="fw-bold">Пароль</label>
                <input type="password" name="password" class="form-control" placeholder="Пароль" value="" required>
            </div>
            <div class="mt-2">
                <button class="btn color--primary" id="login" type="button">Войти</button>
            </div>
        </form>
        <form id="registration_form">
            <div class="row mx-0 justify-content-start">
                <button class="btn col-auto color--primary toggle" type="button" data-type="">🡄 Вход</button>
            </div>
            <div>
                <label class="fw-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email" value="" required>
            </div>
            <div class="mt-2">
                <label class="fw-bold">Имя</label>
                <input type="text" name="name" class="form-control" placeholder="Имя" value="" required>
            </div>
            <div class="mt-2">
                <label class="fw-bold">Пароль</label>
                <input type="password" name="password" class="form-control" placeholder="Пароль" value="" required>
            </div>
            <div class="mt-2">
                <button class="btn color--primary" id="registration" type="button">Зарегистрироваться</button>
            </div>
        </form>


    </div>

    <div class="p-5">
        @yield('content')
    </div>

</div>

<script type="module" src="/frontend/js/header.js"></script>

@yield('scripts')

</body>

</html>
