<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Card - {{ $wo['wonum'] ?? '-' }} - PLN Nusantara Power</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
@page { 
    margin: 10mm 15mm; 
    size: A4; 
}
body { 
    font-family: Arial, sans-serif; 
    font-size:11px; 
    color:#000; 
    background:#fff; 
}
table th, table td { line-height:1.35; overflow-wrap:break-word; word-wrap:break-word; }

.page-number:after {
    content: counter(page);
}

.page {
    width: 180mm;
    margin: 0;
    position: relative; 
    overflow: visible;
    page-break-inside: auto;
    page-break-after: always; 
}
.page:last-child {
    page-break-after: auto;
}

.repeating-header-table {
    width: 100%;
    border-collapse: collapse;
    page-break-inside: auto;
}
.repeating-header-table thead {
    display: table-header-group;
}
.repeating-header-table tbody tr > td {
    vertical-align: top;
    padding: 0;
}
.repeating-header-table tbody tr {
    page-break-inside: auto;
    page-break-after: auto;
}
.content-row {
    page-break-inside: auto;
    page-break-after: auto;
}
.keep-with-next {
    page-break-after: avoid;
}

/* HEADER */
.hdr-table { width:100%; border-bottom:2px solid #000; padding-bottom:6px; margin-bottom:8px; border-collapse:collapse; }
.co { font-size:14px; font-weight:bold; }
.unit { font-size:11px; }

/* JOB CARD TITLE */
.jc-title { font-size:16px; font-weight:bold; margin:6px 0 6px 0; }

/* HR dividers */
.hr1 { border:none; border-top:1px solid #000; margin:5px 0; }
.hr2 { border:none; border-top:2px solid #000; margin:5px 0; }

.bold { font-weight:bold; }
.f10 { font-size:10px; }
.f9 { font-size:9.5px; }

.sr-section { margin-bottom:4px; }
.detil-section { font-size:10px; line-height:1.65; margin-bottom:4px; }
.detil-section p { margin-bottom:1px; }

.task-section {
    margin-top:6px;
    page-break-inside:auto;
    break-inside:auto;
}
.task-block-head {
    page-break-inside:avoid;
    break-inside:avoid;
}
.task-id { font-weight:bold; font-size:12px; margin:5px 0 5px 0; }
.task-nm { font-size:13px; font-weight:bold; font-style:italic; margin-top:3px; }
.task-ld {
    margin-top:8px;
    font-size:9.5px;
    padding-left:15px;
    line-height:1.45;
}
.task-ld-chunk {
    font-size:9.5px;
    padding-left:15px;
    line-height:1.45;
    page-break-inside:avoid;
    break-inside:avoid;
    margin-top:4px;
}
.jobcard-end-section { margin-top:12px; }

.step-t { font-weight:bold; margin:6px 0 2px; font-size:10px; }
ol { margin-left:16px; font-size:10px; line-height:1.7; }

/* TABLES */
.blue-table { width:100%; border-collapse:collapse; font-size:10px; margin:6px 0; table-layout:fixed; border:1px solid #000; page-break-inside:auto; }
.blue-table thead { display:table-header-group; }
.blue-table tr { page-break-inside:auto; page-break-after:auto; }
.blue-table th, .blue-table td { border:1px solid #000; padding:4px 6px; vertical-align:top; text-align:left; }
.blue-table th { background:#4472C4; color:#fff; font-weight:bold; }
.blue-table .blue-table-title th { text-align:center; }

.sig-lbl { font-size:10px; margin-bottom:5px; }
.fr-title { font-weight:bold; font-size:10px; margin-bottom:6px; }

/* JSA */
.jsa-tbl { width:100%; border-collapse:collapse; font-size:9.5px; table-layout:fixed; border:1px solid #000; page-break-inside:auto; }
.jsa-tbl thead { display:table-header-group; }
.jsa-tbl tr { page-break-inside:auto; page-break-after:auto; }
.jsa-tbl th, .jsa-tbl td { border:1px solid #000; padding:5px 6px; vertical-align:top; word-wrap:break-word; }
.jsa-tbl th { background:#4472C4; color:#fff; font-weight:bold; }
.jsa-tbl .jsa-tahapan { font-size:9.5px; line-height:1.45; padding:6px 8px 6px 15px; }
.jsa-tbl .jsa-risk-col, .jsa-tbl .jsa-prec-col { font-size:8.5px; line-height:1.45; padding:6px 8px; }
.jsa-tbl .jsa-empty { color:#666; text-align:center; }

/* PERBAIKAN: kolom No dipersempit */
.jsa-col-no      { width:3%; }
.jsa-col-tahapan { width:45%; }
.jsa-col-risk    { width:16%; }
.jsa-col-prec    { width:30%; }
.jsa-col-pic     { width:6%; }

.chk { width:12px; height:12px; border:1px solid #000; display:inline-block; }

.w-tbl { width:100%; border-collapse:collapse; font-size:9.5px; }
.w-tbl th, .w-tbl td { border:1px solid #000; padding:4px 6px; }
.w-tbl th { background:#ddd; font-weight:bold; }
.w-tbl td { height:20px; }
</style>
</head>
<body>

<!-- ========== PAGE 1 ========== -->
<div class="page">
  <table class="repeating-header-table">
    <thead>
      <tr>
        <td>@include('admin.maximo.partials.jobcard-pdf-header', ['pageNumber' => null])</td>
      </tr>
    </thead>
    <tbody>
      <tr class="content-row keep-with-next">
        <td>
        <div class="jc-title">JOB CARD</div>
        <hr class="hr1">

        <table style="width:100%; font-size:10px; margin-top:4px;">
          <tr>
            <td style="width:150px;"><b>No. WO :</b> {{ $wo['wonum'] ?? '-' }}</td>
            <td>[{{ $wo['description'] ?? '-' }}]</td>
          </tr>
        </table>
        <div class="f10" style="margin-bottom:6px;"><b>Job Plan :</b> {{ $wo['jpnum'] ?? '-' }}</div>

        <hr class="hr1">
        </td>
      </tr>

        @if($sr)
      <tr class="content-row">
        <td>
        <div class="sr-section">
          <div class="bold f10" style="margin-bottom:3px;">Service Request Information</div>
          <table style="width:100%; font-size:10px; margin-bottom:3px;">
            <tr>
              <td style="width:60px; font-weight:bold;">No. SR :</td>
              <td style="width:80px;">{{ $sr['ticketid'] ?? '-' }}</td>
              <td>[{{ $sr['description'] ?? '-' }}]</td>
              <td style="width:80px; font-weight:bold;">Reported By :</td>
              <td style="width:200px;">{{ $sr['reportedby'] ?? '-' }} &nbsp;&nbsp; {{ $sr['reportedby_name'] ?? '-' }}</td>
            </tr>
          </table>
        </div>

        @if(isset($sr['longdescription']) && $sr['longdescription'] != '-' && !empty(trim($sr['longdescription'])))
        <div class="detil-section" style="margin-top:6px;">
          <div class="bold" style="margin-bottom:3px;">Detil SR</div>
          @php
              $text = str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $sr['longdescription']);
              $text = strip_tags($text);
              $text = preg_replace("/[\r\n]+/", "\n\n", $text);
          @endphp
          {!! nl2br(e(trim($text))) !!}
        </div>
        @endif
        </td>
      </tr>
        @endif

        @if(isset($tasks) && count($tasks) > 0)
          @foreach($tasks as $task)
      <tr class="content-row keep-with-next">
        <td>
          <div class="task-section">
            <div class="task-block-head">
            <hr class="hr1">
            <div class="task-id" style="margin-top:4px;">Task : <b>{{ $task['wonum'] ?? '-' }}</b></div>

            <table style="width:100%; font-size:9.5px; margin-bottom:5px; border-collapse:collapse;">
              <tr>
                <td style="width:33%; padding:1px 0;"><b>Site :</b> {{ $task['siteid'] ?? '-' }}</td>
                <td style="width:33%; padding:1px 0;"><b>Sched Start :</b> {{ $task['schedstart'] ?? '-' }}</td>
                <td style="width:34%; padding:1px 0;"><b>Sched Finish :</b> {{ $task['schedfinish'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:1px 0;"><b>Status :</b> {{ $task['status'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>Target Start :</b> {{ $task['targstartdate'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>Target Finish :</b> {{ $task['targcompdate'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:1px 0;"><b>Parent :</b> {{ $task['parent'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>Actual Start :</b> {{ $task['actstart'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>Actual Finish :</b> {{ $task['actfinish'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:1px 0;"><b>Work Type :</b> {{ $task['worktype'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>Report Date :</b> {{ $task['reportdate'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>Reported By :</b> {{ $task['reportedby'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:1px 0;"><b>Assign :</b> {{ $task['assigned_to'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>Failure Class :</b> {{ $task['failurecode'] ?? '-' }}</td>
                <td style="padding:1px 0;"><b>GL Account :</b> {{ $task['glaccount'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:1px 0;"><b>Priority :</b> {{ $task['wopriority'] ?? '-' }}</td>
                <td colspan="2" style="padding:1px 0;"><b>Person Group :</b> {{ $task['persongroup'] ?? '-' }}</td>
              </tr>
            </table>

            <table style="width:100%; font-size:9.5px; margin-bottom:5px; border-collapse:collapse;">
              <tr>
                <td style="width:65px; font-weight:bold; padding:1px 0;">Asset :</td>
                <td style="padding:1px 0;">{{ $task['assetnum'] ?? '-' }} &nbsp;&nbsp; {{ $task['asset_description'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="width:65px; font-weight:bold; padding:1px 0;">Location :</td>
                <td style="padding:1px 0;">{{ $task['location'] ?? '-' }} &nbsp;&nbsp; {{ $task['location_description'] ?? '-' }}</td>
              </tr>
            </table>

            <div class="task-nm">Task : {{ $task['description'] ?? '-' }}</div>
            </div>
          </div>
        </td>
      </tr>

            @if(isset($task['longdescription']) && $task['longdescription'] != '-' && !empty(trim($task['longdescription'])))
            @php
                $taskLd = str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $task['longdescription']);
                $taskLd = strip_tags($taskLd);
                $taskLd = preg_replace("/[ \t]+/", ' ', $taskLd);
                $taskLd = preg_replace("/\n{3,}/", "\n\n", trim($taskLd));
            @endphp
            @foreach(array_chunk(preg_split("/\n{2,}/", $taskLd), 4) as $chunk)
      <tr class="content-row">
        <td>
            <div class="task-ld-chunk">{!! nl2br(e(implode("\n\n", array_filter($chunk, fn($line) => trim($line) !== '')))) !!}</div>
        </td>
      </tr>
            @endforeach
            @elseif(isset($wo['longdescription']) && $wo['longdescription'] != '-' && !empty(trim($wo['longdescription'])))
            @php
                $taskLd = str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $wo['longdescription']);
                $taskLd = strip_tags($taskLd);
                $taskLd = preg_replace("/[ \t]+/", ' ', $taskLd);
                $taskLd = preg_replace("/\n{3,}/", "\n\n", trim($taskLd));
            @endphp
            @foreach(array_chunk(preg_split("/\n{2,}/", $taskLd), 4) as $chunk)
      <tr class="content-row">
        <td>
            <div class="task-ld-chunk">{!! nl2br(e(implode("\n\n", array_filter($chunk, fn($line) => trim($line) !== '')))) !!}</div>
        </td>
      </tr>
            @endforeach
            @endif
          @endforeach
        @endif

      <tr class="content-row">
        <td>
        <div class="jobcard-end-section">
          <table class="blue-table">
            <thead>
            <tr class="blue-table-title"><th colspan="8">Planned &amp; Actual Labor</th></tr>
            <tr>
              <th>Task ID</th><th>Craft</th><th>Skill Level</th><th>Labor</th>
              <th>Planned Quantity</th><th>Planned Hours</th><th>Actual Quantity</th><th>Actual Hours</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($wplabors) && count($wplabors) > 0)
              @foreach($wplabors as $wpl)
              <tr>
                <td>{{ $wpl['wonum'] }}</td>
                <td>{{ $wpl['craft'] }}</td>
                <td>{{ $wpl['skilllevel'] }}</td>
                <td>{{ $wpl['labor'] }}</td>
                <td>{{ $wpl['quantity'] }}</td>
                <td>{{ $wpl['laborhrs'] }}</td>
                <td></td>
                <td></td>
              </tr>
              @endforeach
            @else
              <tr><td>{{ $wo['wonum'] }}</td><td>MECH1</td><td>JUNIOR</td><td></td><td>4</td><td>5</td><td></td><td></td></tr>
            @endif
            </tbody>
          </table>

          @include('admin.maximo.partials.jobcard-hazard-table', ['hazards' => $hazards ?? []])
        </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<!-- ========== PAGE 2 ========== -->
<div class="page">
  @include('admin.maximo.partials.jobcard-pdf-header', ['pageNumber' => 2])

        <div class="step-t" style="margin-top:8px;">&nbsp;REALISASI PEKERJAAN :</div>
        <table style="width:100%; font-size:10px; margin-top:4px; margin-bottom:12px;">
          <tr><td style="width:20px; vertical-align:bottom;">1.</td><td style="border-bottom:1px dotted #888;"></td></tr>
          <tr><td colspan="2" style="height:12px;"></td></tr>
          <tr><td style="vertical-align:bottom;">2.</td><td style="border-bottom:1px dotted #888;"></td></tr>
          <tr><td colspan="2" style="height:12px;"></td></tr>
          <tr><td style="vertical-align:bottom;">3.</td><td style="border-bottom:1px dotted #888;"></td></tr>
          <tr><td colspan="2" style="height:12px;"></td></tr>
          <tr><td style="vertical-align:bottom;">4.</td><td style="border-bottom:1px dotted #888;"></td></tr>
        </table>

        <hr class="hr1" style="margin-top:10px;">
        <div style="font-weight:bold; font-style:italic; font-size:10px; margin-top:5px;"><em>Isolasi dan Perhatian Keselamatan Kerja</em></div>
        <div style="border-bottom:1px dotted #888; margin-top:18px;"></div>
        <div style="border-bottom:1px dotted #888; margin-top:18px;"></div>
</div>

<!-- ========== PAGE 3 ========== -->
<div class="page">
  @include('admin.maximo.partials.jobcard-pdf-header', ['pageNumber' => 3])

        <div class="sig-lbl">Diminta Oleh</div>
        <table style="width:200px; margin-left:20px; margin-bottom:30px; text-align:center;">
          <tr><td style="padding-top:28px; border-bottom:1px dotted #555;"></td></tr>
          <tr><td style="font-size:10px; font-style:italic; padding-top:3px;">Supervisor Pemeliharaan</td></tr>
        </table>

        <div class="sig-lbl">Verifikasi</div>
        <table style="width:450px; margin-left:20px; margin-bottom:30px; text-align:center;">
          <tr>
            <td style="width:200px; padding-top:28px; border-bottom:1px dotted #555;"></td>
            <td style="width:50px;"></td>
            <td style="width:200px; padding-top:28px; border-bottom:1px dotted #555;"></td>
          </tr>
          <tr>
            <td style="font-size:10px; font-style:italic; padding-top:3px;">Supervisor Operasi</td>
            <td></td>
            <td style="font-size:10px; font-style:italic; padding-top:3px;">Pelepasan Sistem</td>
          </tr>
        </table>

        <div class="sig-lbl">Verifikasi</div>
        <table style="width:200px; margin-left:20px; margin-bottom:20px; text-align:center;">
          <tr><td style="padding-top:28px; border-bottom:1px dotted #555;"></td></tr>
          <tr><td style="font-size:10px; font-style:italic; padding-top:3px;">Supervisor KLK3</td></tr>
        </table>

        <hr class="hr2">

        <div style="padding:8px 0; border-bottom:2px solid #000;">
          <div class="fr-title">Failure Reporting</div>
          <table style="width:100%; font-size:10px; margin-bottom:6px;">
            <tr><td style="width:62px; font-weight:bold; padding-bottom:6px;">Problems :</td><td style="border-bottom:1px dotted #555; padding-bottom:6px;"></td></tr>
            <tr><td style="font-weight:bold; padding-bottom:6px;">Cause :</td><td style="border-bottom:1px dotted #555; padding-bottom:6px;"></td></tr>
            <tr><td style="font-weight:bold;">Remedy :</td><td style="border-bottom:1px dotted #555;"></td></tr>
          </table>
        </div>

        <hr class="hr2" style="margin-top:0;">

        <div style="margin-top:10px;">
          <div style="font-weight:bold; font-size:10px; margin-bottom:10px;">Work Order Release</div>
          <div class="sig-lbl">Diminta Oleh</div>
          <table style="width:200px; margin-left:20px; margin-bottom:30px; text-align:center;">
            <tr><td style="padding-top:28px; border-bottom:1px dotted #555;"></td></tr>
            <tr><td style="font-size:10px; font-style:italic; padding-top:3px;">Supervisor Pemeliharaan</td></tr>
          </table>
          <div class="sig-lbl">Verifikasi</div>
          <table style="width:200px; margin-left:20px; margin-bottom:30px; text-align:center;">
            <tr><td style="padding-top:28px; border-bottom:1px dotted #555;"></td></tr>
            <tr><td style="font-size:10px; font-style:italic; padding-top:3px;">Supervisor Operasi</td></tr>
          </table>
        </div>
</div>

<!-- ========== PAGE 4 (JSA) ========== -->
<div class="page">
        <table style="width:100%; border-collapse:collapse; font-size:9.5px; margin-bottom:6px;">
          <tr>
            <td rowspan="4" style="border:1px solid #000; width:70px; text-align:center; padding:4px; vertical-align:middle;">
              <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 80 80"><rect width="80" height="80" fill="#FFD700"/><polygon points="40,12 55,35 48,35 55,68 25,45 33,45 26,12" fill="#003087"/></svg>
              <div style="font-size:7px; font-weight:bold; color:#003087; margin-top:2px;">PLN<br>Nusantara Power</div>
            </td>
            <td colspan="2" style="border:1px solid #000; text-align:center; font-weight:bold; font-size:11px; padding:3px;">PT PLN NUSANTARA POWER</td>
            <td style="border:1px solid #000; padding:2px 5px;"><strong>No Dokumen</strong> : FMZ 08.2.3.4</td>
          </tr>
          <tr>
            <td colspan="2" style="border:1px solid #000; text-align:center; font-weight:bold; padding:3px;">INTEGRATED MANAGEMENT SYSTEM</td>
            <td style="border:1px solid #000; padding:2px 5px;"><strong>Tgl Terbit</strong> : 02-02-2017</td>
          </tr>
          <tr>
            <td colspan="2" style="border:1px solid #000; text-align:center; font-weight:bold; font-size:11px; padding:3px;">FORM JOB SAFETY ANALYSIS</td>
            <td style="border:1px solid #000; padding:2px 5px;"><strong>Revisi</strong> : 1</td>
          </tr>
          <tr>
            <td colspan="2" style="border:1px solid #000; padding:2px 5px;"></td>
            <td style="border:1px solid #000; padding:2px 5px;"><strong>Halaman</strong> : Page 1 of 1</td>
          </tr>
          <tr><td colspan="2" style="border:1px solid #000; padding:3px 5px;"><strong>NAMA PEKERJAAN (Sesuai No WT)</strong></td><td colspan="2" style="border:1px solid #000; padding:3px 5px;">[{{ $wo['description'] ?? '-' }}]</td></tr>
          <tr><td colspan="2" style="border:1px solid #000; padding:3px 5px;"><strong>DASAR PEKERJAAN (WO, Task)</strong></td><td colspan="2" style="border:1px solid #000; padding:3px 5px;">{{ $wo['wonum'] ?? '-' }} @if(isset($tasks) && count($tasks) > 0) , {{ implode(', ', array_column($tasks, 'wonum')) }} @endif</td></tr>
          <tr><td colspan="2" style="border:1px solid #000; padding:3px 5px;"><strong>LOKASI</strong></td><td colspan="2" style="border:1px solid #000; padding:3px 5px;">{{ $wo['location_description'] ?? '-' }}</td></tr>
          <tr><td colspan="2" style="border:1px solid #000; padding:3px 5px;"><strong>PELAKSANA PEKERJAAN</strong></td><td colspan="2" style="border:1px solid #000; padding:3px 5px;">{{ $wo['persongroup'] ?? '-' }}</td></tr>
          <tr>
            <td colspan="2" style="border:1px solid #000; padding:3px 5px;"><strong>Tgl Hari Kerja</strong> : Tgl .................... sd Tgl ....................</td>
            <td colspan="2" style="border:1px solid #000; padding:3px 5px;"><strong>Waktu Kerja Per Hari</strong> : Pkl .................... sd Pkl ....................</td>
          </tr>
        </table>

        <div style="font-size:9.5px; margin:5px 0;">
          <strong>BERI TANDA</strong> &nbsp;&#9745;&nbsp; <strong>IJIN PEKERJAAN YANG HARUS DILENGKAPI</strong>
        </div>

        <table style="width:100%; font-size:9px; margin-bottom:6px;">
          <tr>
            <td><span class="chk"></span> HOT WORK</td>
            <td><span class="chk"></span> CONFINED SPACE</td>
            <td><span class="chk"></span> WORKING AT HEIGHT</td>
            <td><span class="chk"></span> ISOLASI</td>
          </tr>
          <tr>
            <td style="padding-top:4px;"><span class="chk"></span> DIGGING</td>
            <td style="padding-top:4px;"><span class="chk"></span> VICINITY</td>
            <td style="padding-top:4px;"><span class="chk"></span> NEAR &amp; UNDERWATER</td>
            <td style="padding-top:4px;"><span class="chk"></span> NOTHING</td>
          </tr>
        </table>

        @php
            $formatJsaTahapan = function ($html) {
                $text = str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $html);
                $text = strip_tags($text);
                $text = preg_replace("/[ \t]+/", ' ', $text);
                $lines = preg_split("/\r\n|\n/", trim($text));
                $out = [];
                foreach ($lines as $line) {
                    $trim = trim($line);
                    if ($trim === '') {
                        if (count($out) > 0 && end($out) !== '') { $out[] = ''; }
                        continue;
                    }
                    if (preg_match('/^\d+\.\s*$/u', $trim)) { continue; }
                    if (preg_match('/^[.\-–—\s]+$/u', $trim)) { continue; }
                    $out[] = $trim;
                }
                $text = implode("\n", $out);
                return preg_replace("/\n{3,}/", "\n\n", trim($text));
            };

            $jsaTahapanBlocks = [];
            if (isset($tasks) && count($tasks) > 0) {
                foreach ($tasks as $task) {
                    $block = '';
                    if (isset($task['longdescription']) && $task['longdescription'] != '-' && !empty(trim($task['longdescription']))) {
                        $formatted = $formatJsaTahapan($task['longdescription']);
                        if ($formatted !== '') { $block = $formatted; }
                    } elseif (isset($wo['longdescription']) && $wo['longdescription'] != '-' && !empty(trim($wo['longdescription']))) {
                        $formatted = $formatJsaTahapan($wo['longdescription']);
                        if ($formatted !== '') { $block = $formatted; }
                    } else {
                        $block = $task['description'] ?? '-';
                    }
                    $jsaTahapanBlocks[] = $block;
                }
            } else {
                $block = '';
                if (isset($wo['longdescription']) && $wo['longdescription'] != '-' && !empty(trim($wo['longdescription']))) {
                    $formatted = $formatJsaTahapan($wo['longdescription']);
                    if ($formatted !== '') { $block = $formatted; }
                } else {
                    $block = $wo['description'] ?? '-';
                }
                $jsaTahapanBlocks[] = $block;
            }
            $jsaTahapanText = implode("\n\n", $jsaTahapanBlocks);
            $jsaTahapanRows = [];
            foreach ($jsaTahapanBlocks as $block) {
                $parts = preg_split("/\n{2,}/", trim($block));
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part !== '') { $jsaTahapanRows[] = $part; }
                }
            }
            if (count($jsaTahapanRows) === 0 && trim($jsaTahapanText) !== '') {
                $jsaTahapanRows[] = $jsaTahapanText;
            }

            $jsaRiskLines = [];
            $jsaPrecLines = [];
            if (isset($hazards) && count($hazards) > 0) {
                $n = 0;
                foreach ($hazards as $hz) {
                    if (!empty($hz['precautions']) && count($hz['precautions']) > 0) {
                        foreach ($hz['precautions'] as $prec) {
                            $n++;
                            $precLabel = is_array($prec)
                                ? ($prec['description'] ?? ($prec['precautionid'] ?? '-'))
                                : $prec;
                            $jsaRiskLines[] = $n . '. ' . ($hz['description'] ?? '-');
                            $jsaPrecLines[] = $n . '. ' . $precLabel;
                        }
                    } else {
                        $n++;
                        $jsaRiskLines[] = $n . '. ' . ($hz['description'] ?? '-');
                        $jsaPrecLines[] = $n . '. -';
                    }
                }
            }
            $jsaRiskText  = implode("\n", $jsaRiskLines);
            $jsaPrecText  = implode("\n", $jsaPrecLines);
            $jsaHasHazards = count($jsaRiskLines) > 0;
        @endphp

        <table class="jsa-tbl">
          <colgroup>
            <col class="jsa-col-no">
            <col class="jsa-col-tahapan">
            <col class="jsa-col-risk">
            <col class="jsa-col-prec">
            <col class="jsa-col-pic">
          </colgroup>
          <thead>
            <tr>
              <th style="text-align:center;">No</th>
              <th>Tahapan Kerja</th>
              <th>Risk</th>
              <th>Pengendalian Bahaya (pre caution)</th>
              <th>PIC</th>
            </tr>
          </thead>
          <tbody>
            @foreach($jsaTahapanRows as $rowIndex => $jsaTahapanRow)
            <tr>
              <td style="text-align:center;">{{ $rowIndex === 0 ? '1' : '' }}</td>
              <td class="jsa-tahapan">{!! nl2br(e($jsaTahapanRow)) !!}</td>
              @if($rowIndex === 0 && $jsaHasHazards)
              <td class="jsa-risk-col">{!! nl2br(e($jsaRiskText)) !!}</td>
              <td class="jsa-prec-col">{!! nl2br(e($jsaPrecText)) !!}</td>
              <td>&nbsp;</td>
              @elseif($rowIndex === 0)
              <td class="jsa-empty">-</td>
              <td class="jsa-empty" style="font-style:italic;">Tidak ada data Precaution &amp; Hazard</td>
              <td>&nbsp;</td>
              @else
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              @endif
            </tr>
            @endforeach
          </tbody>
        </table>
</div>

<!-- ========== PAGE 5 ========== -->
<div class="page">
  @include('admin.maximo.partials.jobcard-pdf-header', ['pageNumber' => 5])

        <table style="width:100%; margin-bottom:8px;">
          <tr>
            <td style="vertical-align:top; padding-right:10px;">
              <table class="w-tbl">
                <tr><th>Nama Pekerja</th><th>Skill / Posisi</th><th>Peralatan Kerja yang Digunakan</th></tr>
                <tr><td>1.</td><td>1.</td><td>1.</td></tr>
                <tr><td>2.</td><td>2.</td><td>2.</td></tr>
                <tr><td>3.</td><td>3.</td><td>3.</td></tr>
                <tr><td>4.</td><td>4.</td><td>4.</td></tr>
                <tr><td>5.</td><td>5.</td><td>5.</td></tr>
                <tr><td>6.</td><td>6.</td><td>6.</td></tr>
              </table>
            </td>
            <td style="width:220px; vertical-align:top;">
              <div style="font-size:9px; line-height:1.7; border:1px solid #000; padding:5px;">
                <div style="text-decoration:underline; font-weight:bold; margin-bottom:2px;">Keterangan :</div>
                <div>1. DILARANG merokok di area kerja.</div>
                <div>2. Jagalah kebersihan lingkungan di area kerja.</div>
                <div>3. Jika ada kondisi bahaya/ tidak aman di sekitar, segera lapor kepada pengawas area.</div>
                <div>4. Dilarang berjalan/ memasuki area yang tidak tercantum dalam permit tanpa seizin dari pengawas area.</div>
                <div>5. Dilarang menyentuh peralatan dan tombol emergency di sekitar area tanpa izin.</div>
              </div>
            </td>
          </tr>
        </table>

        <table style="width:100%;">
          <tr>
            <td style="vertical-align:top; border:1px solid #000; padding:4px; font-size:9.5px; height:70px;">
              <div style="font-weight:bold; border-bottom:1px solid #000; padding-bottom:2px; margin-bottom:4px;">Catatan Lain :</div>
            </td>
            <td style="width:10px;"></td>
            <td style="width:155px; vertical-align:top;">
              <table style="width:100%; border-collapse:collapse; font-size:9.5px;">
                <tr><td style="border:1px solid #000; padding:3px 5px; font-weight:bold;">Prepared By :</td><td style="border:1px solid #000; padding:3px 5px; font-weight:bold;">Sign :</td></tr>
                <tr><td style="border:1px solid #000; padding:3px 5px; height:30px;"></td><td style="border:1px solid #000; padding:3px 5px; height:30px;"></td></tr>
                <tr><td colspan="2" style="border:1px solid #000; padding:2px 5px; background:#ddd; font-weight:bold; text-align:center; font-size:9px;">Created :</td></tr>
                <tr><td style="border:1px solid #000; padding:3px 5px; font-size:8.5px;">Tanggal :</td><td style="border:1px solid #000; padding:3px 5px; font-size:8.5px;">Waktu</td></tr>
                <tr><td style="border:1px solid #000; height:20px;"></td><td style="border:1px solid #000; height:20px;"></td></tr>
              </table>
            </td>
          </tr>
        </table>
</div>

<!-- ========== PAGE 6 ========== -->
<div class="page" style="page-break-after:auto;">
  @include('admin.maximo.partials.jobcard-pdf-header', ['pageNumber' => 6])
  &nbsp;
</div>

</body>
</html>
