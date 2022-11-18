<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="product_update_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('product_update_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="product_update" method="post" role="form">
                    <div class="notification-container"></div>

                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="update" />
                    <input type="hidden" name="id" value="" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="create_name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('products.input.name') ?></label>
                        <input type="text" id="create_name" class="form-control" name="name" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="create_product"><i class="fa fa-fw fa-code fa-sm text-muted mr-1"></i> <?= l('products.input.product') ?></label>
                        <input type="text" id="create_product" name="product_id" class="form-control" value="" />
                        <small class="text-muted form-text"><?= l('products.input.product_help') ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="create_type"><i class="fa fa-fw fa-adjust fa-sm text-muted mr-1"></i> <?= l('products.input.type') ?></label>
                        <select id="create_type" name="type" class="form-control">
                            <?php foreach($data->product_types as $key => $product_type): ?>
                            <option value="<?= $product_type->product_type_id ?>"><?= $product_type->name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product_link"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('products.input.product_link') ?></label>
                        <input type="text" id="product_link" class="form-control" name="product_link" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="create_auto_generated_link"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('products.input.auto_generated_link') ?></label>
                        <input type="text" id="create_auto_generated_link" class="form-control" name="auto_generated_link" required="required" readonly />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary" data-is-ajax><?= l('global.submit') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#product_update_modal').on('show.bs.modal', event => {
        let id = $(event.relatedTarget).data('id');
        let name = $(event.relatedTarget).data('name');
        let type = $(event.relatedTarget).data('type');
        let product_id = $(event.relatedTarget).data('product-id');
        let product_link = $(event.relatedTarget).data('product-link');
        let auto_generated_link = $(event.relatedTarget).data('auto-generated-link');

        $(event.currentTarget).find('input[name="id"]').val(id);
        $(event.currentTarget).find('input[name="name"]').val(name);
        $(event.currentTarget).find('[name="type"]').val(type);
        $(event.currentTarget).find(`input[name="product_id"]`).val(product_id);
        $(event.currentTarget).find('input[name="product_link"]').val(product_link);
        $(event.currentTarget).find('input[name="auto_generated_link"]').val(base_url+'p/'+auto_generated_link);
    });

    $('form[name="product_update"]').on('submit', event => {
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
                } else if (data.status == 'success') {

                    /* Hide modal */
                    $('#product_update_modal').modal('hide');

                    /* Clear input values */
                    $('form[name="product_update"] input').val('');

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