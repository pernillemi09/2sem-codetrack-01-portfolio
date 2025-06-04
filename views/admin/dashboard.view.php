<?php
/** @var \App\Template $this */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */
/** @var \App\Http\Request $request */
/** @var int $unreadMessages */

$this->extend('layout');
?>

<?php $this->start('title', 'Admin Dashboard') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-heading">
            Admin Dashboard
        </h1>
    </div>
</section>

<section class="dashboard">
    <div class="container">
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2 class="section-heading">Quick Actions</h2>
                <div class="action-grid">
                    <a href="/admin/messages" class="link-card">
                        <?php if ($unreadMessages > 0): ?>
                            <span class="badge"><?= $unreadMessages ?></span>
                        <?php endif; ?>
                        <h3>View Messages</h3>
                        <p>Check contact form submissions</p>
                    </a>
                    <a href="/admin/profile" class="link-card disabled">
                        <h3>Edit Profile</h3>
                        <p>Update your personal information</p>
                    </a>
                </div>
            </div>

            <div class="dashboard-card">
                <h2 class="section-heading">Recent Activity</h2>
                <div class="activity-list">
                    <p class="text-muted">No recent activity to show.</p>
                </div>
            </div>
        </div>
    </div>
</section>
