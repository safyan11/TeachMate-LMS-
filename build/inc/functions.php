<?php
require_once 'db.php';

// Generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Generate 6-digit OTP
function generateOTP() {
    return sprintf("%06d", mt_rand(1, 999999));
}

// Store password reset token
function storeResetToken($email, $token) {
    global $conn;
    $expires_at = date('Y-m-d H:i:s', time() + 900); // 15 minutes expiry
    
    // Delete any existing tokens for this email
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Insert new token
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $token, $expires_at);
    return $stmt->execute();
}

// Verify reset token
function verifyResetToken($email, $token) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Update user password
function updatePassword($email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    return $stmt->execute();
}

// Delete used token
function deleteToken($email, $token) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ? AND token = ?");
    $stmt->bind_param("ss", $email, $token);
    return $stmt->execute();
}

// Check if email exists
function emailExists($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>