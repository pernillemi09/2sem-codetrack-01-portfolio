<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Models\Message;
use App\Repositories\MessageRepository;

/**
 * Handles the admin messages functionality.
 * This controller is protected by authentication.
 */
class MessagesController extends Controller
{
    private MessageRepository $messages;

    public function __construct()
    {
        $this->messages = new MessageRepository();
    }

    /**
     * Show the admin messages page.
     */
    public function index(Request $request): Response
    {
        if (!$this->isLoggedIn()) {
            return $this->redirectToLoginWithError();
        }

        $messages = $this->messages->findAll();
        $count = $this->messages->count();
        $countUnread = $this->messages->countUnread();

        $response = new Response();
        $response->setTemplate($this->template, 'admin/messages', [
            ...$this->pullFlash($response),
            'request' => $request,
            'messages' => $messages,
            'count' => $count,
            'countUnread' => $countUnread,
        ]);
        return $response;
    }

    /**
     * Mark a message as read/unread.
     */
    public function toggleRead(Request $request, string $id): Response
    {
        if (!$this->isLoggedIn()) {
            return $this->redirectToLoginWithError();
        }

        $response = new Response();

        // Check CSRF token
        if (!$request->validateCsrfToken()) {
            return $this->handleInvalidRequest(
                $response,
                'Invalid security token',
                $request->getAll(),
            );
        }

        $message = $this->messages->find((int) $id);
        if (!$message) {
            return $this->handleInvalidRequest(
                $response,
                'Could not find message',
                $request->getAll(),
            );
        }

        $this->messages->updateReadStatus(
            $message->getId(),
            !$message->getIsRead(),
        );

        $response->redirect('/admin/messages');
        return $response;
    }

    /**
     * Delete a message.
     */
    public function delete(Request $request, string $id): Response
    {
        if (!$this->isLoggedIn()) {
            return $this->redirectToLoginWithError();
        }

        $response = new Response();

        // Check CSRF token
        if (!$request->validateCsrfToken()) {
            return $this->handleInvalidRequest(
                $response,
                'Invalid security token',
                $request->getAll(),
            );
        }

        $message = $this->messages->find((int) $id);
        if (!$message) {
            return $this->handleInvalidRequest(
                $response,
                'Could not find message',
                $request->getAll(),
            );
        }

        $this->messages->delete(
            $message->getId(),
        );

        $response->redirect('/admin/messages');
        return $response;
    }
}
