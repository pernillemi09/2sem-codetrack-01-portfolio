<?php
/** @var \App\Template $this */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */
/** @var \App\Dto\ProjectDto[] $projects */

$this->extend('layout');
?>

<?php $this->start('title', 'Projects') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-heading">
            My Projects
        </h1>
        <p class="page-intro">
            Welcome to my project showcase! Here you'll find a collection of my recent work, highlighting my skills in web development and software engineering. Each project demonstrates my commitment to creating clean, efficient, and user-friendly solutions.
        </p>
    </div>
</section>

<section class="project-list">
    <div class="container">
        <div class="project-grid">
            <?php foreach ($projects as $index => $project): ?>
                <div
                    class="project-row <?= $index % 2 === 1 ? 'project-row--reverse' : '' ?>"
                >
                    <div class="project-image">
                        <img
                            src="<?= htmlspecialchars($project->image) ?>"
                            alt="<?= htmlspecialchars($project->title) ?>"
                        >
                    </div>
                    <div class="project-content">
                        <h2>
                            <?= htmlspecialchars($project->title) ?>
                        </h2>
                        <p class="project-description">
                            <?= htmlspecialchars($project->description) ?>
                        </p>
                        <p class="technologies">
                            <strong>Technologies:</strong>
                            <?= htmlspecialchars($project->technologies) ?>
                        </p>
                        <div class="project-actions">
                            <a
                                href="<?= htmlspecialchars($project->link) ?>"
                                class="btn-view-project"
                            >
                                View Project
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
