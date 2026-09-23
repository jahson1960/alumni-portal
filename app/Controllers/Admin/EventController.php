<?php

namespace App\Controllers\Admin;

use App\Models\Event;

class EventController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.events.index', [
            'title' => 'Events',
            'activeNav' => 'events',
            'events' => Event::all('event_date DESC'),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.events.form', [
            'title' => 'New Event',
            'activeNav' => 'events',
            'event' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            $this->flash('error', 'Event not found.');
            $this->redirect('admin/events');
        }

        $this->view('admin.events.form', [
            'title' => 'Edit Event',
            'activeNav' => 'events',
            'event' => $event,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Event::delete((int) $id);
        $this->flash('success', 'Event deleted.');
        $this->redirect('admin/events');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $description = trim((string) $this->input('description', ''));
        $image = trim((string) $this->input('image', ''));
        $location = trim((string) $this->input('location', ''));
        $eventDate = trim((string) $this->input('event_date', ''));
        $eventTime = trim((string) $this->input('event_time', ''));
        $isVirtual = $this->input('is_virtual') ? 1 : 0;
        $category = in_array($this->input('category'), \App\Models\Event::CATEGORIES, true) ? $this->input('category') : null;
        $isFeatured = $this->input('is_featured') ? 1 : 0;

        $errors = [];
        if ($title === '') {
            $errors[] = 'Event title is required.';
        }
        if ($eventDate === '' || !strtotime($eventDate)) {
            $errors[] = 'A valid event date is required.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/events/{$id}/edit" : 'admin/events/create');
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'image' => $image !== '' ? $image : null,
            'location' => $location,
            'event_date' => date('Y-m-d', strtotime($eventDate)),
            'event_time' => $eventTime,
            'is_virtual' => $isVirtual,
            'category' => $category,
            'is_featured' => $isFeatured,
        ];

        if ($id === null) {
            Event::create($data);
            $this->flash('success', 'Event created.');
        } else {
            Event::update($id, $data);
            $this->flash('success', 'Event updated.');
        }

        $this->redirect('admin/events');
    }
}
