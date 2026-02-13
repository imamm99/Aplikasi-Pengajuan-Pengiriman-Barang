<?php $menuConfig = loadMenuConfig(); ?>
<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="<?= base_url('admin/index.php') ?>">
            <span class="align-middle">AdminPanel</span>
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Pages
            </li>

            <?php
            // Simple logic to determine active menu
            $current_path = $_SERVER['REQUEST_URI'];
            $user_role = $_SESSION['role'] ?? 'guest';

            // Determine dashboard URL based on role
            $dashboard_url = ($user_role === 'admin') ? 'admin/index.php' : 'pengaju/index.php';
            $is_dashboard_active = (strpos($current_path, '/admin/') !== false && $user_role === 'admin') ||
                (strpos($current_path, '/pengaju/') !== false && $user_role === 'pengaju');
            ?>

            <li class="sidebar-item <?= $is_dashboard_active ? 'active' : '' ?>">
                <a class="sidebar-link" href="<?= base_url($dashboard_url) ?>">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-header">Modules</li>

            <?php
            if (!empty($menuConfig['modules'])):
                foreach ($menuConfig['modules'] as $moduleName => $module):
                    // Check if user has permission
                    if (userCanAccessModule($moduleName)):
                        $label = $module['label'] ?? $moduleName;
                        if ($moduleName === 'transaksi_pengiriman' && $_SESSION['role'] === 'pengaju') {
                            $label = 'Pengajuan Pengiriman';
                        }

                        // Determine active state for module
                        $is_active = strpos($current_path, "/$moduleName/") !== false;
                        ?>
                        <li class="sidebar-item <?= $is_active ? 'active' : '' ?>">
                            <a class="sidebar-link" href="<?= base_url($moduleName . '/index.php') ?>">
                                <i class="align-middle" data-feather="<?= $module['icon'] ?? 'box' ?>"></i>
                                <span class="align-middle"><?= htmlspecialchars($label) ?></span>
                            </a>
                        </li>
                        <?php
                    endif;
                endforeach;
            endif;
            ?>

            <li class="sidebar-header">User</li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="<?= base_url('logout.php') ?>">
                    <i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>