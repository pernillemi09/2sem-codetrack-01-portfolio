<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Dto\ProjectDto;
use App\Http\Request;
use App\Http\Response;

/**
 * Controller for handling requests to the projects page.
 *
 * Provides actions for rendering the projects page with a list of projects.
 */
class ProjectController extends Controller
{
    /**
     * Show the projects page.
     * Renders the 'projects' view with a list of projects.
     *
     * @param Request $request The current HTTP request instance.
     * @return Response
     */
    public function index(Request $request): Response
    {
        $response = new Response();
        $response->setTemplate($this->template, 'projects', [
            ...$this->pullFlash($response),
            'request' => $request,
            'projects' => $this->getProjects(),
        ]);
        return $response;
    }

    /**
     * Get list of projects to display.
     *
     * @return array<int, ProjectDto>
     */
    private function getProjects(): array
    {
        return [
            new ProjectDto(
                title: 'Portfolio Website',
                description: 'A modern portfolio website built from scratch with PHP. Features a clean, responsive design with a mobile-first approach, optimized performance, and maintainable object-oriented code following the MVC pattern. Includes custom form handling with rate limiting and a modular template system.',
                technologies: 'PHP, HTML5, CSS3, Responsive Design',
                image: 'images/projects/portfolio.jpg',
                code: 'https://github.com/madh-zealand/2sem-codetrack-01-portfolio',
                link: '#portfolio',
            ),
            new ProjectDto(
                title: 'Digital Guestbook',
                description: 'An interactive guestbook application that allows visitors to leave messages and engage with the community. Features user-friendly forms with validation, spam protection, and a clean interface. Messages are stored securely and displayed in a paginated format.',
                technologies: 'PHP, MySQL, CSS Grid/Flexbox, Form Validation',
                image: 'images/projects/guestbook.jpg',
                code: 'https://github.com/madh-zealand/tba',
                link: '#guestbook',
            ),
            new ProjectDto(
                title: 'Browser Game',
                description: 'A fun and engaging browser-based game built with vanilla JavaScript. Implements game mechanics using the requestAnimationFrame API, features smooth animations, collision detection, and a scoring system. Demonstrates strong understanding of DOM manipulation and event handling.',
                technologies: 'JavaScript, HTML Canvas, CSS Animations',
                image: 'images/projects/webgame.jpg',
                code: 'https://github.com/madh-zealand/tba',
                link: '#webgame',
            ),
            new ProjectDto(
                title: 'Browser Game',
                description: 'A fun and engaging browser-based game built with vanilla JavaScript. Implements game mechanics using the requestAnimationFrame API, features smooth animations, collision detection, and a scoring system. Demonstrates strong understanding of DOM manipulation and event handling.',
                technologies: 'JavaScript, HTML Canvas, CSS Animations',
                image: 'images/projects/webgame.jpg',
                code: 'https://github.com/madh-zealand/tba',
                link: '#webgame',
            )
        ];
    }
}
