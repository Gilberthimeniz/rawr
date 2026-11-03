<?php
require_once __DIR__ . '/../classes/Bankaccount.php';

$bankaccounts = new Bankaccount();
$totalBankaccounts = $bankaccounts->countBankaccounts();
$rows = $bankaccounts->getAllBankaccounts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body>
<div class="container">

    <header>
        <h1>Bank Account</h1>
        <nav>
            <a href="index.php" class="active">Dashboard</a>
            <a href="bankaccount.php">Bank Accounts</a>
        </nav>
    </header>

    <div class="stats">
        <div class="card stat"><strong><?php echo $totalBankaccounts; ?></strong><span>Bank Accounts</span></div>
    </div>

    <div class="card">
        <h2>Active</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Brand</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Reg. No.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $s): ?>
                        <tr>
                            <td><?php echo (int)$s['id']; ?></td>
                            <td><?php echo htmlspecialchars($s['brand']); ?></td>
                            <td><?php echo htmlspecialchars($s['name']); ?></td>
                            <td><?php echo htmlspecialchars($s['email']); ?></td>
                            <td><?php echo htmlspecialchars($s['phone']); ?></td>
                            <td><?php echo htmlspecialchars($s['registrationNumber']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr><td colspan="6" class="empty">No account yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
