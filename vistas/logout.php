<?php
session_destroy();

if (headers_sent()) {
    echo '<scrip> window.location.href="index.php?vista=login"</scrip>';
} else {
    header("location:index.php?vista=login");
}
