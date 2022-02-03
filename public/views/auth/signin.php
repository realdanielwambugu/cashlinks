<?php require_once view('header'); ?>
<body class="bgColor-2">

    <?php require_once view('includes/nav'); ?>

    <main class="w-5/12 m-0-auto mt-10 ff-pri">

        <form id="signin" class="w-full txt-h-c" action="signin" method="post">
            <h2 class="pb-8 color-1">Hi! Welcome back.</h2>
            <input class="w-7/12 px-2 py-2"
            type="username" name="username" placeholder="Your account username">
            <br><br>
            <input class="w-7/12 px-2 py-2"
            type="password" name="password" placeholder="Your account password">
            <br>
            <p class="py-3" id="res"></p>
            <button id="btn" class="w-7/12 px-2 py-3 fw-bold bgColor-pri border-0 color-1
            hover:bgColor-pri-800 pointer"
            type="button" name="button"
            onclick="(new Ajax).form('signin').loader('btn').flush('res').send();"
            >Sign in</button>
            <p class="py-3 color-1">Not a member?
                <a class="color-pri" href="/signup"> Sign up</a>
            </p>
        </form>
    </main>
</body>
<?php require_once view('footer'); ?>
