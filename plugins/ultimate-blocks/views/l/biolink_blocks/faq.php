<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="accordion" id="<?= 'accordion_' . $data->link->biolink_block_id ?>">
        <?php foreach($data->link->settings->items as $key => $item): ?>
        <div class="card">
            <div class="card-header" id="<?= 'accordion_' . $data->link->biolink_block_id . '_header_' . $key ?>">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block" type="button" data-toggle="collapse" data-target="#<?= 'accordion_' . $data->link->biolink_block_id . '_content_' . $key ?>" aria-expanded="true" aria-controls="<?= 'accordion_' . $data->link->biolink_block_id . '_content_' . $key ?>">
                        <?= $item->title ?>
                    </button>
                </h2>
            </div>

            <div id="<?= 'accordion_' . $data->link->biolink_block_id . '_content_' . $key ?>" class="collapse" aria-labelledby="<?= 'accordion_' . $data->link->biolink_block_id . '_header_' . $key ?>" data-parent="#<?= 'accordion_' . $data->link->biolink_block_id ?>">
                <div class="card-body">
                    <?= nl2br($item->content) ?>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>
