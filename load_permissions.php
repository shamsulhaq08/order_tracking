<?php
include 'db_config.php';

if (!isset($_POST['user_id'])) {
    die("User ID not provided.");
}
$user_id = $_POST['user_id'];

$page_labels = [
   
    'staff.php' => 'Staff',
    'order.php' => 'Orders',
    'order_view.php' => 'Order View',
    'order_edit.php' => 'Order Edit',
    'bank_account.php' => 'Bank Account',
    'register.php' => 'Register',
    'manage_rights.php' => 'Manage Rights',
    'order_update_requests.php' => 'Order Update Requests'
];
$pages = [ 'staff.php', 'order.php', 'order_view.php', 'order_edit.php', 'bank_account.php', 'register.php', 'manage_rights.php', 'order_update_requests.php'];

// Fetch username from database
$username = '';
$user_query = "SELECT username FROM users WHERE user_id = '$user_id' LIMIT 1";
$user_result = mysqli_query($conn, $user_query);
if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_row = mysqli_fetch_assoc($user_result);
    $username = $user_row['username'];
}

echo "<h3 class='text-center'>User Permissions for <strong>$username</strong> </h3>";


echo "<div class='table-responsive'>";
echo "<table class='table table-striped table-hover align-middle'>";
echo "<thead class='table-dark'><tr>
    <th scope='col'>Page</th>
    <th scope='col'>Page View</th>
    <th scope='col'>Page Edit</th>
    <th scope='col'>Edit Allow</th>
      </tr></thead><tbody>";

foreach($pages as $page) {
    // Grant full permissions if user_id is 'user_00001'
    if ($user_id === 'user_00001') {
    $can_view = 1;
    $can_edit = 1;
    $can_allow = 1;
    } else {
    $query = "SELECT * FROM user_permissions WHERE user_id='$user_id' AND page_name='$page'";
    $perm_result = mysqli_query($conn, $query);
    $perm = mysqli_fetch_assoc($perm_result);

    $can_view = $perm ? $perm['can_view'] : 0;
    $can_edit = $perm ? $perm['can_edit'] : 0;
    $can_allow = $perm ? (isset($perm['can_allow']) ? $perm['can_allow'] : 0) : 0;
    }

    echo "<tr>";
$label = isset($page_labels[$page]) ? $page_labels[$page] : ucfirst(str_replace('.php', '', $page));
echo "<td><strong>$label</strong></td>";

    if ($page !== 'order_edit.php') {
        echo "<td>
        <div class='form-check form-switch m-0'>
            <input class='form-check-input toggle' type='checkbox' data-user='$user_id' data-page='$page' name='view' id='view_{$page}_{$user_id}' " . ($can_view ? "checked" : "") . ($user_id === 'user_00001' ? " disabled" : "") . ">
            <label class='form-check-label visually-hidden' for='view_{$page}_{$user_id}'>Can View</label>
        </div>
        </td>";
    } else {
        echo "<td></td>";
    }
    echo "<td>
    <div class='form-check form-switch m-0'>";
    // Hide edit switch for specific pages
    $hidden_pages = ['index.php', 'staff.php', 'order.php', 'bank_account.php', 'register.php', 'manage_rights.php', 'order_edit.php'];
    if (!in_array($page, $hidden_pages)) {
        echo "<input class='form-check-input toggle' type='checkbox' data-user='$user_id' data-page='$page' name='edit' id='edit_{$page}_{$user_id}' " . ($can_edit ? "checked" : "") . ($user_id === 'user_00001' ? " disabled" : "") . ">";
        echo "<label class='form-check-label visually-hidden' for='edit_{$page}_{$user_id}'>Can Edit</label>";
    }
    echo "</div>
    </td>";
    // Show "Order Edit Fields Allow" switch only for 'order_edit.php'
    if ($page === 'order_edit.php') {
        $checked = $can_allow ? "checked" : "";
        $disabled = ($user_id === 'user_00001') ? "disabled" : "";
        echo "<td>
        <div class='form-check form-switch m-0'>
            <input class='form-check-input toggle' type='checkbox' data-user='$user_id' data-page='$page' name='allow' id='allow_{$page}_{$user_id}' $checked $disabled>
            <label class='form-check-label visually-hidden' for='allow_{$page}_{$user_id}'>Order Edit Fields Allow</label>
          
            <span style=\"font-size: 0.85em; color: #666;\">(Name, Contact, Source, Upload More Media Files)</span>
        </div>
        </td>";
    } else {
        echo "<td></td>";
    }
    echo "</tr>";
}
echo "</tbody></table></div>";
?>

<script>
$('.toggle').on('change', function(){
    const user_id = $(this).data('user');
    const page = $(this).data('page');
    const permission = $(this).attr('name');
    const value = $(this).is(':checked') ? 1 : 0;
    // Get username from PHP
    const username = <?php echo json_encode($username); ?>;

    $.post('update_permission.php', {
        user_id: user_id,
        page_name: page,
        permission: permission,
        value: value
    }, function(response){
        if (value === 1) {
            alert('Permission allowed. ' + username + ' can now ' + permission + '');
        } else {
            alert('Permission not allowed.');
        }
    });
});
</script>
