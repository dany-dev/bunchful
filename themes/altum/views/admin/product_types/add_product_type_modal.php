<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="product_type_create_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('product_type_create_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= SITE_URL . 'admin/product-types/create' ?>" name="create_product_type" method="post" role="form">
                    <div class="notification-container"></div>

                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="id" />
                    <div class="form-group">
                        <label for="create_name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('product_types.input.name') ?></label>
                        <input type="text" id="create_name" class="form-control" name="name" required="required" />
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>
<script>
    $('#product_type_create_modal').on('show.bs.modal', event => {
        console.log($(event.relatedTarget))
        if($(event.relatedTarget).data('productTypeId')) {
            let id = $(event.relatedTarget).data('productTypeId');
            let name = $(event.relatedTarget).data('resourceName');
            
            $('[name="request_type"]').val('update');
            $('input[name="id"]').val(id);
            $('input[name="name"]').val(name);
        }
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>