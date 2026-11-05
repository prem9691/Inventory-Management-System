<?php
// Session check karne ka safe aur simple tareeqa
if (session_id() == '') {
    session_start();
}

function set_flash_message($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function display_flash_message() {
    if (isset($_SESSION['flash'])) {
        $msg = $_SESSION['flash']['message'];
        $type = $_SESSION['flash']['type'];
        $bg_color = ($type === 'success') ? '#dcfce7' : '#fee2e2';
        $text_color = ($type === 'success') ? '#16a34a' : '#dc2626';
        $border_color = ($type === 'success') ? '#86efac' : '#fca5a5';

        echo '
        <div id="flash-alert" style="background: ' . $bg_color . '; color: ' . $text_color . '; border: 1px solid ' . $border_color . '; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa-solid ' . ($type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation') . '"></i> ' . $msg . '</span>
            <span onclick="this.parentElement.style.display=\'none\'" style="cursor: pointer; font-weight: bold;">&times;</span>
        </div>
        <script>
            setTimeout(function() {
                let alert = document.getElementById("flash-alert");
                if(alert) { alert.style.transition = "opacity 0.5s ease"; alert.style.opacity = "0"; setTimeout(() => alert.remove(), 500); }
            }, 3000);
        </script>
        ';
        unset($_SESSION['flash']);
    }
}
?>