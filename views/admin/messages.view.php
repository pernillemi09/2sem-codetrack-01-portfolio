<?php
/** @var \App\Template $this */
/** @var array<string, array<string>> $errors */
/** @var \App\Http\Request $request */
/** @var \App\Models\Message[] $messages */
/** @var int $count */
/** @var int $countUnread */

$this->extend('layout');
?>

<?php $this->start('title', 'Admin Messages') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-heading">
            Messages
        </h1>
        <p class="page-intro">
            Manage messages received through your contact form. You can mark messages as read/unread and delete them when no longer needed.
        </p>
    </div>
</section>

<section class="messages">
    <div class="container">
        <div class="dashboard-header">
            <div class="header-actions">
                <div class="message-stats">
                    <?php if ($countUnread > 0): ?>
                        <strong><?= $countUnread ?></strong> unread
                        <span class="text-muted">/</span>
                    <?php endif; ?>
                    <span class="text-muted"><?= $count ?> total</span>
                </div>
                <button type="button" class="button button-outline js-toggle-all">
                    Mark All as Read
                </button>
            </div>
        </div>

        <div class="messages-wrapper">
            <?php if (empty($messages)): ?>
                <div class="message-empty">
                    <p class="text-muted">No messages yet.</p>
                    <p>When visitors send you messages through the contact form, they'll appear here.</p>
                </div>
            <?php else: ?>
                <div class="messages-list">
                    <?php foreach ($messages as $message): ?>
                        <article class="message-card<?= $message->getIsRead() ? ' is-read' : '' ?>">
                            <header class="message-header">
                                <h2><?= htmlspecialchars($message->getSubject()) ?></h2>
                                <time datetime="<?= $message->getCreatedAt() ?>" class="message-date">
                                    <?= htmlspecialchars($message->getCreatedAt()) ?>
                                </time>
                            </header>

                            <div class="message-meta">
                                From:
                                <strong><?= htmlspecialchars($message->getName()) ?></strong>
                                &lt;<?= htmlspecialchars($message->getEmail()) ?>&gt;
                            </div>

                            <div class="message-body"><?=
                                htmlspecialchars(trim($message->getMessage()))
                            ?></div>

                            <footer class="message-actions">
                                <form method="POST" action="/admin/messages/<?= $message->getId() ?>/toggle-read" class="inline-form">
                                    <input
                                        type="hidden"
                                        name="_token"
                                        value="<?= htmlspecialchars($request->getCsrfToken()) ?>"
                                    >
                                    <button type="submit" class="button button-small">
                                        <?= $message->getIsRead() ? 'Mark as Unread' : 'Mark as Read' ?>
                                    </button>
                                </form>

                                <form
                                    method="POST"
                                    action="/admin/messages/<?= $message->getId() ?>/delete"
                                    class="inline-form"
                                    onsubmit="return confirm('Are you sure you want to delete this message?')"
                                >
                                    <input
                                        type="hidden"
                                        name="_token"
                                        value="<?= htmlspecialchars($request->getCsrfToken()) ?>"
                                    >
                                    <button type="submit" class="button button-danger button-small">
                                        Delete
                                    </button>
                                </form>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
