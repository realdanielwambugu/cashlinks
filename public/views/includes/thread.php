<div class="bgColor-sec w-full fx fx-j-btw color-1 mt-4 px-4">
    <div class="w-10 txt-h-c pt-4 fw-bold">
        <h2><i class="fal fa-check"></i></h2>
        <!-- <h1 class="pointer">
            <i class="fa fa-angle-up"></i>
        </h1>
        <p class="fs-lg">36</p> -->
    </div>

    <div class="w-11/12 py-2">
        <div class="w-full py-2 color-1-700">
            <!-- Thread created by
            <span class="color-pri-700">
                <?= $thread->user->username; ?>
            </span> -->

            <!-- Link shared from
            <span class="color-pri-700">
                <?= $thread->country ?>
            </span> -->
        </div>

        <div class="no-line color-1 pointer"
        onclick="count_click('<?= $thread->id ?>', '<?= $thread->link ?>')">
            <div class="fw-600 pb-2">
                <?= $thread->title; ?>
            </div>
            <!-- <div class="fs-sm py-2 color-1-700 fw-normal">
                <?= truncate($thread->body, 200); ?>
            </div> -->
        </div>

        <div class="color-1-700 pointer"
         onclick="count_click('<?= $thread->id ?>', '<?= $thread->link ?>')">
          <i class="fal fa-link pr-1"></i>
          <?= truncate($thread->link, 100);?>
        </div>

        <div class="w-11/12 fx fx-j-e">
            <div class="fx fx-i-c py-3">
                <!-- <a href="/comment/<?= $thread->id ?>"
                    class="color-1-700 no-line px-4">
                  <i class="fal fa-comments"></i>
                  <?= $thread->comment()->count() ?> comments
                </a> -->

                <div class="color-1-700 no-line px-4">
                  <i class="fal fa-hand-pointer"></i>
                  <span id="click_load_<?= $thread->id ?>">

                       <?=  shorten_num($clicks = $thread->clicks); ?>

                      <?php if ($clicks < 2): ?>
                           click
                      <?php else: ?>
                           clicks
                      <?php endif; ?>

                  </span>

              </div>

                <?php if ($thread->user->can('delete', $thread)): ?>

                    <a href="/delete_thread/<?= $thread->id ?>"
                        class="color-1-700 no-line px-4">
                      <i class="fal fa-trash"></i> delete
                    </a>

                <?php endif; ?>

            </div>
        </div>

    </div>
</div>
