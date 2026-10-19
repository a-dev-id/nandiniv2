<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 25)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        return view('pages.faq', [
            'page' => $page,
            'sections' => $sections,
            'faqGroups' => $this->faqGroups($sections),
        ]);
    }

    /** Parse the existing CMS headings and answers without duplicating their content. */
    private function faqGroups(Collection $sections): array
    {
        $groups = [];

        foreach ($sections->where('section_key', 'intro_text_section') as $section) {
            $document = new \DOMDocument();
            $previousErrors = libxml_use_internal_errors(true);
            try {
                $document->loadHTML('<?xml encoding="UTF-8"><body>'.($section->description ?? '').'</body>');
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previousErrors);
            }

            $group = null;
            $question = null;
            foreach ($document->getElementsByTagName('body')->item(0)?->childNodes ?? [] as $node) {
                if ($node->nodeName === 'h2') {
                    $groups[] = [
                        'title' => trim($node->textContent),
                        'id' => 'faq-'.\Illuminate\Support\Str::slug($node->textContent),
                        'section_id' => $section->id,
                        'items' => [],
                    ];
                    $group = array_key_last($groups);
                    $question = null;
                } elseif ($node->nodeName === 'h3' && $group !== null) {
                    $groups[$group]['items'][] = ['question' => trim($node->textContent), 'answer' => ''];
                    $question = array_key_last($groups[$group]['items']);
                } elseif ($group !== null && $question !== null) {
                    $groups[$group]['items'][$question]['answer'] .= $document->saveHTML($node);
                }
            }
        }

        return array_values(array_filter($groups, fn (array $group): bool => $group['items'] !== []));
    }

    private function getPageSections(Page $page): Collection
    {
        return $page->sections()
            ->where('is_active', true)
            ->with([
                'images' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();
    }
}
