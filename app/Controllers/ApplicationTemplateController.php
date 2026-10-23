<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\ApplicationTemplate;
use App\Models\ApplicationTemplateField;

/**
 * Reusable in-app job-application form templates. Any logged-in user who can post a job
 * (alumni self-posters and admins alike) can build one here and attach it to a job post
 * via Apply Method -> "In-App Form", so the same set of questions doesn't need rebuilding
 * for every new listing.
 */
class ApplicationTemplateController extends Controller
{
    public function index(): void
    {
        $this->guard();
        $templates = ApplicationTemplate::forUser((int) Auth::id());
        foreach ($templates as &$template) {
            $template['field_count'] = count(ApplicationTemplateField::forTemplate((int) $template['id']));
        }
        unset($template);

        $this->view('application_templates.index', [
            'title' => 'Application Templates',
            'activeNav' => 'jobs',
            'templates' => $templates,
        ]);
    }

    public function create(): void
    {
        $this->guard();
        $this->view('application_templates.form', [
            'title' => 'New Application Template',
            'activeNav' => 'jobs',
            'template' => null,
            'fields' => [],
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();
        $this->guard();
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $this->guard();
        $template = ApplicationTemplate::findForUser((int) $id, (int) Auth::id());
        if (!$template) {
            $this->flash('error', 'Template not found.');
            $this->redirect('application-templates');
        }

        $this->view('application_templates.form', [
            'title' => 'Edit Application Template',
            'activeNav' => 'jobs',
            'template' => $template,
            'fields' => ApplicationTemplateField::forTemplate((int) $id),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();
        $this->guard();
        $template = ApplicationTemplate::findForUser((int) $id, (int) Auth::id());
        if (!$template) {
            $this->flash('error', 'Template not found.');
            $this->redirect('application-templates');
        }
        $this->save((int) $id);
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        $this->guard();
        $template = ApplicationTemplate::findForUser((int) $id, (int) Auth::id());
        if ($template) {
            ApplicationTemplate::delete((int) $id);
            $this->flash('success', 'Template deleted. Any job post still using it falls back to its own description for how to apply.');
        }
        $this->redirect('application-templates');
    }

    private function save(?int $id): void
    {
        $name = trim((string) $this->input('name', ''));
        $labels = (array) $this->input('field_label', []);
        $types = (array) $this->input('field_type', []);
        $options = (array) $this->input('field_options', []);
        $required = (array) $this->input('field_required', []);

        if ($name === '') {
            $_SESSION['_errors'] = ['Template name is required.'];
            $this->redirect($id ? "application-templates/{$id}/edit" : 'application-templates/create');
        }

        $fields = [];
        foreach ($labels as $i => $label) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }
            $type = in_array($types[$i] ?? '', ApplicationTemplate::FIELD_TYPES, true) ? $types[$i] : 'text';
            $fields[] = [
                'label' => $label,
                'field_type' => $type,
                'options' => $type === 'select' ? trim((string) ($options[$i] ?? '')) : null,
                'is_required' => ($required[$i] ?? '0') === '1',
            ];
        }

        if ($id === null) {
            $id = ApplicationTemplate::create(['user_id' => Auth::id(), 'name' => $name]);
            $this->flash('success', 'Template created.');
        } else {
            ApplicationTemplate::rename($id, $name);
            $this->flash('success', 'Template updated.');
        }

        ApplicationTemplateField::replaceForTemplate($id, $fields);
        $this->redirect('application-templates');
    }

    private function guard(): void
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }
}
