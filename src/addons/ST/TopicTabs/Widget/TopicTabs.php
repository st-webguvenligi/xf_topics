<?php

namespace ST\TopicTabs\Widget;

use XF\Widget\AbstractWidget;

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
                'since' => time() - ((int) $options['popular_days'] * 86400)
            ],
            'articles' => [
                'title' => 'Makale Paylaşımları',
                'forums' => $this->parseForumIds($options['article_forums']),
                'order' => ['last_post_date', 'DESC']
            ]
        ];

        foreach ($tabs as &$tab)
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

        return $this->renderer('st_topic_tabs_widget', ['tabs' => $tabs]);
    }

    protected function parseForumIds($value)
    {
        if (!$value)
        {
            return [];
        }

        $ids = preg_split('/[,\s]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
        $ids = array_map('intval', $ids);

        return array_values(array_unique(array_filter($ids)));
    }
}
