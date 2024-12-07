<?php
// popup.php

// Assuming $update_success is set to true in the main page if an update was successful
?>

<!-- Popup Styles -->
<style>
    .popup {
        display: none; /* Hidden by default */
        position: fixed;
        left: 60%;
        top: 5%;
        transform: translate(-50%, -50%);
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        opacity: 0;
        transition: opacity 1s;
    }

    .popup p {
        margin: 0 0 10px;
        font-size: 18px;
    }
</style>

<!-- Popup Message -->
<div id="popup" class="popup">
    <p>Updated Successfully!</p>
</div>

<!-- JavaScript to show the popup -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($update_success) && $update_success): ?>
            const popup = document.getElementById('popup');
            popup.style.display = 'block';
            setTimeout(function() {
                popup.style.opacity = '1';
                setTimeout(function() {
                    popup.style.opacity = '0';
                    setTimeout(function() {
                        popup.style.display = 'none';
                    }, 1000); // Hide completely after 1 second
                }, 3000); // Fade out after 3 seconds
            }, 100); // Short delay to trigger CSS transition
        <?php endif; ?>
    });
</script>
