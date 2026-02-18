<?php
// If form submitted, process the EMI calculation
$emi = '';
$total_interest = '';
$total_payment = '';
$amount = '';
$years = '';
$interest = '';
$price_type = '';
$err = '';

function convertAmount($amount, $type)
{
    switch (strtolower($type)) {
        case 'cr':
        case 'crore':
            return $amount * 10000000;
        case 'lakh':
            return $amount * 100000;
        case 'k':
            return $amount * 1000;
        default:
            return $amount;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $years = isset($_POST['years']) ? floatval($_POST['years']) : 0;
    $interest = isset($_POST['interest']) ? floatval($_POST['interest']) : 0;
    $price_type = isset($_POST['price_type']) ? $_POST['price_type'] : '';

    if ($amount <= 0 || $years <= 0 || $interest < 0) {
        $err = "Please enter valid values.";
    } else {
        $principal = convertAmount($amount, $price_type);
        $months = $years * 12;
        $monthly_interest = $interest / (12 * 100);

        if ($monthly_interest == 0) {
            $emi = $principal / $months;
        } else {
            $emi = ($principal * $monthly_interest * pow(1 + $monthly_interest, $months)) /
                (pow(1 + $monthly_interest, $months) - 1);
        }
        $emi = round($emi, 2);
        $total_payment = round($emi * $months, 2);
        $total_interest = round($total_payment - $principal, 2);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>EMI Instalment Calculator - FindBrick</title>
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
    <style>
        .emi-table th,
        .emi-table td {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <h2 class="mb-4 text-center">EMI Instalment Calculator</h2>
        <div class="row justify-content-center">
            <div class="col-md-7">
                <form method="post" class="card card-body mb-4">
                    <?php if ($err): ?>
                        <div class="alert alert-danger"><?php echo $err; ?></div>
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group col-5">
                            <label>Property Price</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required value="<?php echo htmlspecialchars($amount); ?>">
                        </div>
                        <div class="form-group col-3">
                            <label>Type</label>
                            <select name="price_type" class="form-control">
                                <option value="">Select</option>
                                <option value="cr" <?php if ($price_type == 'cr') echo 'selected'; ?>>Crore</option>
                                <option value="lakh" <?php if ($price_type == 'lakh') echo 'selected'; ?>>Lakh</option>
                                <option value="k" <?php if ($price_type == 'k') echo 'selected'; ?>>Thousand</option>
                                <option value="normal" <?php if ($price_type == 'normal') echo 'selected'; ?>>Normal</option>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label>Duration (years)</label>
                            <input type="number" step="0.1" class="form-control" name="years" required value="<?php echo htmlspecialchars($years); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Interest Rate (%) per annum</label>
                        <input type="number" step="0.01" class="form-control" name="interest" required value="<?php echo htmlspecialchars($interest); ?>">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Calculate EMI</button>
                </form>

                <?php if ($emi): ?>
                    <table class="table table-bordered emi-table">
                        <tr>
                            <th>Principal Amount</th>
                            <td>₹<?php echo number_format(convertAmount($amount, $price_type)); ?></td>
                        </tr>
                        <tr>
                            <th>Loan Tenure</th>
                            <td><?php echo htmlspecialchars($years); ?> years (<?php echo $years * 12; ?> months)</td>
                        </tr>
                        <tr>
                            <th>Interest Rate</th>
                            <td><?php echo htmlspecialchars($interest); ?>% per annum</td>
                        </tr>
                        <tr>
                            <th>Monthly EMI</th>
                            <td class="bg-success text-white h5">₹<?php echo number_format($emi, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Total Payment (Principal + Interest)</th>
                            <td>₹<?php echo number_format($total_payment, 2); ?></td>
                        </tr>
                        <tr>
                            <th>Total Interest Payable</th>
                            <td>₹<?php echo number_format($total_interest, 2); ?></td>
                        </tr>
                    </table>
                <?php endif; ?>

                <a href="javascript:history.back()" class="btn btn-link">Back</a>
            </div>
        </div>
    </div>
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>

</body>

</html>