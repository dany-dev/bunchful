<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="catalog_create_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('catalog_create_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= SITE_URL . 'admin/catalog/create' ?>" name="create_catalog" method="post" role="form" enctype="multipart/form-data">
                    <div class="notification-container"></div>

                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="id" />
                    <div class="form-group">
                        <label for="name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('catalogs.input.name') ?></label>
                        <input type="text" id="name" class="form-control" name="name" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('catalogs.input.description') ?></label>
                        <input type="text" id="description" class="form-control" name="description" required="required" />
                    </div>
                    
                    <div class="form-group">
                        <label for="price"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('catalogs.input.price') ?></label>
                        <input type="text" id="price" class="form-control" name="price" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="image"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('catalogs.input.image') ?></label>
                        <input type="file" id="image" class="form-control" name="image[]" required="required" multiple />
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
    $('#catalog_create_modal').on('show.bs.modal', event => {
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