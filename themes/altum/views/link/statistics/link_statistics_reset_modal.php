<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="link_statistics_reset_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="modal-title">
                        <i class="fa fa-fw fa-sm fa-redo text-muted mr-2"></i>
                        <?= l('statistics_reset_modal.header') ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form name="link_statistics_reset_modal" method="post" action="<?= url('link/' . $data->link->link_id . '/statistics/reset') ?>" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="link_id" value="" />
                    <input type="hidden" name="start_date" value="" />
                    <input type="hidden" name="end_date" value="" />

                    <p class="text-muted"><?= l('statistics_reset_modal.subheader') ?></p>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.reset') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';

    /* On modal show load new data */
    $('#link_statistics_reset_modal').on('show.bs.modal', event => {
        let link_id = $(event.relatedTarget).data('link-id');
        let start_date = $(event.relatedTarget).data('start-date');
        let end_date = $(event.relatedTarget).data('end-date');

        $(event.currentTarget).find('input[name="link_id"]').val(link_id);
        $(event.currentTarget).find('input[name="start_date"]').val(start_date);
        $(event.currentTarget).find('input[name="end_date"]').val(end_date);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
