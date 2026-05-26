<?php
$file = "d:/PROJECT_GROUP/monitoringsistem/resources/views/admin/maximo/jobcard-pdf.blade.php";
$content = file_get_contents($file);

// 1. Fix "mepet di atas" by adding margin-top to hdr-table
$content = preg_replace("/\.hdr-table\s*\{([^\}]+)\}/i", ".hdr-table { width:100%; padding-bottom:6px; margin-bottom:8px; border-collapse:collapse; margin-top:25px; }", $content);

// 2. Un-nest the JSA table from repeating-header-table
$searchPAGE4 = '<!-- ========== PAGE 4 (JSA) ========== -->
<div class="page">
  <table class="repeating-header-table">
    <thead>
      <tr>
        <td>@include(\'admin.maximo.partials.jobcard-pdf-header\', [\'pageNumber\' => null])</td>
      </tr>
    </thead>
    <tbody>
      <tr class="content-row">
        <td>
        <table style="width:100%; border-collapse:collapse; font-size:9.5px; margin-bottom:6px;">';
        
$replacePAGE4 = '<!-- ========== PAGE 4 (JSA) ========== -->
<div class="page">
  @include(\'admin.maximo.partials.jobcard-pdf-header\', [\'pageNumber\' => null])
  <table style="width:100%; border-collapse:collapse; font-size:9.5px; margin-bottom:6px;">';

$content = str_replace($searchPAGE4, $replacePAGE4, $content);

$searchEndPAGE4 = '            @endfor
          </tbody>
        </table>
        </td>
      </tr>
    </tbody>
  </table>
</div>';

$replaceEndPAGE4 = '            @endfor
          </tbody>
        </table>
</div>';

$content = str_replace($searchEndPAGE4, $replaceEndPAGE4, $content);

// 3. Fix nbsp
$content = str_replace('<td class="jsa-risk-col">{{ $rLine ?? \'&nbsp;\' }}</td>', '<td class="jsa-risk-col">{!! $rLine ?? \'&nbsp;\' !!}</td>', $content);
$content = str_replace('<td class="jsa-prec-col">{{ $pLine ?? \'&nbsp;\' }}</td>', '<td class="jsa-prec-col">{!! $pLine ?? \'&nbsp;\' !!}</td>', $content);

// 4. Fix JSA Column Widths
$searchColumns = '<th style="text-align:center;">No</th>
              <th>Tahapan Kerja</th>
              <th>Risk</th>
              <th>Pengendalian Bahaya (pre caution)</th>
              <th>PIC</th>';
$replaceColumns = '<th style="width:5%; text-align:center;">No</th>
              <th style="width:40%;">Tahapan Kerja</th>
              <th style="width:20%;">Risk</th>
              <th style="width:25%;">Pengendalian Bahaya (pre caution)</th>
              <th style="width:10%;">PIC</th>';
$content = str_replace($searchColumns, $replaceColumns, $content);

file_put_contents($file, $content);
echo "Fixes applied.";
