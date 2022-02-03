<?php require_once view('header'); ?>
<body class="bgColor-2">

    <?php require_once view('includes/nav'); ?>

    <main class="w-3/12 m-0-auto mt-10 ff-pri">
        <form id="create_thread" class="w-full txt-h-c" action="create_thread"
         method="post">
            <h2 class="pb-8 color-1"> Share your link</h2>

            <input class="w-full px-2 py-2" type="text" name="title"
            placeholder="Enter title">

            <!-- <br><br>

            <select id="country" name="country" class="w-full px-2 py-2">

                <?php require_once view('includes/countries'); ?>

            </select> -->

            <br><br>

            <input class="w-full px-2 py-2" type="text" name="link"
            placeholder="Enter Link: eg http://yourlink">

            <br><br>

            <input class="w-full px-2 py-2" type="number" name="clicks"
            placeholder="clicks">
            <!-- <br><br>

            <textarea class="w-full px-2 py-2" name="body" rows="8"
            placeholder="Thread body(optional)"></textarea> -->

            <br>
            <p class="py-3" id="res"></p>
            <button id="btn" class="w-full px-2 py-3 fw-bold bgColor-pri border-0 color-1
            hover:bgColor-pri-800 pointer"
            type="button" name="button"
            onclick="(new Ajax).form('create_thread').loader('btn').flush('res').send();"
            >Share</button>
        </form>
    </main>
</body>
<?php require_once view('footer'); ?>
