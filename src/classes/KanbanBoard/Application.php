<?php

namespace KanbanBoard;

use Michelf\Markdown;

/**
 * Application module
 */
class Application
{

    /**
     * @var
     */
    private $github;

    /**
     * @var
     */
    private $repositories;

    /**
     * @var array|mixed
     */
    private $paused_labels;

    /**
     * @param $github
     * @param $repositories
     * @param array $paused_labels
     */
    public function __construct($github, $repositories, array $paused_labels = [])
    {
        $this->github        = $github;
        $this->repositories  = $repositories;
        $this->paused_labels = $paused_labels;
    }

    /**
     * @return array
     */
    public function board(): array
    {
        $ms         = [];
        $milestones = [];
        foreach ($this->repositories as $repository) {
            foreach ($this->github->milestones($repository) as $data) {
                $ms[$data['title']]               = $data;
                $ms[$data['title']]['repository'] = $repository;
            }
        }
        ksort($ms);
        foreach ($ms as $name => $data) {
            $issues  = $this->issues($data['repository'], $data['number']);
            $percent = self::_percent($data['closed_issues'], $data['open_issues']);
            if ($percent) {
                $milestones[] = [
                    'milestone' => $name,
                    'url'       => $data['html_url'],
                    'progress'  => $percent['percent'],
                    'queued'    => !empty($issues['queued']) ? $issues['queued'] : 0,
                    'active'    => !empty($issues['active']) ? $issues['active'] : 0,
                    'completed' => $percent['complete']
                ];
            }
        }
        return $milestones;
    }

    /**
     * @param $repository
     * @param $milestone_id
     * @return array
     */
    private function issues($repository, $milestone_id): array
    {
        $i = $this->github->issues($repository, $milestone_id);
        foreach ($i as $ii) {
            if (isset($ii['pull_request'])) {
                continue;
            }
            $issues[$ii['state'] === 'closed' ? 'completed' : (($ii['assignee']) ? 'active' : 'queued')][] = [
                'id'        => $ii['id'],
                'number'    => $ii['number'],
                'title'     => $ii['title'],
                'body'      => Markdown::defaultTransform($ii['body']),
                'url'       => $ii['html_url'],
                'assignee'  => (is_array($ii) && array_key_exists('assignee', $ii) && !empty($ii['assignee'])) ? $ii['assignee']['avatar_url'] . '?s=16' : null,
                'paused'    => self::labels_match($ii, $this->paused_labels),
                'progress'  => self::_percent(
                    substr_count(strtolower($ii['body']), '[x]'),
                    substr_count(strtolower($ii['body']), '[ ]')
                ),
                'closed'    => $ii['closed_at']
            ];
        }
        return $issues;
    }

    /**
     * @param $issue
     * @param $needles
     * @return array
     */
    private static function labels_match($issue, $needles): array
    {
        if (Utilities::hasValue($issue, 'labels')) {
            foreach ($issue['labels'] as $label) {
                if (in_array($label['name'], $needles)) {
                    return [$label['name']];
                }
            }
        }
        return [];
    }

    /**
     * @param $complete
     * @param $remaining
     * @return array
     */
    private static function _percent($complete, $remaining): array
    {
        $total = $complete + $remaining;
        if ($total > 0) {
            $percent = ($complete or $remaining) ? round($complete / $total * 100) : 0;
            return [
                'total'     => $total,
                'complete'  => $complete,
                'remaining' => $remaining,
                'percent'   => $percent
            ];
        }
        return [];
    }
}
