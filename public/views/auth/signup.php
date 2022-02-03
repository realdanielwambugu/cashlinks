<?php require_once view('header'); ?>
<body class="bgColor-2">

    <?php require_once view('includes/nav'); ?>

    <main class="w-5/12 m-0-auto mt-10 ff-pri">
        <form id="signup" class="w-full txt-h-c" action="signup" method="post">
            <h2 class="pb-8 color-1">Join Cashlinks</h2>
            <input class="w-7/12 px-2 py-2"
            type="text" name="username" placeholder="Choose a username">
            <br><br>
            <input class="w-7/12 px-2 py-2"
            type="email" name="email" placeholder="Your email address">
            <br><br>
            <input class="w-7/12 px-2 py-2"
            type="password" name="password" placeholder="Choose a password">
            <br>
            <p class="py-3" id="res"></p>
            <button id="btn" class="w-7/12 px-2 py-3 fw-bold bgColor-pri border-0 color-1
            hover:bgColor-pri-800 pointer"
            type="button" name="button"
            onclick="(new Ajax).form('signup').loader('btn').flush('res').send();"
            >Sign Up</button>
            <p class="py-3 color-1">Have an account?
                <a class="color-pri" href="/signin"> Sign in</a>
            </p>
        </form>
    </main>
</body>
<?php require_once view('footer'); ?>
