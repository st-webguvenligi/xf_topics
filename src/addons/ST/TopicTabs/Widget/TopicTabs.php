<?php

namespace ST\TopicTabs\Widget;

use XF\Widget\AbstractWidget;
use XF\Widget\WidgetRenderer;

class TopicTabs extends AbstractWidget
{
    protected $defaultOptions = [
        'announcement_forums' => '',
        'latest_forums' => '',
        'popular_forums' => '',
        'article_forums' => '',
        'limit' => 5,
        'popular_days' => 30
    ];

    public function render()
    {
        $options = $this->options + $this->defaultOptions;
        $tabs = [
            'announcements' => [
                'title' => 'Duyurular',
                'forums' => $this->parseForumIds($options['announcement_forums']),
                'order' => ['post_date', 'DESC']
            ],
            'latest' => [
                'title' => 'Son Konular',
                'forums' => $this->parseForumIds($options['latest_forums']),
                'order' => ['last_post_date', 'DESC']
            ],
            'popular' => [
                'title' => 'Popüler Konular',
                'forums' => $this->parseForumIds($options['popular_forums']),
                'order' => ['view_count', 'DESC'],
                'since' => (time() - ((int) $options['popular_days'] * 86400))
            ],
            'articles' => [
                'title' => 'Makale Paylaşımları',
                'forums' => $this->parseForumIds($options['article_forums']),
                'order' => ['last_post_date', 'DESC']
            ]
        ];

        foreach ($tabs as $tabId => &$tab)
        {
            $finder = \XF::finder('XF:Thread')
                ->with('LastPoster')
                ->where('discussion_state', 'visible')
                ->order($tab['order'][0], $tab['order'][1])
                ->limit(max(1, (int) $options['limit']));

            if ($tab['forums'])
            {
                $finder->where('node_id', $tab['forums']);
            }

            if (!empty($tab['since']))
            {
                $finder->where('post_date', '>=', $tab['since']);
            }

            $tab['threads'] = $finder->fetch();
        }
        unset($tab);

        return $this->renderer('st_topic_tabs_widget', [
            'tabs' => $tabs
        ]);
    }

    protected function parseForumIds($value)
    {
        if (!$value)
        {
            return [];
        }

        $ids = array_map('intval', preg_split('/[,\s]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY));
        return array_values(array_unique(array_filter($ids)));
    }

    public function verifyOptions(array &$options, \XF\Admin\Controller\AbstractWidget $controller)
    {
        foreach (['announcement_forums', 'latest_forums', 'popular_forums', 'article_forums'] as $key)
        {
            if (!isset($options[$key]))
            {
                $options[$key] = '';
            }
        }

        $options['limit'] = max(1, min(50, (int) ($options['limit'] ?? 5)));
        $options['popular_days'] = max(1, min(3650, (int) ($options['popular_days'] ?? 30)));

        return true;
    }

    public function renderOptions(\XF\Admin\Controller\AbstractWidget $controller, \XF\Entity\Widget $widget)
    {
        return $controller->formRow(
            $controller->formTextBox($widget->options['announcement_forums'] ?? '', 'options[announcement_forums]'),
            ['label' => 'Duyurular forum ID\'leri']
        )
        . $controller->formRow(
            $controller->formTextBox($widget->options['latest_forums'] ?? '', 'options[latest_forums]'),
            ['label' => 'Son Konular forum ID\'leri']
        )
        . $controller->formRow(
            $controller->formTextBox($widget->options['popular_forums'] ?? '', 'options[popular_forums]'),
            ['label' => 'Popüler Konular forum ID\'leri']
        )
        . $controller->formRow(
            $controller->formTextBox($widget->options['article_forums'] ?? '', 'options[article_forums]'),
            ['label' => 'Makale Paylaşımları forum ID\'leri']
        )
        . $controller->formRow(
            $controller->formNumberBox($widget->options['limit'] ?? 5, 'options[limit]', ['min' => 1, 'max' => 50]),
            ['label' => 'Konu sayısı']
        )
        . $controller->formRow(
            $controller->formNumberBox($widget->options['popular_days'] ?? 30, 'options[popular_days]', ['min' => 1, 'max' => 3650]),
            ['label' => 'Popülerlik dönemi (gün)']
        );
    }
}
