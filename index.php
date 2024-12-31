<?php
// Menyertakan konfigurasi
include('config/config.php');

// Redirect ke halaman user
header('Location: ' . base_url('user/index.php'));
