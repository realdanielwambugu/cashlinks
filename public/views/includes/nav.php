<nav class="w-10/12 bgColor-sec m-0-auto fx fx-j-btw ff-pri mt-8 py-2 px-8">
    <ul class="w-4/12 list-none fx fx-i-c fx-j-btw p-0">
        <li>
            <a class="no-line color-pri "
            href="/">
                <h3 class="fw-black"> <?= app_name(); ?></h3>
            </a>
        </li>
        <li>|</li>
        <li>
            <a class="no-line color-1 hover:color-pri"
            href="/">Links</a>
        </li>
        <li>|</li>
        <li>
            <a class="no-line color-1 hover:color-pri"
            href="/privacy">Privacy</a>
        </li>

        <li>|</li>
        <li>
            <a class="no-line color-1 hover:color-pri"
            href="/terms">Terms</a>
        </li>
    </ul>

    <ul class="w-3/12 list-none fx fx-i-c fx-j-btw p-0">

        <?php if (!auth()->check()): ?>

            <li>
                <a class="no-line color-1 hover:color-pri"
                href="/signup">Sign Up</a>
            </li>
            <li>|</li>
            <li>
                <a class="no-line color-1 hover:color-pri"
                href="/signin">Sign In</a>
            </li>

        <?php else: ?>

            <li>
                <a class="no-line color-1 hover:color-pri fx fx-i-c"
                 href="/" class="fx fx-i-c">
                    <!-- <div class="w-8 h-8 rounded-full">
                        <img class="w-full h-full cover rounded-full"
                        src="<?= images_path('user/'. auth()->user()->photo) ?>"
                        alt="user pic">
                    </div> -->
                    <span class="px-2">
                        username(<?=truncate(auth()->user()->username, 12) ?>)
                    </span>
                </a>
            </li>
            <li>|</li>
            <li>
                <a class="no-line color-1 hover:color-pri"
                 href="/logout">Sign Out</a>
            </li>

        <?php endif; ?>
    </ul>
</nav>
