<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="show_qr_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">QR</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <img class="image" src="" alt="">
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $(() => {
        $('#show_qr_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this)
            modal.find('.modal-body .image').prop('src', '<?= UPLOADS_FULL_URL . 'qr_codes/' ?>'+id+'<?= '/image.png' ?>');
        })
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>