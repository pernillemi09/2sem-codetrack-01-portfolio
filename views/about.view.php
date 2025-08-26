<?php
/** @var \App\Template $this */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */

$this->extend('layout');
?>

<?php $this->start('title', 'About Me') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-heading">
            About Me
        </h1>
        <p class="page-intro">
            Get to know more about my journey, skills, and passion for multimediedesigner. I'm dedicated to creating elegant solutions to complex problems.
        </p>
    </div>
</section>

<section class="bio">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2 class="section-heading">Bio</h2>
                <p>
                    Hi, I'm a passionate multimediedesigner with a focus on building clean,
                    efficient, and user-friendly applications. I specialize in PHP
                    development and modern web technologies, with a particular interest
                    in creating robust back-end solutions.
                </p>
                <p>
                    My journey in web development began with a fascination for problem-solving
                    and has evolved into a deep appreciation for well-structured, maintainable code.
                    I believe in writing code that not only works but is also easy to understand
                    and maintain.
                </p>
                <p>
                    When I'm not coding, I enjoy keeping up with the latest tech trends,
                    contributing to open-source projects, and sharing knowledge with the
                    developer community. I'm particularly excited about modern PHP practices,
                    test-driven development, and building scalable applications.
                </p>
            </div>

            <div class="about-image">
                <img src="images/105494933_1597334893778608_8188881637984347372_n.jpg" alt="Headshot of Abigail looking suspicious" class="portrait-image">
            </div>
        </div>
    </div>
</section>

<section class="skills section-padding">
    <div class="container">
        <h2 class="section-heading">Skills</h2>
        <div class="skill-items">
            <span class="skill-tag">HTML</span>
            <span class="skill-tag">CSS</span>
            <span class="skill-tag">Photoshop</span>
            <span class="skill-tag">Illustrator</span>
            <span class="skill-tag">InDesign</span>
            <span class="skill-tag">Graphic Design</span>
        </div>
    </div>
</section>
