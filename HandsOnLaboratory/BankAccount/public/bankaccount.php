<?php
require_once __DIR__ . '/../classes/Bankaccount.php';
$bankaccounts = new Bankaccount();

$message = '';
$type = '';
$isEditing = false;
$form = ['id' => 0, 'brand' => '', 'name' => '', 'email' => '', 'phone' => '', 'registrationNumber' => ''];

// ---------- MESSAGE AFTER REDIRECT ----------
$flash = [
    'added'   => 'Bank account added.',
    'updated' => 'Bank account updated.',
    'deleted' => 'Bank account deleted.',
];
if (isset($_GET['msg']) && isset($flash[$_GET['msg']])) {
    $message = $flash[$_GET['msg']];
    $type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- DELETE ----------
    if (isset($_POST['delete_id'])) {
        try {
            $bankaccounts->deleteBankaccount((int)$_POST['delete_id']);
            header('Location: bankaccount.php?msg=deleted');
            exit;
        } catch (mysqli_sql_exception $e) {
            $message = 'Cannot delete this bank account (it may have related records).';
            $type = 'error';
        }

    // ---------- ADD / UPDATE ----------
    } else {
        $form['id']                 = (int)($_POST['update_id'] ?? 0);
        $form['brand']              = trim($_POST['brand'] ?? '');
        $form['name']               = trim($_POST['name'] ?? '');
        $form['email']              = trim($_POST['email'] ?? '');
        $form['phone']              = trim($_POST['phone'] ?? '');
        $form['registrationNumber'] = trim($_POST['registrationNumber'] ?? '');
        $isEditing = $form['id'] > 0;

        if ($form['brand'] === '' || $form['name'] === '' || $form['phone'] === '' || $form['registrationNumber'] === '') {
            $message = 'Please fill in all fields.';
            $type = 'error';
        } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $type = 'error';
        } else {
            try {
                if ($isEditing) {
                    $bankaccounts->updateBankaccount(
                        $form['id'], $form['brand'], $form['name'], $form['email'], $form['phone'], $form['registrationNumber']
                    );
                    header('Location: bankaccount.php?msg=updated');
                } else {
                    $bankaccounts->addBankaccount(
                        $form['brand'], $form['name'], $form['email'], $form['phone'], $form['registrationNumber']
                    );
                    header('Location: bankaccount.php?msg=added');
                }
                exit;
            } catch (mysqli_sql_exception $e) {
                $message = ($e->getCode() === 1062)
                    ? 'That registration number already exists.'
                    : 'Database error: could not save the bank account.';
                $type = 'error';
            }
        }
    }

// ---------- LOAD ACCOUNT TO EDIT ----------
} elseif (isset($_GET['edit'])) {
    $row = $bankaccounts->getBankaccount((int)$_GET['edit']);
    if ($row) {
        $form = $row;
        $isEditing = true;
    } else {
        $message = 'Bank account not found.';
        $type = 'error';
    }
}

// ---------- LIST ----------
$list = $bankaccounts->getAllBankaccounts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Accounts</title>
    <link rel="stylesheet" href="../asset/style.css">
</head>
<body>
<div class="container">

    <header>
        <h1>Bank Account</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="bankaccount.php" class="active">Bank Accounts</a>
        </nav>
    </header>

    <?php if ($message): ?>
        <div class="alert <?php echo $type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?php echo $isEditing ? 'Edit bank account' : 'Add new bank account'; ?></h2>
        <form class="main" method="POST" action="bankaccount.php">
            <?php if ($isEditing): ?>
                <input type="hidden" name="update_id" value="<?php echo (int)$form['id']; ?>">
            <?php endif; ?>
            <div>
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" placeholder="BDO"
                       value="<?php echo htmlspecialchars($form['brand']); ?>" required>
            </div>
            <div>
                <label for="name">Account holder name</label>
                <input type="text" id="name" name="name" placeholder="Juan Dela Cruz"
                       value="<?php echo htmlspecialchars($form['name']); ?>" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="juan@example.com"
                       value="<?php echo htmlspecialchars($form['email']); ?>" required>
            </div>
            <div>
                <label for="phone">Phone number</label>
                <input type="text" id="phone" name="phone" placeholder="09171234567"
                       value="<?php echo htmlspecialchars($form['phone']); ?>" required>
            </div>
            <div>
                <label for="registrationNumber">Registration number</label>
                <input type="text" id="registrationNumber" name="registrationNumber" placeholder="2024001"
                       value="<?php echo htmlspecialchars($form['registrationNumber']); ?>" required>
            </div>
            <div class="actions">
                <button type="submit" class="btn"><?php echo $isEditing ? 'Save changes' : 'Add account'; ?></button>
                <?php if ($isEditing): ?>
                    <a href="bankaccount.php" class="btn ghost">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Bank Accounts <span class="count">(<?php echo count($list); ?>)</span></h2>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $s): ?>
                        <tr class="<?php echo ($isEditing && (int)$form['id'] === (int)$s['id']) ? 'editing' : ''; ?>">
                            <td><?php echo (int)$s['id']; ?></td>
                            <td><?php echo htmlspecialchars($s['brand']); ?></td>
                            <td><?php echo htmlspecialchars($s['name']); ?></td>
                            <td><?php echo htmlspecialchars($s['email']); ?></td>
                            <td><?php echo htmlspecialchars($s['phone']); ?></td>
                            <td><?php echo htmlspecialchars($s['registrationNumber']); ?></td>
                            <td><div class="row-actions">
                                <a class="btn-sm" href="bankaccount.php?edit=<?php echo (int)$s['id']; ?>">Edit</a>
                                <form method="POST" action="bankaccount.php"
                                      onsubmit="return confirm(<?php echo htmlspecialchars(json_encode('Delete ' . $s['name'] . '?'), ENT_QUOTES); ?>);">
                                    <input type="hidden" name="delete_id" value="<?php echo (int)$s['id']; ?>">
                                    <button type="submit" class="btn-sm danger">Delete</button>
                                </form>
                            </div></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$list): ?>
                        <tr><td colspan="7" class="empty">No account yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
