<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="employee_details_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= l('employee_details_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Hello World!!!
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $(() => {
        $('#employee_details_modal').on('shown.bs.modal', event => {
            let employee_id = $(event.relatedTarget).data('employee-id');
            $.ajax({
                url: `${url}company-ajax/employee_details?id=` + employee_id,
                type: "GET",
                success: function(data) {
                    data = JSON.parse(data);
                    console.log(data.details);
                    $('#employee_details_modal').find(".modal-body").empty();
                    $('#employee_details_modal').find(".modal-body").html(`
                        <div class="row mb-2">
                            <div class="col-md-6">
                                Name:
                            </div>
                            <div class="col-md-6">
                            ` + data.details.name + `
                            </div>
                        </div>  
                        <div class="row mb-2">
                            <div class="col-md-6">
                                Email:
                            </div>
                            <div class="col-md-6">
                            ` + data.details.email + `
                            </div>
                        </div>  
                        <div class="row mb-2">
                            <div class="col-md-6">
                                Total Logins:
                            </div>
                            <div class="col-md-6">
                            ` + data.details.total_logins + `
                            </div>
                        </div>    
                    `);
                },
                error: function(a, b, c) {
                    console.log(a);
                    console.log(b);
                    console.log(c);
                }

            });
        });
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>