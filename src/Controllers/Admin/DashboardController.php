<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\MessageRepository;

/**
 * Handles the admin dashboard functionality.
 * This controller is protected by authentication.
 */
class DashboardController extends Controller
{
    private MessageRepository $messages;

    public function __construct()
    {
        $this->messages = new MessageRepository();
    }

    /**
     * Show the admin dashboard.
     */
    public function index(Request $request): Response
    {
        if (!$this->isLoggedIn()) {
            return $this->redirectToLoginWithError();
        }

        $unreadMessages = $this->messages->countUnread();

        $response = new Response();
        $response->setTemplate($this->template, 'admin/dashboard', [
            ...$this->pullFlash($response),
            'request' => $request,
            'unreadMessages' => $unreadMessages,
        ]);
        return $response;
    }
}
