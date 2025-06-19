
<?php
session_start();
require 'db_config.php';

// Get session values safely
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = (int)$_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
    exit();
}
$created_by = $user_id;
$created_by_name = $username;
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

    <head>
        <meta charset="utf-8" />
        <title>Order Form</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Drezoc - Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="MyraStudio" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

		<!-- App css -->
		<link href="assets/css/style.min.css" rel="stylesheet" type="text/css">
		<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
		<script src="assets/js/config.js"></script>
    </head>

    <body>

        <!-- Begin page -->
        <div class="layout-wrapper">

            <!-- ========== Left Sidebar ========== -->
            <div class="main-menu">
                <!-- Brand Logo -->
                 <div class="logo-box">
                <!-- Brand Logo Light -->
                  <a href="index.php" class="logo-light">
                    <h4 style="color: white;">Order Tracking</h4>
                </a>

                
            </div>
            <?php include 'sidebar.php'; ?>
            </div>

            
<style>
        form {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px 30px; /* row-gap, column-gap */
        }

        /* Stack each label + input vertically */
        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
   
            user-select: none;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        select,
        textarea {
            padding: 10px 12px;
            border: 1.8px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.25s ease;
            font-family: inherit;
            resize: vertical;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus,
        select:focus,
        textarea:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
        }

        /* Checkbox & radio groups span full width */
        .checkbox-group, .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 6px;
        }

        .checkbox-group label, .radio-group label {
            font-weight: 500;
            user-select: none;
            cursor: pointer;
            align-items: center;
            display: flex;
            gap: 6px;
           
        }

        /* Let checkboxes & radios be easier to click */
        input[type="checkbox"], input[type="radio"] {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        /* Make checkbox and radio groups take full row */
        .checkbox-group,
        .radio-group {
            grid-column: span 3;
        }

        /* Button styles */
        button {
            grid-column: span 3;
            background-color: #007bff;
            color: white;
            
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            user-select: none;
        }

        button:hover {
            background-color: #0056b3;
        }

     @media (max-width: 768px) {

        
   form {
    display: inline;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px 30px;
}
    .form-group,
    .checkbox-group,
    .radio-group,
    button {
          /* force full width */
              width: 92%;
    }
}

    </style>
            <!-- Start Page Content here -->
            <div class="page-content">

                <?php include 'topbar.php'; ?>

                <div class="px-3">

                    <!-- Start Content-->
                    <div class="container-fluid">
                        <br>
                        
               <div class="container">
 
  <form action="submit_order.php" method="POST" enctype="multipart/form-data">

    <?php

    $orderCount = 0;
    $orderCountResult = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
    if ($orderCountResult && $row = $orderCountResult->fetch_assoc()) {
        $orderCount = (int)$row['total_orders'];
    }
    ?>
    <div class="form-group" style="grid-column: span 3;">
        <h2>Order Invoice #: <?= $orderCount + 1 ?></h2>

    </div>

        <?php
       
        $staff = $conn->query("SELECT id, first_name, last_name FROM staff");
        $staff2 = $conn->query("SELECT id, first_name, last_name FROM staff");
        
        $bankdetails = $conn->query("SELECT id, account_number, account_name, branch_name, ifsc_code, remarks FROM bank_accounts");
        ?>

       
        <div class="form-group">
            <label for="date">Date:</label>
            <input 
            type="date" 
            name="date" 
            id="date"  
            value="<?= date('Y-m-d') ?>" 
            readonly
            >
        </div>
        <div class="form-group">
            <label for="delivery_date">Delivery Date:</label>
            <input 
                type="date" 
                name="delivery_date" 
                id="delivery_date"  
                onclick="this.showPicker()" 
                required
                min="<?= date('Y-m-d') ?>"
            >
        </div>

        <div class="form-group">
            <label for="time">Delivery Time:</label>
            <input type="time" name="time" id="time" onclick="this.showPicker()" required>
        </div>
        <div class="form-group">
            <label for="name">Customer Name:</label>
            <input type="text" name="name" id="name" placeholder="Enter customer name" required>
        </div>
        <div class="form-group">
            <label for="contact">Contact #:</label>
            <input type="number" name="contact" id="contact" placeholder="Enter contact number" required>
        </div>
        <div class="form-group">
            <label>Is WhatsApp Number Same as Contact #?</label>
            <div class="radio-group">
            <label>
                <input type="radio" name="is_whatsapp_same" value="yes" checked> Yes
            </label>
            <label>
                <input type="radio" name="is_whatsapp_same" value="no"> No
            </label>
            </div>
        </div>
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
                whatsappInput.value = '';
                whatsappInput.readOnly = false;
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
        <div class="form-group" >
          
            <label for="whatsapp_number">WhatsApp:</label>
            <input 
                type="number" 
                name="whatsapp_number" 
                id="whatsapp_number" 
                placeholder="Enter WhatsApp number"
               
            >
         
            <button type="button" id="check_whatsapp_btn" style="height:40px; padding:0 18px; font-size:15px;">Check Online </button>
        </div>

        <div class="form-group">
            <label>Order Taker:</label>
            <input type="text" value="<?= htmlspecialchars($username) ?>" readonly>
            <input type="hidden" name="order_taker_id" value="<?= htmlspecialchars($user_id) ?>">
        </div>

        <div class="form-group">
            <label for="order_maker_id">Order Maker:</label>
            <select name="order_maker_id" id="order_maker_id" required>
                <?php while ($row = $staff2->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
                   <!-- Empty form-group to fill the third column here -->
        <div></div>

        <div class="form-group" style="grid-column: span 3;">
        <label>Customer Address</label>
        <input type="text" name="customer_address" id="customer_address" placeholder="Enter customer address" >
        </div>

       <div class="form-group" style="grid-column: span 3;">
           <label>Order Source:</label>
           <div class="radio-group">
               <label><input type="radio" name="source" value="Whatsapp"> Whatsapp</label>
    <label><input type="radio" name="source" value="Instagram"> Instagram</label>
    <label><input type="radio" name="source" value="Facebook"> Facebook</label>
    <label><input type="radio" name="source" value="Physical"> Physical</label>
    <label>
      <input type="radio" name="source" value="Other" id="source_other_radio"> Other
    </label>
    <input 
      type="text" 
      name="source_other_text" 
      id="source_other_text" 
      placeholder="Please specify" 
      style="display:none; margin-left:10px; min-width:100%;">
  </div>
  <br>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var radios = document.querySelectorAll('input[name="source"]');
    var otherText = document.getElementById('source_other_text');
    var otherRadio = document.getElementById('source_other_radio');

    function toggleOtherText() {
      if (otherRadio.checked) {
        otherText.style.display = 'inline-block';
        otherText.focus();
      } else {
        otherText.style.display = 'none';
        otherText.value = '';
      }
    }

    radios.forEach(function(radio) {
      radio.addEventListener('change', toggleOtherText);
    });

    // On page load, ensure correct state
    toggleOtherText();
  });
</script>

<div class="form-group" style="grid-column: span 3;">
    <label for="description">Description:</label>
    <div id="quill-editor" style="height: 180px; background: #fff;"></div>
    <input type="hidden" name="description" id="description">
</div>
<!-- Quill.js CDN -->
<link href="./assets/quill.snow.css" rel="stylesheet">
<script src="./assets/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: '',
        modules: {
            toolbar: [
                [{ header: [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'blockquote', 'code-block'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['clean']
            ]
        }
    });
    var form = document.querySelector('form');
    form.addEventListener('submit', function() {
        document.getElementById('description').value = quill.root.innerHTML;
    });

    // Theme switch for Quill editor
    function updateQuillTheme() {
        var html = document.documentElement;
        var isDark = html.getAttribute('data-bs-theme') === 'dark';
        var quillEditor = document.querySelector('.ql-editor');
        var quillToolbar = document.querySelector('.ql-toolbar');
        if (quillEditor) {
            quillEditor.style.background = isDark ? '#23272f' : '#fff';
            quillEditor.style.color = isDark ? '#f1f1f1' : '#222';
        }
        if (quillToolbar) {
            quillToolbar.style.background = isDark ? '#23272f' : '#fff';
            quillToolbar.style.color = isDark ? '#f1f1f1' : '#222';
        }
    }

    // Initial theme set
    updateQuillTheme();

    // Listen for theme changes (if your theme switcher changes data-bs-theme)
    const observer = new MutationObserver(updateQuillTheme);
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
});
</script>

       
    <!-- Responsive Payment Details Section -->
    <style>
    @media (max-width: 768px) {
        .payment-details-table {
            display: block !important;
            width: 50% !important;
            border: none !important;
        }
        .payment-details-table thead,
        .payment-details-table tr,
        .payment-details-table td,
        .payment-details-table th {
            display: block !important;
            width: 100% !important;
            border: none !important;
        }
        .payment-details-table th[colspan] {
            text-align: left !important;
      
            font-size: 18px;
            padding: 10px 0 5px 0;
            border-bottom: 1px solid #eee;
        }
        .payment-details-table td {
            padding: 0 !important;
        }
        .payment-details-table .form-group {
            
            margin-bottom: 18px;
        }
    }
    </style>
    <table class="table table-bordered payment-details-table" width="100%" style="margin-top: 20px; grid-column: span 3;">
        <thead>
            <tr>
                <th colspan="2">Payment Details</th>
            </tr>
        </thead>
    <tr>
        <td style="width: 50%;">

                <div class="form-group">
                    <label for="total">Total Order Amount:</label>
                    <input type="number" name="total" id="total" placeholder="Enter total order amount">
                </div>
      

        
                  <br>

                <!-- <div class="form-group">
                    <label>Advance Type:</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="advance_type" value="bank" id="advance_type_bank"> Bank
                        </label>
                        <label>
                            <input type="radio" name="advance_type" value="online" id="advance_type_online"> Online
                        </label>
                        <label>
                            <input type="radio" name="advance_type" value="card" id="advance_type_card"> Card
                        </label>
                    </div> -->

                <div class="form-group" id="advance_group">
                    <label for="advance" id="advance_label">Advance:</label>
                    <input type="number" name="advance" id="advance" placeholder="Advance amount" readonly>
                </div>
                
                </div>
                  <br>
                <div class="form-group">
                    <label for="remaining">Remaining:</label>
                    <input type="number" name="remaining" id="remaining" placeholder="Remaining amount" readonly >
                </div>
            </div>

            <br>
        <div class="form-group">
            <label for="file_media">Upload Files:</label>
            <input 
                type="file" 
                name="file_media[]" 
                id="file_media" 
                accept="image/*" 
                multiple 
                capture="environment"
                style="font-size:16px;"
            >
            <div id="file_preview" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;"></div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fileInput = document.getElementById('file_media');
            var previewDiv = document.getElementById('file_preview');

            fileInput.addEventListener('change', function(e) {
                previewDiv.innerHTML = '';
                var files = fileInput.files;
                if (files.length > 0) {
                    // Show only the first file as preview
                    var file = files[0];
                    if (file.type.startsWith('image/')) {
                        var reader = new FileReader();
                        reader.onload = function(ev) {
                            var img = document.createElement('img');
                            img.src = ev.target.result;
                            img.style.maxWidth = '120px';
                            img.style.maxHeight = '120px';
                            img.style.borderRadius = '8px';
                            img.style.border = '1px solid #ccc';
                            previewDiv.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        var span = document.createElement('span');
                        span.textContent = file.name;
                        previewDiv.appendChild(span);
                    }
                    // Optionally, show count of total files
                    if (files.length > 1) {
                        var count = document.createElement('div');
                        count.textContent = '+ ' + (files.length - 1) + ' more file(s) selected';
                        count.style.fontSize = '13px';
                        count.style.color = '#666';
                        previewDiv.appendChild(count);
                    }
                }
            });
        });
        </script>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateRemaining() {
                var total = parseFloat(document.getElementById('total').value) || 0;
                var advance = parseFloat(document.getElementById('advance').value) || 0;
                var remaining = total - advance;
                 var advance = cash_payment + online_amount + card_amount;
                document.getElementById('remaining').value = remaining >= 0 ? remaining : 0;
            }
            document.getElementById('total').addEventListener('input', updateRemaining);
            document.getElementById('advance').addEventListener('input', updateRemaining);

           
        });
        </script>

        </td>
        <td style="width: 50%;">
        <div class="form-group">
            <div class="form-group">
                <label>Payment Method:</label>
                <div class="checkbox-group" id="payment-method-group">
                    <label><input type="checkbox" name="payment[]" value="Cash" id="payment_cash"> Cash</label>
                    <label><input type="checkbox" name="payment[]" value="Bank" id="payment_bank"> Bank</label>
                    <span id="more-payment-methods" style="display:none;">
                        <label><input type="checkbox" name="payment[]" value="Online" id="payment_online"> Online</label>
                        <label><input type="checkbox" name="payment[]" value="Card" id="payment_card"> POS/Card</label>
                    </span>
                </div>
            </div>
            <div class="form-group">

                <div class="form-group" id="bank_detail_group" style="display:none;">
                    <label for="bank_detail">Cash Amount:</label>
                    <input type="number" name="cash_payment" id="cash_payment" placeholder="Enter cash Amount">
                </div>
                
                <hr>
                 <label>Online Amount:</label>
                   <div class="form-group" id="online_amount_detail_group" style="display:none;">
                  
                    <input type="number" name="online_amount" id="online_amount"  placeholder="Enter Online Amount">
                </div>
                <div class="form-group" id="bank_detail_group" style="display:none;">
                    <label for="bank_detail">Online Bank Details:</label>
                    <select name="bank_detail" id="bank_detail">
                        <option value="">Select bank account</option>
                        <?php while ($row = $bankdetails->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['id']) ?>">
                                <?= htmlspecialchars($row['account_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>


                <div class="form-group" id="ac_detail_group" style="display:none;">
                    <label for="ac_detail">Customer A/C Detail:</label>
                    <input type="text" name="ac_detail" id="ac_detail" placeholder="Enter account details">
                </div>

                   <div class="form-group" id="transaction_id_detail_group" style="display:none;">
                    <label for="transaction_id">Transaction ID :</label>
                    <input type="number" name="transaction_id" id="transaction_id"  placeholder="Enter Transaction ID">
                </div>
         
                <hr>
                      <div class="form-group" id="card_amount_detail_group" style="display:none;">
                        <label for="card_amount">POS / Card Amount :</label>
                        <input type="number" name="card_amount" id="card_amount" placeholder="Enter Card Amount">
                    </div>
           <div class="form-group" id="pos_bank_detail_group" style="display:none;">
    <label for="pos_bank_detail">POS Bank Details:</label>
    <select name="pos_bank_detail" id="pos_bank_detail">
        <option value="">Select bank account</option>
       <?php
$bankdetails2 = $conn->query("SELECT id, account_name, account_number FROM bank_accounts");
while ($row = $bankdetails2->fetch_assoc()):
    $value = (int)$row['id']; // Use numeric ID as value
    $label = htmlspecialchars($row['account_name']); // Only account name as label
?>
    <option value="<?= $value ?>"><?= $label ?></option>
<?php endwhile; ?>
    </select>
</div>

               <div class="form-group" id="card_detail_group" style="display:none;">
                    <label for="card_detail">Card Detail (Last 4 Digits):</label>
                    <input type="number" name="card_detail" id="card_detail" placeholder="1234" inputmode="numeric" pattern="\d{1,4}" max="9999">
                </div>
                                <script>
                                    // Enforce max 4 digits for card_detail input
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var cardDetailInput = document.getElementById('card_detail');
                                        if (cardDetailInput) {
                                            cardDetailInput.addEventListener('input', function() {
                                                if (this.value.length > 4) {
                                                    this.value = this.value.slice(0, 4);
                                                }
                                            });
                                        }
                                    });
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var cash = document.getElementById('payment_cash');
                                        var bank = document.getElementById('payment_bank');
                                        var online = document.getElementById('payment_online');
                                        var card = document.getElementById('payment_card');
                                        var moreMethods = document.getElementById('more-payment-methods');
                                        var cashPaymentGroup = document.querySelectorAll('#bank_detail_group')[0]; // First is cash_payment group
                                        var bankDetailGroup = document.querySelectorAll('#bank_detail_group')[1]; // Second is bank_detail group
                                        var acDetailGroup = document.getElementById('ac_detail_group');
                                        var cardDetailGroup = document.getElementById('card_detail_group');
                                        var cardAmountDetailGroup = document.getElementById('card_amount_detail_group');
                                        var transactionIdDetailGroup = document.getElementById('transaction_id_detail_group');
                                        var onlineAmountDetailGroup = document.getElementById('online_amount_detail_group');
                                        var posBankDetailGroup = document.getElementById('pos_bank_detail_group');
                                        var form = document.querySelector('form');
                                        var totalInput = document.getElementById('total');
                                        var cashAmountInput = document.getElementById('cash_payment');
                                        var onlineAmountInput = document.getElementById('online_amount');
                                        var cardAmountInput = document.getElementById('card_amount');
                                        var submitBtn = form.querySelector('button[type="submit"]');

                                        // --- Calculate advance and remaining based on all payment fields and check/uncheck ---
                                        function updateAdvanceAndRemaining() {
                                            var total = parseFloat(document.getElementById('total').value) || 0;
                                            var cashVal = (cash.checked) ? (parseFloat(document.getElementById('cash_payment').value) || 0) : 0;
                                            var onlineVal = (bank.checked && online && online.checked) ? (parseFloat(document.getElementById('online_amount').value) || 0) : 0;
                                            var cardVal = (bank.checked && card && card.checked) ? (parseFloat(document.getElementById('card_amount').value) || 0) : 0;

                                            var advance = cashVal + onlineVal + cardVal;
                                            document.getElementById('advance').value = advance;

                                            var remaining = total - advance;
                                            document.getElementById('remaining').value = remaining >= 0 ? remaining : 0;

                                            // Overpaid check
                                            if (advance > total && total > 0) {
                                                alert(`You can only pay up to ${total}.`);
                                                if (cashAmountInput === document.activeElement) {
                                                    cashAmountInput.value = '';
                                                    cashAmountInput.dispatchEvent(new Event('input'));
                                                }
                                                if (onlineAmountInput === document.activeElement) {
                                                    onlineAmountInput.value = '';
                                                    onlineAmountInput.dispatchEvent(new Event('input'));
                                                }
                                                if (cardAmountInput === document.activeElement) {
                                                    cardAmountInput.value = '';
                                                    cardAmountInput.dispatchEvent(new Event('input'));
                                                }
                                                if (submitBtn) submitBtn.disabled = true;
                                                return;
                                            } else {
                                                if (submitBtn) submitBtn.disabled = false;
                                            }
                                        }

                                        // Attach updateAdvanceAndRemaining to relevant fields
                                        document.getElementById('total').addEventListener('input', updateAdvanceAndRemaining);
                                        document.getElementById('cash_payment').addEventListener('input', updateAdvanceAndRemaining);
                                        document.getElementById('online_amount').addEventListener('input', updateAdvanceAndRemaining);
                                        document.getElementById('card_amount').addEventListener('input', updateAdvanceAndRemaining);

                                        // Also update when payment method checkboxes change (to show/hide fields and recalc)
                                        [cash, bank, online, card].forEach(function(el) {
                                            if (el) el.addEventListener('change', function() {
                                                updatePaymentOptions();
                                                updateAdvanceAndRemaining();
                                            });
                                        });

                                        function updatePaymentOptions() {
                                            // Show/hide the more payment methods section
                                            moreMethods.style.display = bank.checked ? 'inline' : 'none';

                                            // Reset checkboxes if bank is unchecked
                                            if (!bank.checked) {
                                                if (online) online.checked = false;
                                                if (card) card.checked = false;
                                            }

                                            // Show/hide detail fields
                                            bankDetailGroup.style.display = 'none'; // This will be shown conditionally
                                            cashPaymentGroup.style.display = cash.checked ? '' : 'none';

                                            // Show online payment fields if online is checked
                                            if (bank.checked && online && online.checked) {
                                                acDetailGroup.style.display = '';
                                                transactionIdDetailGroup.style.display = '';
                                                onlineAmountDetailGroup.style.display = '';
                                                bankDetailGroup.style.display = ''; // Online uses this field
                                            } else {
                                                acDetailGroup.style.display = 'none';
                                                transactionIdDetailGroup.style.display = 'none';
                                                onlineAmountDetailGroup.style.display = 'none';
                                            }

                                            // Show card payment fields if card is checked
                                            if (bank.checked && card && card.checked) {
                                                cardDetailGroup.style.display = '';
                                                cardAmountDetailGroup.style.display = '';
                                                posBankDetailGroup.style.display = ''; // Card uses POS bank
                                            } else {
                                                cardDetailGroup.style.display = 'none';
                                                cardAmountDetailGroup.style.display = 'none';
                                                posBankDetailGroup.style.display = 'none';
                                            }
                                        }

                                        // Initial state
                                        updatePaymentOptions();
                                        updateAdvanceAndRemaining();

                                        // Validation: If Bank is checked, at least one of Online or Card must be checked
                                        // If Bank+Online, require bank_detail, online_amount (ac_detail and transaction_id not required)
                                        // If Bank+Card, require pos_bank_detail, card_detail, card_amount
                                        form.addEventListener('submit', function(e) {
                                            // If neither cash nor bank is checked, prevent submit
                                            if (!cash.checked && !bank.checked) {
                                                alert('Please select at least one payment method: Cash or Bank.');
                                                if (cash) cash.focus();
                                                e.preventDefault();
                                                return false;
                                            }

                                            // Check: If any payment field is filled but total order amount is empty, prevent submit
                                            var totalVal = totalInput.value.trim();
                                            var cashVal = document.getElementById('cash_payment').value.trim();
                                            var onlineVal = document.getElementById('online_amount').value.trim();
                                            var cardVal = document.getElementById('card_amount').value.trim();
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
                                                    // ac_detail and transaction_id are NOT required anymore
                                                    if (!document.getElementById('online_amount').value.trim()) {
                                                        alert('Online Amount is required.');
                                                        document.getElementById('online_amount').focus();
                                                        e.preventDefault();
                                                        return false;
                                                    }
                                                }
                                                // Bank + Card
                                                if (card && card.checked) {
                                                    if (!document.getElementById('pos_bank_detail').value.trim()) {
                                                        alert('POS Bank Detail is required.');
                                                        document.getElementById('pos_bank_detail').focus();
                                                        e.preventDefault();
                                                        return false;
                                                    }
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
                                                // If both Online and Card are checked, require all fields for both
                                                if ((online && online.checked) && (card && card.checked)) {
                                                    // Already validated above, but you can add any additional logic here if needed
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
                                    });
                                </script>
            </div>
        </div>
        </td>
        </tr>
        </table>



 


        <button type="submit" style="padding: 14px 0;">Submit Order</button>
    </form>
</div>
                    </div> <!-- container -->

                </div> <!-- content -->

        <!-- <?php //include 'footer.php'; ?> -->

            </div>
            <!-- End Page content -->


        </div>
        <!-- END wrapper -->
        
        <!-- App js -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.js"></script>

    </body>
</html>