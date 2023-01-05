<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="usersListModal" tabindex="-1" role="dialog" aria-labelledby="usersListModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usersListModalLabel">Company Employees</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';
    $(() => {
        $(document).on('click', '.show-users', function() {
            $('#usersListModal').find('.modal-body .table tbody').empty();
            $('#usersListModal').find('.modal-header .modal-title').empty();
            $.ajax({
                type: 'GET',
                url: '<?= SITE_URL . 'admin/companies/get_users/' ?>' + $(this).data('target'),
                success: (data) => {
                    data = JSON.parse(data);
                    if (data.status === 'success') {
                        $('#usersListModal').find('.modal-header .modal-title').html(`<strong>` + data.details.name + `</strong>&nbsp;Employees`);
                        if (data.details.users.length > 0) {
                            data.details.users.forEach(element => {
                                $('#usersListModal').find('.modal-body .table tbody').append(`
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex flex-column">
                                                <p class="m-0">` + element.user_name + ` ` + (element.is_admin == '1' ? ` (Admin)` : ``) + ` </p>
                                                <p class="m-0 text-muted">` + element.user_email + `</p>
                                            </div>
                                            <a href="#" class="remove-employee">
                                                <i class="text-danger fas fa-times-circle" style="font-size:30px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                `);
                            });
                        } else {
                            $('#usersListModal').find('.modal-body .table tbody').append(`
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <p class="m-0" >No Record Found!</p>
                                        </div>
                                    </td>
                                </tr>
                                `);
                        }
                        $('#usersListModal').modal('show');
                    }
                },
                error: () => {
                    enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                    display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
                },
            });
        });

        $(document).on('click', '.remove-employee', function(e) {
            e.preventDefault();
        });
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>