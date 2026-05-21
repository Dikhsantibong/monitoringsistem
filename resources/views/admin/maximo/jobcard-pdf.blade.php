<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Card - {{ $wo['wonum'] ?? '-' }} - PLN Nusantara Power</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
@page { 
    margin: 0; 
    size: A4; 
}
body { 
    font-family: Arial, sans-serif; 
    font-size:11px; 
    color:#000; 
    background:#fff; 
}
.page { 
    width: 180mm; /* 210mm - 15mm(left) - 15mm(right) */
    min-height: 277mm; /* 297mm - 10mm(top) - 10mm(bottom) */
    margin: 10mm 15mm; /* top/bottom 10mm, left/right 15mm */
    position: relative; 
    page-break-after: always; 
}


/* HEADER */
.hdr-table { width:100%; border-bottom:2px solid #000; padding-bottom:6px; margin-bottom:8px; }
.logo-txt { font-size:9px; color:#003087; font-weight:bold; line-height:1.4; padding-left:8px; }
.co { font-size:14px; font-weight:bold; }
.unit { font-size:11px; }

/* JOB CARD TITLE */
.jc-title { font-size:16px; font-weight:bold; margin:6px 0 6px 0; }

/* HR dividers */
.hr1 { border:none; border-top:1px solid #000; margin:5px 0; }
.hr2 { border:none; border-top:2px solid #000; margin:5px 0; }

/* WO info row */
.bold { font-weight:bold; }
.f10 { font-size:10px; }
.f9 { font-size:9.5px; }

/* SERVICE REQUEST */
.sr-section { margin-bottom:4px; }
.detil-section { font-size:10px; line-height:1.65; margin-bottom:4px; }
.detil-section p { margin-bottom:1px; }

/* TASK SECTION */
.task-section { margin-top:6px; }
.task-id { font-weight:bold; font-size:12px; margin:5px 0 5px 0; }
.task-nm { font-size:13px; font-weight:bold; font-style:italic; margin-top:3px; }

/* PAGE 2 STEPS */
.step-t { font-weight:bold; margin:6px 0 2px; font-size:10px; }
ol { margin-left:16px; font-size:10px; line-height:1.7; }

/* TABLES */
.blue-table { width:100%; border-collapse:collapse; font-size:10px; margin:6px 0; }
.blue-table th { background:#4472C4; color:#fff; padding:4px 6px; text-align:left; }
.blue-table td { padding:3px 6px; border-bottom:1px dotted #bbb; }

/* SIGNATURE */
.sig-lbl { font-size:10px; margin-bottom:5px; }

/* FAILURE REPORTING */
.fr-title { font-weight:bold; font-size:10px; margin-bottom:6px; }

/* JSA */
.jsa-tbl { width:100%; border-collapse:collapse; font-size:9.5px; }
.jsa-tbl th, .jsa-tbl td { border:1px solid #000; padding:3px 5px; vertical-align:top; }
.jsa-tbl th { background:#000; color:#fff; }
.chk { width:12px; height:12px; border:1px solid #000; display:inline-block; }

/* Worker table */
.w-tbl { width:100%; border-collapse:collapse; font-size:9.5px; }
.w-tbl th, .w-tbl td { border:1px solid #000; padding:3px 5px; }
.w-tbl th { background:#ddd; font-weight:bold; }
.w-tbl td { height:20px; }
</style>
</head>
<body>

<!-- ========== PAGE 1 ========== -->
<div class="page">
  <table class="hdr-table">
    <tr>
      <td style="width:30%; vertical-align:middle;">
        <table style="border-collapse:collapse;">
          <tr>
            <td><img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="width: 75px; height: auto;"></td>
          </tr>
        </table>
      </td>
      <td style="width:40%; text-align:center; vertical-align:middle;">
        <div class="co">PLN Nusantara Power</div>
        <div class="unit">Unit Pembangkitan Kendari</div>
      </td>
      <td style="width:30%; text-align:right; vertical-align:middle; font-size:10px;">
        Halaman : 1
      </td>
    </tr>
  </table>

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

  @if($sr)
  <!-- SERVICE REQUEST -->
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
  <!-- DETIL SR -->
  <div class="detil-section" style="margin-top:6px;">
    <div class="bold" style="margin-bottom:3px;">Detil SR</div>
    {!! nl2br(e(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $sr['longdescription'])))) !!}
  </div>
  @endif
  @endif

  <!-- TASK SECTION -->
  @if(isset($tasks) && count($tasks) > 0)
    @foreach($tasks as $task)
    <div class="task-section">
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
      
      @if(isset($task['longdescription']) && $task['longdescription'] != '-' && !empty(trim($task['longdescription'])))
      <div class="task-ld" style="margin-top: 8px; font-size: 9.5px; padding-left: 15px;">
        {!! nl2br(e(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $task['longdescription'])))) !!}
      </div>
      @elseif(isset($wo['longdescription']) && $wo['longdescription'] != '-' && !empty(trim($wo['longdescription'])))
      <div class="task-ld" style="margin-top: 8px; font-size: 9.5px; padding-left: 15px;">
        {!! nl2br(e(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $wo['longdescription'])))) !!}
      </div>
      @endif
    </div>
    @endforeach
  @endif

  <!-- Planned & Actual Labor (Moved to Page 1) -->
  <table class="blue-table" style="margin-top:15px;">
    <tr><th colspan="8">Planned &amp; Actual Labor</th></tr>
    <tr style="background:#4472C4; color:#fff;">
      <th>Task ID</th><th>Craft</th><th>Skill Level</th><th>Labor</th>
      <th>Planned Quantity</th><th>Planned Hours</th><th>Actual Quantity</th><th>Actual Hours</th>
    </tr>
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
  </table>

  <div style="position:absolute; bottom:0; left:0; font-size:10px;">Halaman : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1</div>
</div>

<!-- ========== PAGE 2 ========== -->
<div class="page">
  <table class="hdr-table">
    <tr>
      <td style="width:30%; vertical-align:middle;">
        <table style="border-collapse:collapse;">
          <tr>
            <td><img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="width: 75px; height: auto;"></td>
          </tr>
        </table>
      </td>
      <td style="width:40%; text-align:center; vertical-align:middle;">
        <div class="co">PLN Nusantara Power</div>
        <div class="unit">Unit Pembangkitan Kendari</div>
      </td>
      <td style="width:30%; text-align:right; vertical-align:middle; font-size:10px;">
        Halaman : 2
      </td>
    </tr>
  </table>

  <!-- REALISASI PEKERJAAN Blank Lines (Optional, keeping as manual fill area if needed) -->
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

  <!-- Precaution & Hazard -->
  <table class="blue-table">
    <tr><th colspan="3">Precaution &amp; Hazard</th></tr>
    <tr style="background:#4472C4; color:#fff;"><th style="width:30px;">No.</th><th>Hazard</th><th>Precaution</th></tr>
    <tr><td>1</td><td>GENERAL</td><td>Safety Gloves</td></tr>
    <tr><td>2</td><td>GENERAL</td><td>Safety helmet</td></tr>
    <tr><td>3</td><td>GENERAL</td><td>PPE - Safety Earplug</td></tr>
    <tr><td>4</td><td>GENERAL</td><td>PPE - Safety Gloves</td></tr>
    <tr><td>5</td><td>GENERAL</td><td>PPE - Safety Google</td></tr>
    <tr><td>6</td><td>GENERAL</td><td>PPE - Safety Helmet</td></tr>
    <tr><td>7</td><td>GENERAL</td><td>PPE - Masker</td></tr>
    <tr><td>8</td><td>GENERAL</td><td>PPE - Safety Shoes</td></tr>
  </table>

  <hr class="hr1" style="margin-top:10px;">
  <div style="font-weight:bold; font-style:italic; font-size:10px; margin-top:5px;"><em>Isolasi dan Perhatian Keselamatan Kerja</em></div>
  <div style="border-bottom:1px dotted #888; margin-top:18px;"></div>
  <div style="border-bottom:1px dotted #888; margin-top:18px;"></div>

  <div style="position:absolute; bottom:0; left:0; font-size:10px;">Halaman : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2</div>
</div>

<!-- ========== PAGE 3 ========== -->
<div class="page">
  <table class="hdr-table">
    <tr>
      <td style="width:30%; vertical-align:middle;">
        <table style="border-collapse:collapse;">
          <tr>
            <td><img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="width: 75px; height: auto;"></td>
          </tr>
        </table>
      </td>
      <td style="width:40%; text-align:center; vertical-align:middle;">
        <div class="co">PLN Nusantara Power</div>
        <div class="unit">Unit Pembangkitan Kendari</div>
      </td>
      <td style="width:30%; text-align:right; vertical-align:middle; font-size:10px;">
        Halaman : 3
      </td>
    </tr>
  </table>

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

  <div style="position:absolute; bottom:0; left:0; font-size:10px;">Halaman : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3</div>
</div>

<!-- ========== PAGE 4 (JSA) ========== -->
<div class="page">
  <table class="hdr-table">
    <tr>
      <td style="width:30%; vertical-align:middle;">
        <table style="border-collapse:collapse;">
          <tr>
            <td><img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="width: 75px; height: auto;"></td>
          </tr>
        </table>
      </td>
      <td style="width:40%; text-align:center; vertical-align:middle;">
        <div class="co">PLN Nusantara Power</div>
        <div class="unit">Unit Pembangkitan Kendari</div>
      </td>
      <td style="width:30%; text-align:right; vertical-align:middle; font-size:10px;">
        Halaman : 4
      </td>
    </tr>
  </table>

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

  <table class="jsa-tbl">
    <tr><th style="width:28px;">No</th><th>Tahapan Kerja</th><th style="width:58px;">Risk</th><th>Pengendalian Bahaya (pre caution)</th><th style="width:38px;">PIC</th></tr>
    <tr>
      <td style="text-align:center;">1</td>
      <td style="font-size:8.5px; line-height:1.6;">
        @php $hasTaskDesc = false; @endphp
        @if(isset($tasks) && count($tasks) > 0)
          @foreach($tasks as $task)
            @if(isset($task['longdescription']) && $task['longdescription'] != '-' && !empty(trim($task['longdescription'])))
              @php $hasTaskDesc = true; @endphp
              <strong>Task : {{ $task['description'] ?? '-' }}</strong><br>
              {!! nl2br(e(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $task['longdescription'])))) !!}<br><br>
            @endif
          @endforeach
        @endif
        
        @if(!$hasTaskDesc && isset($wo['longdescription']) && $wo['longdescription'] != '-' && !empty(trim($wo['longdescription'])))
          <strong>Task : {{ $wo['description'] ?? '-' }}</strong><br>
          {!! nl2br(e(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $wo['longdescription'])))) !!}<br><br>
        @endif
      </td>
      <td style="text-align:center; font-weight:bold; font-size:9px;">1<br>GENERAL</td>
      <td style="font-size:8.5px; line-height:1.9;">
        1 &nbsp;<strong>APDGLVS: Safety Gloves</strong><br>
        2 &nbsp;<strong>APDHLMT: Safety helmet</strong><br>
        3 &nbsp;<strong>EARPLUG: PPE - Safety Earplug</strong><br>
        4 &nbsp;<strong>GLOVES: PPE - Safety Gloves</strong><br>
        5 &nbsp;<strong>GOOGLE: PPE - Safety Google</strong><br>
        6 &nbsp;<strong>HELMET: PPE - Safety Helmet</strong><br>
        7 &nbsp;<strong>MASKER: PPE - Masker</strong><br>
        8 &nbsp;<strong>SHOES: PPE - Safety Shoes</strong>
      </td>
      <td></td>
    </tr>
    <tr><td style="height:18px;"></td><td></td><td></td><td></td><td></td></tr>
  </table>

  <div style="position:absolute; bottom:0; left:0; font-size:10px;">Halaman : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 4</div>
</div>

<!-- ========== PAGE 5 ========== -->
<div class="page">
  <table class="hdr-table">
    <tr>
      <td style="width:30%; vertical-align:middle;">
        <table style="border-collapse:collapse;">
          <tr>
            <td><img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="width: 75px; height: auto;"></td>
          </tr>
        </table>
      </td>
      <td style="width:40%; text-align:center; vertical-align:middle;">
        <div class="co">PLN Nusantara Power</div>
        <div class="unit">Unit Pembangkitan Kendari</div>
      </td>
      <td style="width:30%; text-align:right; vertical-align:middle; font-size:10px;">
        Halaman : 5
      </td>
    </tr>
  </table>

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
        <div style="font-weight:bold; border-bottom:1px solid #000; padding-bottom:2px; margin-bottom:4px;">CatatanLain :</div>
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

  <div style="position:absolute; bottom:0; left:0; font-size:10px;">Halaman : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 5</div>
</div>

<!-- ========== PAGE 6 ========== -->
<div class="page" style="page-break-after: auto;">
  <table class="hdr-table">
    <tr>
      <td style="width:30%; vertical-align:middle;">
        <table style="border-collapse:collapse;">
          <tr>
            <td><img src="{{ public_path('logo/navlog1.png') }}" alt="PLN Logo" style="width: 75px; height: auto;"></td>
          </tr>
        </table>
      </td>
      <td style="width:40%; text-align:center; vertical-align:middle;">
        <div class="co">PLN Nusantara Power</div>
        <div class="unit">Unit Pembangkitan Kendari</div>
      </td>
      <td style="width:30%; text-align:right; vertical-align:middle; font-size:10px;">
        Halaman : 6
      </td>
    </tr>
  </table>
  <div style="position:absolute; bottom:0; left:0; font-size:10px;">Halaman : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 6</div>
</div>

</body>
</html>