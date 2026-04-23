<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

if (!isset($_GET['id'])) {
    die("Complaint ID missing");
}
$id = intval($_GET['id']);

// UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Complaint updated');window.location='complaints.php';</script>";
    } else {
        echo "<script>alert('Error updating');</script>";
    }
    $stmt->close();
}

// FETCH
$stmt = $conn->prepare("SELECT * FROM complaints WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<body class="bg-gray-50 font-sans antialiased">
<div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
        <?php require_once "inc/topbar.php"; ?>
        <div class="p-6">
            <h2 class="text-2xl font-semibold mb-4">Edit Complaint</h2>
            <form method="POST" class="bg-white p-6 rounded shadow max-w-md">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded p-2">
                        <option value="pending" <?= $complaint['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="resolved" <?= $complaint['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
