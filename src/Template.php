<?php

declare(strict_types=1);

namespace App;

/**
 * Simple template engine for rendering PHP views with layouts and sections.
 *
 * Supports layout inheritance, named sections, and data extraction.
 */
class Template
{
    /**
     * The base path where view files are located.
     */
    protected string $viewPath;

    /**
     * The layout to use for rendering views.
     * Defaults to 'layout' if not overridden.
     */
    protected string $layout = 'layout';

    /**
     * Stores the content of named sections for the current render.
     */
    protected array $sections = [];

    /**
     * The name of the section currently being captured.
     */
    protected string $currentSection = '';

    /**
     * The view to render.
     */
    protected ?string $view = null;

    /**
     * The data to extract for the view.
     */
    protected array $data = [];

    /**
     * The rendered content of the template.
     */
    protected ?string $content = null;

    /**
     * Construct a new Template instance.
     *
     * @param string $viewPath The base path where view files are located.
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = rtrim($viewPath, '/');
    }

    /**
     * Prepare and build the template, storing the rendered result.
     * This does not output anything.
     *
     * @param string $view The view file to render (without extension).
     * @param array $data The data to extract for the view.
     */
    public function build(string $view, array $data = []): void
    {
        $this->view = $view;
        $this->data = $data;
        $this->sections = [];
        $this->layout = 'layout';

        extract($this->data, EXTR_SKIP);

        // Load the view first (view itself defines layout via extend())
        ob_start();
        include "{$this->viewPath}/{$this->view}.view.php";
        $this->sections['content'] = ob_get_clean();

        // Now load the chosen layout (default if not overridden)
        ob_start();
        include "{$this->viewPath}/{$this->layout}.view.php";
        $this->content = ob_get_clean();
    }

    /**
     * Return the built template content.
     *
     * @return string The rendered template output.
     */
    public function render(): string
    {
        if ($this->content === null) {
            throw new \LogicException('Template has not been built. Call build() first.');
        }

        return $this->content;
    }

    /**
     * Specify which layout to use for the current render.
     *
     * @param string $layout The layout file to use (without extension).
     */
    public function extend(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Start capturing output for a named section.
     * If content is provided, sets it directly and ends the section.
     *
     * @param string $section The name of the section to start.
     * @param string|null $content Optional content to set directly.
     */
    public function start(string $section, ?string $content = null): void
    {
        if ($content !== null) {
            $this->sections[$section] = $content;
            return;
        }

        $this->currentSection = $section;
        ob_start();
    }

    /**
     * Stop capturing output for the current section and save it.
     */
    public function end(): void
    {
        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = '';
    }

    /**
     * Output the content of a named section.
     * If the section does not exist, outputs an empty string.
     *
     * @param string $name The name of the section to output.
     */
    public function section(string $name): void
    {
        echo $this->sections[$name] ?? '';
    }
}
