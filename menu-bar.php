<nav class="sidebar">
    <h3>Menu</h3>
    <ul class="menu-list">
        <li>
            <a href="index.php?type=personal" class="<?= ($type == 'personal') ? 'active' : '' ?>">
                📌 Personal
            </a>
        </li>
        <li>
            <a href="index.php?type=shared" class="<?= ($type == 'shared') ? 'active' : '' ?>">
                👥 Shared
            </a>
        </li>
    </ul>
</nav>