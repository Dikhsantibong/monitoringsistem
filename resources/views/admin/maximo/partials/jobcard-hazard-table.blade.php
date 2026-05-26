@if(isset($hazards) && count($hazards) > 0)
<table class="report-table" style="margin-top:15px; margin-bottom:10px;">
  <thead>
    <tr class="report-title-row"><th colspan="3">Precaution &amp; Hazard</th></tr>
    <tr class="report-col-row">
      <th style="width:8%; text-align:center;">No.</th>
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
