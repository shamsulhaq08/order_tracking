<?php
session_start();
require 'db_config.php';

// Check login session
if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    die("User not logged in.");
}

$user_id = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    die("Invalid order ID.");
}

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();
$stmt->close();

$staff = [];
$res = $conn->query("SELECT id, first_name FROM staff");
if (!$res) {
    die("Query failed: " . $conn->error);
}
while ($row = $res->fetch_assoc()) {
    $staff[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

    <head>
        <meta charset="utf-8" />
        <title>Edit Order</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- third party css -->
        <link href="assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-select-bs5/css//select.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <!-- third party css end -->

		<!-- App css -->
		<link href="assets/css/style.min.css" rel="stylesheet" type="text/css">
		<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
		<script src="assets/js/config.js"></script>
    </head>

    
        <!-- Begin page -->
        <div class="layout-wrapper">

            <!-- ========== Left Sidebar ========== -->
            <div class="main-menu">
                <!-- Brand Logo -->
                 <div class="logo-box">
                <!-- Brand Logo Light -->
                  <a href="index.php" class="logo-light">
                    <h4 style="color: white;">Tracking</h4>
                </a>

                
            </div>
            <?php include 'sidebar.php'; ?>
            </div>


      <div class="page-content">

                <?php include 'topbar.php'; ?>

                <div class="px-3">

                    <!-- Start Content-->
                    <div class="container-fluid">
                        <br>
                        
               <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Edit Order</h4>
                                    <p class="sub-header">
                                        Edit the order details below.
                                    </p>
                             
                                    <form action="update_order.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="date">Date:</label>
                                                    <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($order['order_date']) ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="delivery_date">Delivery Date:</label>
                                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?= htmlspecialchars($order['delivery_date']) ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="time">Delivery Time:</label>
                                                    <input type="time" name="time" id="time" class="form-control" value="<?= htmlspecialchars($order['order_time']) ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="name">Name:</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($order['customer_name']) ?>">
                                                </div>
                                            </div>
                                       

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="contact">Contact:</label>
                                                    <input type="text" name="contact" id="contact" class="form-control" value="<?= htmlspecialchars($order['contact']) ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Is WhatsApp number same as Contact?</label><br>
                                                    <label>
                                                        <input type="radio" name="is_whatsapp_same" value="yes" <?= ($order['whatsapp_number'] == $order['contact']) ? 'checked' : '' ?>> Yes
                                                    </label>
                                                    <label style="margin-left:15px;">
                                                        <input type="radio" name="is_whatsapp_same" value="no" <?= ($order['whatsapp_number'] != $order['contact']) ? 'checked' : '' ?>> No
                                                    </label>
                                                </div>
                                            </div>
                                           
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="whatsapp_number">Whatsapp number:</label>
                <div class="input-group">
                    <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control" value="<?= htmlspecialchars($order['whatsapp_number']) ?>">
                    <button type="button" class="btn btn-outline-secondary" id="check_whatsapp_btn" title="Check WhatsApp"><i class="mdi mdi-whatsapp"></i></button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="customer_address">Customer Address:</label>
                <input type="text" name="customer_address" id="customer_address" class="form-control" value="<?= htmlspecialchars($order['customer_address']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="order_maker_id">Order Maker:</label>
                <select name="order_maker_id" id="order_maker_id" class="form-control">
                    <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $order['order_maker_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['first_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-12" id="reason_group" style="<?= isset($order['reason']) && $order['reason'] !== '' ? '' : 'display:none;' ?>">
            <div class="form-group" style="grid-column: span 3;">
                <label for="reason">Reason for changing Order Maker:</label>
                <input type="text" name="reason" id="reason" class="form-control" value="<?= htmlspecialchars($order['reason']) ?>">
                <span id="reason_required" style="color:red; display:none;">* Reason is required</span>
                <input type="hidden" name="reason_hidden" id="reason_hidden" value="<?= htmlspecialchars($order['reason']) ?>">
            </div>
        </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
        var orderMakerSelect = document.getElementById('order_maker_id');
        var reasonGroup = document.getElementById('reason_group');
        var reasonInput = document.getElementById('reason');
        var reasonRequired = document.getElementById('reason_required');
        var originalValue = orderMakerSelect.getAttribute('data-original') || '<?= $order['order_maker_id'] ?>';

    orderMakerSelect.addEventListener('change', function() {
    if (this.value !== originalValue) {
        reasonGroup.style.display = '';
        reasonRequired.style.display = '';
    } else {
        reasonGroup.style.display = 'none';
        reasonInput.value = '';
        reasonRequired.style.display = 'none';
        document.getElementById('reason_hidden').value = ''; // clear hidden too
    }
});

        // On form submit, require reason if shown
        document.querySelector('form').addEventListener('submit', function(e) {
            if (reasonGroup.style.display !== 'none' && reasonInput.value.trim() === '') {
            alert('Please provide a reason for changing the Order Maker.');
            reasonInput.focus();
            e.preventDefault();
            return false;
            }
        });
        });
        </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var contactInput = document.getElementById('contact');
        var whatsappInput = document.getElementById('whatsapp_number');
        var radios = document.getElementsByName('is_whatsapp_same');

        function updateWhatsappField() {
            if (radios[0].checked) { // Yes
                whatsappInput.value = contactInput.value;
                whatsappInput.readOnly = true;
            } else {
                whatsappInput.readOnly = false;
                // Only clear if not already different
                if (whatsappInput.value === contactInput.value) {
                    whatsappInput.value = '';
                }
            }
        }

        radios.forEach(function(radio) {
            radio.addEventListener('change', updateWhatsappField);
        });

        contactInput.addEventListener('input', function() {
            if (radios[0].checked) {
                whatsappInput.value = contactInput.value;
            }
        });

        // Initial state
        updateWhatsappField();

        // WhatsApp registration check (opens WhatsApp if installed)
        var checkBtn = document.getElementById('check_whatsapp_btn');
        if (checkBtn) {
            checkBtn.addEventListener('click', function(e) {
                var number = whatsappInput.value.trim();
                if (!number) {
                    alert('Please enter a WhatsApp number.');
                    whatsappInput.focus();
                    return;
                }
                // Format number for wa.me (remove spaces, dashes, etc.)
                var formatted = number.replace(/[^\d]/g, '');
                // Open WhatsApp chat (will only work if WhatsApp is installed)
                window.open('https://wa.me/' + formatted, '_blank');
            });
        }
    });
    </script>
        <br>    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Source:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="source" value="Whatsapp" <?= $order['order_source'] == 'Whatsapp' ? 'checked' : '' ?>> Whatsapp
                    </label>
                    <label>
                        <input type="radio" name="source" value="Instagram" <?= $order['order_source'] == 'Instagram' ? 'checked' : '' ?>> Instagram
                    </label>
                    <label>
                        <input type="radio" name="source" value="Facebook" <?= $order['order_source'] == 'Facebook' ? 'checked' : '' ?>> Facebook
                    </label>
                    <label>
                        <input type="radio" name="source" value="Physical" <?= $order['order_source'] == 'Physical' ? 'checked' : '' ?>> Physical
                    </label>
                    <label>
                        <input type="radio" name="source" value="Other" id="source_other_radio" <?= $order['order_source'] == 'Other' ? 'checked' : '' ?>> Other
                    </label>
                    <input 
                        type="text" 
                        name="source_other_text" 
                        id="source_other_text" 
                        placeholder="Please specify" 
                        style="margin-left:10px; min-width:180px;<?= $order['order_source'] == 'Other' ? '' : 'display:none;' ?>"
                        value="<?= htmlspecialchars($order['source_other_text']) ?>"
                    >
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <!-- Quill editor container -->
                <div id="quill_description"><?= $order['description'] ?></div>
                <!-- Hidden textarea to submit HTML content -->
                <textarea name="description" id="description" class="form-control" style="display:none;"></textarea>
          
            </div>
        </div>
        <br> <br>
        <div class="col-md-6">
            <div class="form-group">
                  <br>
                <label for="file_media">Upload More Media Files:</label>
                <input type="file" name="file_media[]" id="file_media" class="form-control" multiple>
            </div>
              <div class="form-group">
                <label>Current Files:</label>
                <div>
                    <?php
                    // Assuming $order['file_media'] is a comma-separated list of file names/paths
                    if (!empty($order['file_media'])) {
                        $files = explode(',', $order['file_media']);
                        foreach ($files as $file) {
                            $file = trim($file);
                            if ($file) {
                                $fileUrl = 'uploads/' . $file; // Adjust path if needed
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    echo '<img src="' . htmlspecialchars($fileUrl) . '" alt="" class="popup-img" style="max-width:80px;max-height:80px;margin:5px;border:1px solid #ccc;cursor:pointer;" data-img="' . htmlspecialchars($fileUrl) . '">';
                                } else {
                                    echo '<a href="' . htmlspecialchars($fileUrl) . '" target="_blank">' . htmlspecialchars($file) . '</a><br>';
                                }
                            }
                        }
                    } else {
                        echo '<span class="text-muted">No files uploaded.</span>';
                    }
                    ?>
                </div>
            </div>
            <!-- Popup Modal for Image Preview -->
            <div id="imgModal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;">
                <span id="closeImgModal" style="position:absolute;top:30px;right:40px;font-size:40px;color:#fff;cursor:pointer;">&times;</span>
                <img id="imgModalContent" src="" style="max-width:90vw;max-height:90vh;display:block;margin:auto;box-shadow:0 0 20px #000;">
            </div>
        </div>
    </div>
    <br>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        // Show/hide the "Other" text input based on radio selection
        function toggleSourceOther() {
            var otherRadio = document.getElementById('source_other_radio');
            var otherText = document.getElementById('source_other_text');
            if (otherRadio.checked) {
                otherText.style.display = '';
            } else {
                otherText.style.display = 'none';
            }
        }
        document.querySelectorAll('input[name="source"]').forEach(function(radio) {
            radio.addEventListener('change', toggleSourceOther);
        });
        // Initial toggle on page load
        toggleSourceOther();

        // Quill editor
        var quill = new Quill('#quill_description', {
            theme: 'snow'
        });
        // On form submit, copy HTML to textarea
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('description').value = quill.root.innerHTML;
        });

        // Image popup
        document.querySelectorAll('.popup-img').forEach(function(img) {
            img.addEventListener('click', function() {
                document.getElementById('imgModalContent').src = this.getAttribute('data-img');
                document.getElementById('imgModal').style.display = 'flex';
            });
        });
        document.getElementById('closeImgModal').onclick = function() {
            document.getElementById('imgModal').style.display = 'none';
            document.getElementById('imgModalContent').src = '';
        };
        document.getElementById('imgModal').onclick = function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                document.getElementById('imgModalContent').src = '';
            }
        };
    </script>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <br>
                <label for="total"><strong>Total:</strong></label>
                <input type="number" step="0.01" name="total" id="total" class="form-control" value="<?= $order['total'] ?>" style="font-weight:bold;">
            </div>
            <div class="form-group">
                <label for="advance">Advance:</label>
                <input type="number" step="0.01" name="advance" id="advance" class="form-control" value="<?= $order['advance'] ?>" readonly>
            </div>
            <div class="form-group">
                <label for="remaining">Remaining:</label>
                <input type="number" step="0.01" name="remaining" id="remaining" class="form-control" value="<?= $order['remaining'] ?>" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Payment Method:</label>
                <div class="checkbox-group" id="payment-method-group">
                    <?php
                    $selected_payments = explode(', ', $order['payment']);
                    ?>
                    <label><input type="checkbox" name="payment[]" value="Cash" id="payment_cash" <?= in_array('Cash', $selected_payments) ? 'checked' : '' ?>> Cash</label>
                    <label><input type="checkbox" name="payment[]" value="Bank" id="payment_bank" <?= in_array('Bank', $selected_payments) ? 'checked' : '' ?>> Bank</label>
                    <span id="more-payment-methods" style="display:none;">
                        <label><input type="checkbox" name="payment[]" value="Online" id="payment_online" <?= in_array('Online', $selected_payments) ? 'checked' : '' ?>> Online</label>
                        <label><input type="checkbox" name="payment[]" value="Card" id="payment_card" <?= in_array('Card', $selected_payments) ? 'checked' : '' ?>> Card</label>
                    </span>
                </div>
            </div>
            <div class="form-group" id="cash_payment_group" style="display:none;">
                <label for="cash_payment">Cash Payment:</label>
                <input type="number" step="0.01" name="cash_payment" id="cash_payment" class="form-control" value="<?= $order['cash_payment'] ?>" placeholder="Enter cash payment">
            </div>
            <div class="form-group" id="bank_detail_group" style="display:none;">
                <label for="bank_detail">Bank Detail:</label>
                <input type="text" name="bank_detail" id="bank_detail" class="form-control" value="<?= htmlspecialchars($order['bank_detail']) ?>" placeholder="Enter bank details">
            </div>
            <div class="form-group" id="card_detail_group" style="display:none;">
                <label for="card_detail">Card Detail:</label>
                <input type="text" name="card_detail" id="card_detail" class="form-control" value="<?= htmlspecialchars($order['card_detail']) ?>" placeholder="Enter card details">
            </div>
            <div class="form-group" id="ac_detail_group" style="display:none;">
                <label for="ac_detail">A/C Detail:</label>
                <input type="text" name="ac_detail" id="ac_detail" class="form-control" value="<?= htmlspecialchars($order['ac_detail']) ?>" placeholder="Enter A/C details">
            </div>
            <div class="form-group" id="transaction_id_group" style="display:none;">
                <label for="transaction_id">Transaction ID:</label>
                <input type="text" name="transaction_id" id="transaction_id" class="form-control" value="<?= htmlspecialchars($order['transaction_id']) ?>" placeholder="Enter transaction ID">
            </div>
            <div class="form-group" id="online_amount_group" style="display:none;">
                <label for="online_amount">Online Amount:</label>
                <input type="number" step="0.01" name="online_amount" id="online_amount" class="form-control" value="<?= $order['online_amount'] ?>" placeholder="Enter online amount">
            </div>
            <div class="form-group" id="card_amount_group" style="display:none;">
                <label for="card_amount">Card Amount:</label>
                <input type="number" step="0.01" name="card_amount" id="card_amount" class="form-control" value="<?= $order['card_amount'] ?>" placeholder="Enter card amount">
            </div>
        </div>
    </div>

                                   
                                       
                                  
                                        <div class="form-group">
                                              <br>
                                            <input type="submit" value="Update Order" class="btn btn-primary">
                                        </div>
                                    </form>
    <script>
        // Show/hide payment-related fields based on checked payment methods
        function togglePaymentFields() {
            document.getElementById('cash_payment_group').style.display = document.getElementById('payment_cash').checked ? '' : 'none';
            document.getElementById('bank_detail_group').style.display = document.getElementById('payment_bank').checked ? '' : 'none';
            document.getElementById('card_detail_group').style.display = document.getElementById('payment_card').checked ? '' : 'none';
            document.getElementById('ac_detail_group').style.display = document.getElementById('payment_bank').checked ? '' : 'none';
            document.getElementById('transaction_id_group').style.display = document.getElementById('payment_online').checked ? '' : 'none';
            document.getElementById('online_amount_group').style.display = document.getElementById('payment_online').checked ? '' : 'none';
            document.getElementById('card_amount_group').style.display = document.getElementById('payment_card').checked ? '' : 'none';
        }
        document.querySelectorAll('#payment-method-group input[type=checkbox]').forEach(function(cb) {
            cb.addEventListener('change', togglePaymentFields);
        });
        // Show more payment methods if needed (customize as per your logic)
        document.getElementById('payment_bank').addEventListener('change', function() {
            document.getElementById('more-payment-methods').style.display = this.checked ? '' : 'none';
        });
        // Initial toggle on page load
        togglePaymentFields();
        if(document.getElementById('payment_bank').checked) {
            document.getElementById('more-payment-methods').style.display = '';
        }

        // Calculate advance and remaining based on all payment fields and check/uncheck
        function updateAdvanceAndRemaining() {
            var total = parseFloat(document.getElementById('total').value) || 0;
            var cashChecked = document.getElementById('payment_cash').checked;
            var bankChecked = document.getElementById('payment_bank').checked;
            var onlineChecked = document.getElementById('payment_online') && document.getElementById('payment_online').checked;
            var cardChecked = document.getElementById('payment_card') && document.getElementById('payment_card').checked;

            var cashVal = cashChecked ? (parseFloat(document.getElementById('cash_payment').value) || 0) : 0;
            var onlineVal = (bankChecked && onlineChecked) ? (parseFloat(document.getElementById('online_amount').value) || 0) : 0;
            var cardVal = (bankChecked && cardChecked) ? (parseFloat(document.getElementById('card_amount').value) || 0) : 0;

            var advance = cashVal + onlineVal + cardVal;
            document.getElementById('advance').value = advance;

            var remaining = total - advance;
            document.getElementById('remaining').value = remaining >= 0 ? remaining.toFixed(2) : '0.00';

            // Overpaid check
            if (advance > total && total > 0) {
                alert(`You can only pay up to ${total}.`);
                if (document.getElementById('cash_payment') === document.activeElement) {
                    document.getElementById('cash_payment').value = '';
                    document.getElementById('cash_payment').dispatchEvent(new Event('input'));
                }
                if (document.getElementById('online_amount') === document.activeElement) {
                    document.getElementById('online_amount').value = '';
                    document.getElementById('online_amount').dispatchEvent(new Event('input'));
                }
                if (document.getElementById('card_amount') === document.activeElement) {
                    document.getElementById('card_amount').value = '';
                    document.getElementById('card_amount').dispatchEvent(new Event('input'));
                }
                if (document.querySelector('input[type=submit],button[type=submit]')) {
                    document.querySelector('input[type=submit],button[type=submit]').disabled = true;
                }
                return;
            } else {
                if (document.querySelector('input[type=submit],button[type=submit]')) {
                    document.querySelector('input[type=submit],button[type=submit]').disabled = false;
                }
            }
        }

        document.getElementById('total').addEventListener('input', updateAdvanceAndRemaining);
        document.getElementById('cash_payment').addEventListener('input', updateAdvanceAndRemaining);
        document.getElementById('online_amount').addEventListener('input', updateAdvanceAndRemaining);
        document.getElementById('card_amount').addEventListener('input', updateAdvanceAndRemaining);

        ['payment_cash', 'payment_bank', 'payment_online', 'payment_card'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('change', function() {
                togglePaymentFields();
                updateAdvanceAndRemaining();
            });
        });

        // Initial calculation
        updateAdvanceAndRemaining();

        // Validation on submit
        document.querySelector('form').addEventListener('submit', function(e) {
            var cash = document.getElementById('payment_cash');
            var bank = document.getElementById('payment_bank');
            var online = document.getElementById('payment_online');
            var card = document.getElementById('payment_card');
            var totalInput = document.getElementById('total');
            var cashVal = document.getElementById('cash_payment').value.trim();
            var onlineVal = document.getElementById('online_amount').value.trim();
            var cardVal = document.getElementById('card_amount').value.trim();

            // At least one payment method
            if (!cash.checked && !bank.checked) {
                alert('Please select at least one payment method: Cash or Bank.');
                cash.focus();
                e.preventDefault();
                return false;
            }

            // If any payment field is filled but total order amount is empty, prevent submit
            var totalVal = totalInput.value.trim();
            var anyPaymentFilled = false;
            if (
                (cash.checked && cashVal) ||
                (bank.checked && online && online.checked && onlineVal) ||
                (bank.checked && card && card.checked && cardVal)
            ) {
                anyPaymentFilled = true;
            }
            if (anyPaymentFilled && !totalVal) {
                alert('Please enter Total Order Amount before filling payment details.');
                totalInput.focus();
                e.preventDefault();
                return false;
            }

            // Overpaid check on submit
            var total = parseFloat(totalInput.value) || 0;
            var paid = 0;
            if (cash.checked) paid += parseFloat(cashVal) || 0;
            if (bank.checked && online && online.checked) paid += parseFloat(onlineVal) || 0;
            if (bank.checked && card && card.checked) paid += parseFloat(cardVal) || 0;
            if (paid > total && total > 0) {
                alert(`You can only pay up to ${total}.`);
                e.preventDefault();
                return false;
            }

            if (bank.checked) {
                if (!(online && online.checked) && !(card && card.checked)) {
                    alert('If Bank is selected, at least one of Online or Card must also be selected.');
                    bank.focus();
                    e.preventDefault();
                    return false;
                }
                // Bank + Online
                if (online && online.checked) {
                    if (!document.getElementById('bank_detail').value.trim()) {
                        alert('Bank Detail is required.');
                        document.getElementById('bank_detail').focus();
                        e.preventDefault();
                        return false;
                    }
                    if (!document.getElementById('online_amount').value.trim()) {
                        alert('Online Amount is required.');
                        document.getElementById('online_amount').focus();
                        e.preventDefault();
                        return false;
                    }
                }
                // Bank + Card
                if (card && card.checked) {
                    if (!document.getElementById('card_detail').value.trim()) {
                        alert('Card Detail is required.');
                        document.getElementById('card_detail').focus();
                        e.preventDefault();
                        return false;
                    }
                    if (!document.getElementById('card_amount').value.trim()) {
                        alert('Card Amount is required.');
                        document.getElementById('card_amount').focus();
                        e.preventDefault();
                        return false;
                    }
                }
            }
            // If Cash is checked, require cash_payment
            if (cash.checked) {
                if (!document.getElementById('cash_payment').value.trim()) {
                    alert('Cash Payment is required.');
                    document.getElementById('cash_payment').focus();
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    </div>    <!-- end page content -->
    </div> <!-- end layout-wrapper -->
    <!-- Vendor js -->        <!-- App js -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.js"></script>

    </body>
</html>