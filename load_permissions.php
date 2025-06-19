<?php
include 'db_config.php';

if (!isset($_POST['user_id'])) {
    die("User ID not provided.");
}
$user_id = $_POST['user_id'];

$pages = ['index.php', 'staff.php', 'order.php', 'order_view.php', 'bank_account.php', 'register.php', 'manage_rights.php', 'order_update_requests.php'];

echo "<div class='table-responsive'><table class='table table-bordered mb-0'><thead><tr><th>Page</th><th>Can View</th><th>Can Edit</th></tr></thead><tbody>";

foreach($pages as $page) {
    // Grant full permissions if user_id is 'user_00001'
    if ($user_id === 'user_00001') {
        $can_view = 1;
        $can_edit = 1;
        $can_delete = 1;
    } else {
        $query = "SELECT * FROM user_permissions WHERE user_id='$user_id' AND page_name='$page'";
        $perm_result = mysqli_query($conn, $query);
        $perm = mysqli_fetch_assoc($perm_result);

        $can_view = $perm ? $perm['can_view'] : 0;
        $can_edit = $perm ? $perm['can_edit'] : 0;
       // $can_delete = $perm ? $perm['can_delete'] : 0;
    }

    echo "<tr>";
    echo "<td>" . ucfirst($page) . "</td>";
    echo "<td>
        <div class='form-check form-switch'>
            <input class='form-check-input toggle' type='checkbox' data-user='$user_id' data-page='$page' name='view' id='view_{$page}_{$user_id}' " . ($can_view ? "checked" : "") . ($user_id === 'user_00001' ? " disabled" : "") . ">
            <label class='form-check-label' for='view_{$page}_{$user_id}'></label>
        </div>
    </td>";
    echo "<td>
        <div class='form-check form-switch'>
            <input class='form-check-input toggle' type='checkbox' data-user='$user_id' data-page='$page' name='edit' id='edit_{$page}_{$user_id}' " . ($can_edit ? "checked" : "") . ($user_id === 'user_00001' ? " disabled" : "") . ">
            <label class='form-check-label' for='edit_{$page}_{$user_id}'></label>
        </div>
    </td>";
    echo "<td>
  
    </td>";
    echo "</tr>";
}
echo "</tbody></table>";
?>


<script>
$('.toggle').on('change', function(){
    const user_id = $(this).data('user');
    const page = $(this).data('page');
    const permission = $(this).attr('name');
    const value = $(this).is(':checked') ? 1 : 0;

    $.post('update_permission.php', {
        user_id: user_id,
        page_name: page,
        permission: permission,
        value: value
    }, function(response){
        console.log(response);
    });
});
</script>
