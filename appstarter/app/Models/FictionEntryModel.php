<?php

namespace App\Models;

use CodeIgniter\Model;

class FictionEntryModel extends Model
{
    protected $table = 'fiction_entry';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'fiction_title_id',
        'parent_entry_id',
        'entry_position',
        'entry_title',
        'entry_type',
        'entry_note',
        'entry_short_note',
        'entry_status',
        'entry_content',
        'footnote_section',
        'word_count',
        'char_count',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 727;

    private array $configurations = [
        'id'               => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'parent_entry_id'  => [
            'type'     => 'select',
            'label'    => 'Parent Entry',
            'required' => true,
            'options'  => []
        ],
        'entry_position'   => [
            'type'     => 'text',
            'label'    => 'Position #',
            'required' => true
        ],
        'entry_title'      => [
            'type'      => 'text',
            'label'     => 'Entry Title',
            'required'  => true,
            'maxlength' => 128
        ],
        'entry_type'       => [
            'type'    => 'select',
            'label'   => 'Entry Type',
            'options' => [
                'front-matter' => 'Front Matter',
                'chapter'      => 'Chapter',
                'scene'        => 'Scene',
                'folder'       => 'Folder',
                'research'     => 'Research'
            ]
        ],
        'entry_note'       => [
            'type'     => 'text',
            'label'    => 'Entry Note',
            'required' => false,
        ],
        'entry_short_note' => [
            'type'     => 'text',
            'label'    => 'Entry Short Note',
            'required' => false,
        ],
        'entry_content'    => [
            'type'     => 'tinymce',
            'label'    => 'Entry Content <span id="entry-content-word-count"></span> <span id="autosave-label" class="badge bg-danger d-none">AUTOSAVED</span>',
            'required' => true,
        ],
        'entry_status'     => [
            'type'     => 'select',
            'label'    => 'Entry Status',
            'required' => true,
            'options'  => [
                'in_progress'   => 'In Progress',
                'revised_draft' => 'Revised Draft',
                'final_draft'   => 'Final Draft',
            ]
        ],
        'footnote_section' => [
            'type'     => 'textarea',
            'label'    => 'Footnote Section',
            'required' => false,
        ],
    ];

    /**
     * Get configurations for generating forms
     * @param int $parent_id
     * @return array
     */
    public function getConfigurations(int $parent_id = 0): array
    {
        $configurations  = $this->configurations;
        // parent_entry_id
        $raw_options     = $this->where('fiction_title_id', $parent_id)->where('parent_entry_id IS NULL')->findAll();
        $options         = [];
        foreach ($raw_options as $option) {
            $options[$option['id']] = $option['entry_title'];
        }
        $configurations['parent_entry_id']['options'] = $options;
        return $configurations;
    }

    /**
     * @param string $status
     * @return array|string
     */
    public function getEntryStatus(string $status = ''): array|string
    {
        $statuses = [
            'in_progress'   => '<i class="fa-solid fa-circle text-success"></i> In Progress',
            'revised_draft' => '<i class="fa-solid fa-circle text-warning"></i> Revised Draft',
            'final_draft'   => '<i class="fa-solid fa-circle text-primary"></i> Final Draft',
        ];
        return $statuses[$status] ?? $statuses;
    }

    /**
     * @param string $type
     * @return array|string
     */
    public function getEntryType(string $type = ''): array|string
    {
        $types = [
            'front-matter' => '<i class="fa-solid fa-book"></i> Front Matter',
            'chapter'      => '<i class="fa-solid fa-book-open"></i> Chapter',
            'scene'        => '<i class="fa-solid fa-file-lines"></i> Scene',
            'folder'       => '<i class="fa-solid fa-folder-open"></i> Folder',
            'research'     => '<i class="fa-solid fa-folder-tree"></i> Research'
        ];
        return $types[$type] ?? $types;
    }

    /**
     * @param int $title_id
     * @param bool $exclude_content
     * @param array $entry_types
     * @return array
     */
    public function getEntriesOfTitle(int $title_id, bool $exclude_content = true, array $entry_types = []): array
    {
        if (!empty($entry_types)) {
            $this->whereIn('entry_type', $entry_types);
        }
        $entries   = $this->where('fiction_title_id', $title_id)->orderBy('entry_position ASC')->findAll();
        $parents   = [];
        $children  = [];
        $structure = [];
        $word_cnt  = 0;
        $char_cnt  = 0;
        foreach ($entries as $entry) {
            if ($exclude_content) {
                unset($entry['entry_content']);
            }
            if (!empty($entry['parent_entry_id'])) {
                $children[$entry['id']] = $entry;
            } else {
                $parents[$entry['id']] = $entry;
            }
        }
        foreach ($parents as $parent) {
            $structure[$parent['entry_position'] . '.00'] = $parent;
        }
        foreach ($children as $child) {
            $position = $parents[$child['parent_entry_id']]['entry_position'] . '.' . sprintf('%02d', $child['entry_position']);
            $structure[$position] = $child;
            if ('scene' == $child['entry_type']) {
                $word_cnt += $child['word_count'];
                $char_cnt += $child['char_count'];
            }
        }
        ksort($structure);
        $word_cnt = round($word_cnt/100)*100;
        $char_cnt = round($char_cnt/100)*100;
        return [
            'entries'    => $structure,
            'word_count' => $word_cnt,
            'char_count' => $char_cnt
        ];
    }
}