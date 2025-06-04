<?php
/** @var \App\Template $this */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */

$this->extend('layout');
?>

<?php $this->start('title', 'Welcome') ?>

<section class="hero section-padding">
    <div class="container">
        <h1>Hi, I'm Abigail<span class="accent">Multimedia Designer</span></h1>
        <p class="tagline">I create modern and user-friendly web solutions with a focus on quality and performance.</p>
        <a href="/contact" class="button">Contact Me</a>
    </div>
</section>

<section class="intro section-padding">
    <div class="container">
        <div class="intro-content">
            <div class="intro-text">
                <h2 class="section-heading">About Me</h2>
                <p>I'm a passionate web developer with a particular interest in user-friendly design and clean code.
                   Through my projects, I strive to create solutions that not only work well
                   but also make a real difference for users.</p>
                <p>With a keen eye for detail and a commitment to staying current with web technologies,
                   I bring ideas to life through elegant and efficient code.</p>
            </div>
            <div class="intro-image">
                <img src="images/profile-landscape.jpg" alt="Headshot of Abigail looking happy" class="profile-image">
            </div>
        </div>
    </div>
</section>

<section class="quick-links section-padding">
    <div class="container">
        <h2 class="section-heading">Explore More</h2>
        <div class="links-grid">
            <a href="/projects" class="link-card">
                <h3>Projects</h3>
                <p>See examples of my recent projects and solutions</p>
            </a>
            <a href="/about" class="link-card">
                <h3>About</h3>
                <p>Learn more about my background and technical skills</p>
            </a>
            <a href="/contact" class="link-card">
                <h3>Contact</h3>
                <p>Let's discuss your next project</p>
            </a>
        </div>
    </div>
</section>
