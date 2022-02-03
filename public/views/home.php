<?php require_once view('header'); ?>
<body class="bgColor-2">

    <?php require_once view('includes/nav'); ?>

    <main class="w-6/12 m-0-auto mt-10 ff-pri pb-20">
        <?php if (!auth()->check()): ?>
            <div class="w-full h-auto bgColor-sec mb-4 pb-3">
                <div class="w-11/12 color-1 m-0-auto lh-relaxed">
                    <h2 style="color:gold;">Congratulation.</h2>
                    <h4>You found us.</h4>
                    <p>Share and discover links to online money making tips
                    and ideas.</p>
                    <div class="py-2">
                        <a class="w-7/12  px-2 py-1  no-line
                        hover:bgColor-pri-200 pointer rounded fw-bold"
                        href="/create_thread" style="background-color:gold;">
                        Let me in
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <ul class="w-full list-none p-0 fx fx-j-btw fw-600 color-1 ">
            <li class="fw-">Shared Links:</li>
            <li>
                <a class="w-7/12 bgColor-pri-700 px-2 py-1 color-1 no-line
                hover:bgColor-pri-200 pointer rounded"
                href="/create_thread">share your link</a>
            </li>
        </ul>

        <?php if (!empty($threads)):?>

            <?php foreach ($threads as $thread): ?>

                <?php require view('includes/thread'); ?>

            <?php endforeach; ?>
             <br>

                <?php if (end($threads)->id != $prev_last_id): ?>
                    <a class="no-line" href="/next_page/<?= end($threads)->id ?>/<?= rand(1000, 10000) ?>">
                        <button class="w-full px-2 py-3 fw-bold bgColor-sec border-0
                        color-1 hover:bgColor-sec-400 pointer fs-md m-0-auto">Next page</button>
                    </a>
                <?php endif; ?>

         <?php else: ?>
            <div class="w-full  txt-h-c">
                <h3 class="py-10 color-pri">No links</h3>
            </div>
        <?php endif; ?>

    </main>
</body>
<?php require_once view('footer'); ?>
