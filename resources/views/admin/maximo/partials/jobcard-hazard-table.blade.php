@if(isset($hazards) && count($hazards) > 0)
<style>
.hazard-table { width:100%; border-collapse:collapse; font-size:10px; margin-top:15px; margin-bottom:10px; table-layout:fixed; }
.hazard-table thead { display:table-header-group; }
.hazard-table tr { page-break-inside:auto; page-break-after:auto; }
.hazard-table th, .hazard-table td { padding:4px 6px; vertical-align:top; text-align:left; }
.hazard-title-row th { background:#8EA9DB; color:#fff; font-weight:bold; text-align:left; border:none; padding:4px 10px; }
.hazard-col-row th { background:#fff; color:#000; font-weight:bold; border-top:1px dotted #000; border-bottom:1.5px solid #000; text-align:center; }
.hazard-col-row th:first-child { text-align:center; }
.hazard-col-row th:nth-child(2), .hazard-col-row th:nth-child(3) { text-align:left; }
.hazard-table tbody td { border-bottom:1px dotted #000; }
.hazard-table tbody tr:last-child td { border-bottom:1.5px solid #000; }
</style>
<table class="hazard-table">
  <thead>
    <tr class="hazard-title-row"><th colspan="3">Precaution &amp; Hazard</th></tr>
    <tr class="hazard-col-row">
      <th style="width:8%;">No.</th>
      <th style="width:42%;">Hazard</th>
      <th style="width:50%;">Precaution</th>
    </tr>
  </thead>
  <tbody>
    @php $rowNum = 1; @endphp
    @foreach($hazards as $hz)
      @if(!empty($hz['precautions']) && count($hz['precautions']) > 0)
        @foreach($hz['precautions'] as $prec)
          <tr>
            <td style="text-align:center;">{{ $rowNum++ }}</td>
            <td>{{ $hz['description'] ?? '-' }}</td>
            <td>
              @if(is_array($prec))
                {{ $prec['description'] ?? ($prec['precautionid'] ?? '-') }}
              @else
                {{ $prec }}
              @endif
            </td>
          </tr>
        @endforeach
      @else
        <tr>
          <td style="text-align:center;">{{ $rowNum++ }}</td>
          <td>{{ $hz['description'] ?? '-' }}</td>
          <td>-</td>
        </tr>
      @endif
    @endforeach
  </tbody>
</table>
@endif
