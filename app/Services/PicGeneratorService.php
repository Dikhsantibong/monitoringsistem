<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Section;

class PicGeneratorService
{
    public function generate($departmentId, $sectionId)
    {
        $department = Department::find($departmentId);
        $section = Section::find($sectionId);

        return trim(($department ? $department->name : '') . ' - ' . ($section ? $section->name : ''));
    }
} 