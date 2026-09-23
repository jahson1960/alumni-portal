<?php

namespace App\Controllers\Admin;

use App\Core\Upload;
use App\Models\HeroSlide;

class HeroSlideController extends AdminController
{
    private const ALIGNMENTS = ['left', 'center', 'right'];

    public function index(): void
    {
        $this->view('admin.hero_slides.index', [
            'title' => 'Homepage Hero Slides',
            'activeNav' => 'hero_slides',
            'slides' => HeroSlide::allOrdered(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.hero_slides.form', [
            'title' => 'New Hero Slide',
            'activeNav' => 'hero_slides',
            'slide' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $slide = HeroSlide::find((int) $id);
        if (!$slide) {
            $this->flash('error', 'Slide not found.');
            $this->redirect('admin/hero-slides');
        }

        $this->view('admin.hero_slides.form', [
            'title' => 'Edit Hero Slide',
            'activeNav' => 'hero_slides',
            'slide' => $slide,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        HeroSlide::delete((int) $id);
        $this->flash('success', 'Slide deleted.');
        $this->redirect('admin/hero-slides');
    }

    public function toggle(string $id): void
    {
        $this->requireCsrf();
        HeroSlide::toggleEnabled((int) $id);
        $this->flash('success', 'Slide visibility updated.');
        $this->redirect('admin/hero-slides');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $highlight = trim((string) $this->input('highlight', ''));
        $subtitle = trim((string) $this->input('subtitle', ''));
        $description = trim((string) $this->input('description', ''));
        $align = in_array($this->input('content_align'), self::ALIGNMENTS, true) ? $this->input('content_align') : 'left';
        $enabled = $this->input('enabled') ? 1 : 0;
        $sortOrder = (int) $this->input('sort_order', 0);

        $btn1Text = trim((string) $this->input('btn1_text', ''));
        $btn1Show = $this->input('btn1_show') ? 1 : 0;
        $btn1Link = trim((string) $this->input('btn1_link', ''));
        $btn2Text = trim((string) $this->input('btn2_text', ''));
        $btn2Show = $this->input('btn2_show') ? 1 : 0;
        $btn2Link = trim((string) $this->input('btn2_link', ''));

        $errors = [];
        $image = null;
        try {
            $uploaded = Upload::image($this->file('image'), 'hero');
            if ($uploaded) {
                $image = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/hero-slides/{$id}/edit" : 'admin/hero-slides/create');
        }

        $data = [
            'title' => $title !== '' ? $title : null,
            'highlight' => $highlight !== '' ? $highlight : null,
            'subtitle' => $subtitle !== '' ? $subtitle : null,
            'description' => $description !== '' ? $description : null,
            'content_align' => $align,
            'enabled' => $enabled,
            'sort_order' => $sortOrder,
            'btn1_text' => $btn1Text !== '' ? $btn1Text : null,
            'btn1_show' => $btn1Show,
            'btn1_link' => $btn1Link !== '' ? $btn1Link : null,
            'btn2_text' => $btn2Text !== '' ? $btn2Text : null,
            'btn2_show' => $btn2Show,
            'btn2_link' => $btn2Link !== '' ? $btn2Link : null,
        ];
        if ($image) {
            $data['image'] = $image;
        }

        if ($id === null) {
            if (!$image) {
                $_SESSION['_errors'] = ['Please upload a background image for the new slide.'];
                $this->redirect('admin/hero-slides/create');
            }
            $data['sort_order'] = $sortOrder ?: HeroSlide::nextSortOrder();
            HeroSlide::create($data);
            $this->flash('success', 'Slide added.');
        } else {
            HeroSlide::update($id, $data);
            $this->flash('success', 'Slide updated.');
        }

        $this->redirect('admin/hero-slides');
    }
}
