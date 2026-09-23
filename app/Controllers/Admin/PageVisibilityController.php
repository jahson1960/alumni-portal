<?php

namespace App\Controllers\Admin;

use App\Models\LinkVisibility;
use App\Models\PageTabVisibility;
use App\Models\PageVisibility;

class PageVisibilityController extends AdminController
{
    public function edit(): void
    {
        $tabsByPage = [];
        foreach (PageTabVisibility::allRows() as $row) {
            $tabsByPage[$row['page_key']][] = $row;
        }

        $linksByPage = [];
        foreach (LinkVisibility::allRows() as $row) {
            $linksByPage[$row['page_key']][] = $row;
        }

        $this->view('admin.page_visibility.edit', [
            'title' => 'Page & Menu Visibility',
            'activeNav' => 'page_visibility',
            'pages' => PageVisibility::allRows(),
            'tabsByPage' => $tabsByPage,
            'linksByPage' => $linksByPage,
        ]);
    }

    public function update(): void
    {
        $this->requireCsrf();

        $audiences = (array) $this->input('audience', []);
        $navVisible = array_keys((array) $this->input('show_in_nav', []));
        PageVisibility::setMany($audiences, $navVisible);

        $tabVisible = (array) $this->input('tab_visible', []);
        $visibleComposite = [];
        foreach ($tabVisible as $pageKey => $tabs) {
            foreach (array_keys((array) $tabs) as $tabKey) {
                $visibleComposite[] = $pageKey . '|' . $tabKey;
            }
        }
        PageTabVisibility::setMany($visibleComposite);

        $linkVisible = array_keys((array) $this->input('link_visible', []));
        LinkVisibility::setMany($linkVisible);

        $this->flash('success', 'Page and tab visibility updated.');
        $this->redirect('admin/page-visibility');
    }
}
