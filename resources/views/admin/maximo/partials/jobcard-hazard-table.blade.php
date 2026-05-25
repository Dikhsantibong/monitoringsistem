@if(isset($hazards) && count($hazards) > 0)
<table class="blue-table" style="margin-top:15px;">
  <tr><th colspan="3">Precaution &amp; Hazard</th></tr>
  <tr style="background:#4472C4; color:#fff;">
    <th style="width:30px;">No.</th>
    <th>Hazard</th>
    <th>Precaution</th>
  </tr>
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
</table>
@endif
