<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_product_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('product_create_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_product" method="post" role="form">
                    <div class="notification-container"></div>

                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />

                    <div class="form-group">
                        <label for="create_name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('products.input.name') ?></label>
                        <input type="text" id="create_name" class="form-control" name="name" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="create_product"><i class="fa fa-fw fa-code fa-sm text-muted mr-1"></i> <?= l('products.input.product') ?></label>
                        <input type="text" id="create_product" name="product" class="form-control" value="" required="required" />
                        <small class="text-muted form-text"><?= l('products.input.product_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="create_link_url"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('products.input.create_link_url') ?></label>
                        <input type="text" id="create_link_url" class="form-control" name="link_url" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="create_auto_generated_link_url"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('products.input.auto_generated_link_url') ?></label>
                        <input type="text" id="create_auto_generated_link_url" class="form-control" name="auto_generated_link_url" required="required" readonly/>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.create') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $('form[name="create_product"]').on('submit', event => {
        let notification_container = event.currentTarget.querySelector('.notification-container');
        notification_container.innerHTML = '';
        pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

        $.ajax({
            type: 'POST',
            url: `${url}product-ajax`,
            data: $(event.currentTarget).serialize(),
            dataType: 'json',
            success: (data) => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                if (data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Hide modal */
                    $('#create_product_modal').modal('hide');

                    /* Clear input values */
                    $('form[name="create_product"] input').val('');

                    redirect(`products`);
                }
            },
            error: () => {
                enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
            },
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
