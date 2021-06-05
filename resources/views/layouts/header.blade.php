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

<div id="main_container">

    <header class="row mx-0 justify-content-between p-4 shadow">
        <a href="/" class="col-auto text--primary text--middle fw-bold">Pinterest</a>

        <div class="search col">
            <input type="text" class="form-control" placeholder="Поиск" id="search_input">
        </div>

        <div class="row mx-0 col-auto align-items-center">

            <button class="btn color--primary for_guest" id="open_auth" type="button">Войти/Регистрация</button>
            <button class="btn color--primary for_auth" type="button">
                <a href="/user" class="user_name"></a>
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
